<?php
require_once __DIR__ . '/../includes/functions.php';
requireNgo();
require_once __DIR__ . '/../Database/db_config.php';

ob_start();

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$ngo_id = 0;
$errorMsg = '';
$contribData = null;
$contributionItems = [];

// Flash messages
$flashMsg = $_SESSION['ngo_donation_msg'] ?? '';
$flashType = $_SESSION['ngo_donation_type'] ?? '';
unset($_SESSION['ngo_donation_msg'], $_SESSION['ngo_donation_type']);

// Resolve NGO ID
if ($user_id > 0) {
    $ngoRes = mysqli_query($con, "SELECT id, name FROM ngos WHERE user_id = $user_id LIMIT 1");
    if ($ngoRes && mysqli_num_rows($ngoRes) > 0) {
        $ngoRow = mysqli_fetch_assoc($ngoRes);
        $ngo_id = (int)$ngoRow['id'];
    } else {
        $errorMsg = 'NGO profile not found for this account.';
    }
} else {
    $errorMsg = 'Please login to access donation details.';
}

$contribId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Resource Fulfillment POST Actions
if ($ngo_id > 0 && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    $action = trim($_POST['action']);
    $targetId = isset($_POST['contribution_id']) ? (int)$_POST['contribution_id'] : $contribId;

    if ($targetId > 0) {
        $chkQuery = "SELECT c.*, n.name AS ngo_name 
                     FROM contributions c 
                     INNER JOIN ngos n ON n.id = c.ngo_id 
                     WHERE c.id = ? AND c.ngo_id = ? 
                     LIMIT 1";
        $stmt = mysqli_prepare($con, $chkQuery);
        mysqli_stmt_bind_param($stmt, "ii", $targetId, $ngo_id);
        mysqli_stmt_execute($stmt);
        $chkRes = mysqli_stmt_get_result($stmt);

        if ($chkRes && mysqli_num_rows($chkRes) > 0) {
            $cRow = mysqli_fetch_assoc($chkRes);
            $donorUserId = !empty($cRow['user_id']) ? (int)$cRow['user_id'] : 0;
            $cType = $cRow['contribution_type'];

            if ($cType === 'money') {
                $_SESSION['ngo_donation_msg'] = "Payment status cannot be modified manually. Monetary donations are reconciled automatically.";
                $_SESSION['ngo_donation_type'] = 'error';
            } elseif ($action === 'mark_received') {
                if ($cRow['fulfillment_status'] === 'pending') {
                    mysqli_begin_transaction($con);
                    $upStmt = mysqli_prepare($con, "UPDATE contributions SET fulfillment_status = 'received', updated_at = NOW() WHERE id = ?");
                    mysqli_stmt_bind_param($upStmt, "i", $targetId);
                    $upOk = mysqli_stmt_execute($upStmt);
                    mysqli_stmt_close($upStmt);

                    if ($upOk) {
                        $awarded = 0;
                        if ($donorUserId > 0) {
                            $pts = 50; // 50 Karma points for resource donation on NGO verification
                            $tx = awardKarmaPoints($donorUserId, $pts, 'donation_resource', $targetId, "Karma points for " . ucfirst($cType) . " donation to " . ($cRow['ngo_name'] ?? 'NGO'));
                            if ($tx) {
                                $awarded = $pts;
                            }
                        }
                        mysqli_commit($con);
                        $_SESSION['ngo_donation_msg'] = ucfirst($cType) . " contribution marked as received!" . ($awarded > 0 ? " Awarded $awarded Karma points to contributor." : "");
                        $_SESSION['ngo_donation_type'] = 'success';
                    } else {
                        mysqli_rollback($con);
                        $_SESSION['ngo_donation_msg'] = "Failed to update contribution status.";
                        $_SESSION['ngo_donation_type'] = 'error';
                    }
                } else {
                    $_SESSION['ngo_donation_msg'] = "Invalid status transition. Only pending contributions can be marked as received.";
                    $_SESSION['ngo_donation_type'] = 'error';
                }
            } elseif ($action === 'mark_completed' || $action === 'verify_resource') {
                if (in_array($cRow['fulfillment_status'], ['pending', 'received'], true)) {
                    mysqli_begin_transaction($con);
                    $upStmt = mysqli_prepare($con, "UPDATE contributions SET fulfillment_status = 'completed', updated_at = NOW() WHERE id = ?");
                    mysqli_stmt_bind_param($upStmt, "i", $targetId);
                    $upOk = mysqli_stmt_execute($upStmt);
                    mysqli_stmt_close($upStmt);

                    if ($upOk) {
                        $awarded = 0;
                        if ($donorUserId > 0) {
                            $pts = 50; // Duplicate awards are automatically prevented by awardKarmaPoints() helper
                            $tx = awardKarmaPoints($donorUserId, $pts, 'donation_resource', $targetId, "Karma points for " . ucfirst($cType) . " donation to " . ($cRow['ngo_name'] ?? 'NGO'));
                            if ($tx) {
                                $awarded = $pts;
                            }
                        }
                        mysqli_commit($con);
                        $_SESSION['ngo_donation_msg'] = ucfirst($cType) . " contribution marked as completed!" . ($awarded > 0 ? " Awarded $awarded Karma points to contributor." : "");
                        $_SESSION['ngo_donation_type'] = 'success';
                    } else {
                        mysqli_rollback($con);
                        $_SESSION['ngo_donation_msg'] = "Failed to update contribution status.";
                        $_SESSION['ngo_donation_type'] = 'error';
                    }
                } else {
                    $_SESSION['ngo_donation_msg'] = "Invalid status transition. Completed or cancelled contributions cannot be modified.";
                    $_SESSION['ngo_donation_type'] = 'error';
                }
            } elseif ($action === 'cancel_resource' || $action === 'cancel_contribution') {
                if ($cRow['fulfillment_status'] !== 'cancelled') {
                    mysqli_begin_transaction($con);
                    $upStmt = mysqli_prepare($con, "UPDATE contributions SET fulfillment_status = 'cancelled', updated_at = NOW() WHERE id = ?");
                    mysqli_stmt_bind_param($upStmt, "i", $targetId);
                    $upOk = mysqli_stmt_execute($upStmt);
                    mysqli_stmt_close($upStmt);

                    if ($upOk) {
                        $reversed = false;
                        if ($donorUserId > 0) {
                            $revId = reverseKarmaPoints($donorUserId, 'donation_resource', $targetId, "Points reversed due to cancellation of contribution #" . $cRow['contribution_reference']);
                            if ($revId) {
                                $reversed = true;
                            }
                        }
                        mysqli_commit($con);
                        $_SESSION['ngo_donation_msg'] = "Contribution has been cancelled." . ($reversed ? " Awarded Karma points have been reversed." : "");
                        $_SESSION['ngo_donation_type'] = 'success';
                    } else {
                        mysqli_rollback($con);
                        $_SESSION['ngo_donation_msg'] = "Failed to cancel contribution.";
                        $_SESSION['ngo_donation_type'] = 'error';
                    }
                } else {
                    $_SESSION['ngo_donation_msg'] = "Contribution is already cancelled.";
                    $_SESSION['ngo_donation_type'] = 'error';
                }
            } else {
                $_SESSION['ngo_donation_msg'] = "Unrecognized action requested.";
                $_SESSION['ngo_donation_type'] = 'error';
            }
        } else {
            $_SESSION['ngo_donation_msg'] = "Contribution not found or access denied.";
            $_SESSION['ngo_donation_type'] = 'error';
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: " . baseUrl("ngo/donation-details.php?id=" . $targetId));
    exit;
}

// Fetch Contribution Record
if ($ngo_id > 0) {
    if ($contribId <= 0) {
        $errorMsg = 'Invalid contribution ID specified.';
    } else {
        $sql = "SELECT c.*,
                       n.name AS ngo_name,
                       ca.title AS cause_title,
                       ca.category AS cause_category,
                       u.name AS user_registered_name,
                       u.email AS user_registered_email,
                       u.phone AS user_registered_phone
                FROM contributions c
                INNER JOIN ngos n ON n.id = c.ngo_id
                LEFT JOIN causes ca ON ca.id = c.cause_id
                LEFT JOIN users u ON u.id = c.user_id
                WHERE c.id = ? AND c.ngo_id = ?
                LIMIT 1";

        $stmt = mysqli_prepare($con, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $contribId, $ngo_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && mysqli_num_rows($res) > 0) {
                $contribData = mysqli_fetch_assoc($res);
            } else {
                $errorMsg = 'Contribution not found or does not belong to your organization.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $errorMsg = 'Database error while querying contribution.';
        }

        // Fetch contribution items if resource contribution
        if ($contribData && $contribData['contribution_type'] !== 'money') {
            $itemSql = "SELECT * FROM contribution_items WHERE contribution_id = ? ORDER BY id ASC";
            $itemStmt = mysqli_prepare($con, $itemSql);
            if ($itemStmt) {
                mysqli_stmt_bind_param($itemStmt, "i", $contribId);
                mysqli_stmt_execute($itemStmt);
                $itemRes = mysqli_stmt_get_result($itemStmt);
                if ($itemRes) {
                    while ($iRow = mysqli_fetch_assoc($itemRes)) {
                        $contributionItems[] = $iRow;
                    }
                }
                mysqli_stmt_close($itemStmt);
            }
        }
    }
}
?>

<div class="min-h-screen bg-ivory py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        <!-- NAVIGATION BREADCRUMB & BACK BUTTON -->
        <div class="mb-6 flex items-center justify-between">
            <a href="<?= baseUrl('ngo/donations.php') ?>" class="inline-flex items-center text-sm font-bold text-forest hover:text-forest-dark transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Contributions
            </a>
            <?php if ($contribData): ?>
                <span class="text-xs font-mono text-charcoal-light uppercase tracking-wider">
                    Ref: <?= e($contribData['contribution_reference']) ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($flashMsg)): ?>
            <div class="mb-6 p-4 rounded-xl <?= $flashType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                <p class="text-sm font-medium"><?= e($flashMsg) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg) || !$contribData): ?>
            <!-- ERROR STATE -->
            <div class="bg-white rounded-3xl p-10 text-center border border-gold/20 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-50 text-red-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-serif font-bold text-forest mb-2">Contribution Not Found</h2>
                <p class="text-charcoal-light max-w-md mx-auto mb-6">
                    <?= e($errorMsg ?: 'The requested contribution could not be retrieved.') ?>
                </p>
                <a href="<?= baseUrl('ngo/donations.php') ?>" class="inline-flex items-center px-6 py-2.5 bg-forest text-white rounded-xl font-bold hover:bg-forest-dark transition-colors shadow-sm">
                    Return to Contributions List
                </a>
            </div>
        <?php else: ?>

            <?php
            $type = $contribData['contribution_type'];
            $typeLabel = ($type === 'items') ? 'Useful Items' : ucfirst($type);
            $isMoney = ($type === 'money');

            // Donor name resolution
            $donorName = !empty($contribData['donor_name']) ? $contribData['donor_name'] : 'Anonymous Contributor';
            $isAnon = ((int)$contribData['is_anonymous'] === 1);

            // Amount / Quantity display
            if ($isMoney) {
                $primaryDisplay = '₹' . number_format((float)$contribData['monetary_amount'], 2);
                $metricLabel = 'Donation Amount';
            } else {
                $totalQty = 0;
                $units = [];
                foreach ($contributionItems as $it) {
                    $totalQty += (int)$it['quantity'];
                    if (!empty($it['unit'])) $units[] = $it['unit'];
                }
                $primaryDisplay = number_format($totalQty) . ' ' . (!empty($units) ? htmlspecialchars($units[0]) : 'items');
                $metricLabel = 'Pledged Quantity';
            }

            // Status badges
            $fulfillmentStatus = $contribData['fulfillment_status'];
            $paymentStatus = $contribData['payment_status'];

            $statusBadgeClass = 'bg-gray-100 text-gray-700';
            if ($fulfillmentStatus === 'completed') {
                $statusBadgeClass = 'bg-green-50 text-forest border border-green-200';
            } elseif ($fulfillmentStatus === 'received') {
                $statusBadgeClass = 'bg-blue-50 text-blue-700 border border-blue-200';
            } elseif ($fulfillmentStatus === 'pending') {
                $statusBadgeClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
            } elseif ($fulfillmentStatus === 'cancelled') {
                $statusBadgeClass = 'bg-red-50 text-red-700 border border-red-200';
            }
            ?>

            <!-- HERO SUMMARY CARD -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20 mb-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-charcoal/10 pb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span class="px-3 py-1 bg-forest/10 text-forest font-bold text-xs rounded-full uppercase tracking-wider">
                                <?= e($typeLabel) ?>
                            </span>
                            <span class="px-3 py-1 <?= $statusBadgeClass ?> font-bold text-xs rounded-full uppercase tracking-wider">
                                <?= e(ucfirst($fulfillmentStatus)) ?>
                            </span>
                            <?php if ($isMoney): ?>
                                <span class="px-3 py-1 <?= $paymentStatus === 'completed' ? 'bg-green-50 text-green-700 border border-green-200' : ($paymentStatus === 'pending' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-red-50 text-red-700 border border-red-200') ?> font-bold text-xs rounded-full uppercase tracking-wider">
                                    Payment: <?= e(ucfirst($paymentStatus)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-serif font-bold text-forest">
                            <?= e($donorName) ?>
                        </h1>
                        <p class="text-xs text-charcoal-light mt-1">
                            Pledged on <?= date('d F Y, h:i A', strtotime($contribData['created_at'])) ?>
                        </p>
                    </div>

                    <div class="text-left md:text-right">
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                            <?= $metricLabel ?>
                        </p>
                        <p class="text-3xl md:text-4xl font-serif font-bold text-forest">
                            <?= $primaryDisplay ?>
                        </p>
                    </div>
                </div>

                <!-- OVERVIEW METRICS GRID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pt-6 text-sm">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Contribution Ref</p>
                        <p class="font-mono font-medium text-charcoal"><?= e($contribData['contribution_reference']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Associated Cause</p>
                        <p class="font-medium text-charcoal"><?= !empty($contribData['cause_title']) ? e($contribData['cause_title']) : 'General Support' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Donor Email</p>
                        <p class="font-medium text-charcoal"><?= !empty($contribData['donor_email']) ? e($contribData['donor_email']) : 'Not Provided' ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Karma Points Awarded</p>
                        <p class="font-bold text-forest">
                            <?= (int)$contribData['karma_points_awarded'] > 0 ? (int)$contribData['karma_points_awarded'] . ' Points' : '0 (Pending)' ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID: DETAILS & FULFILLMENT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT COLUMN: 2 COLS FOR DETAILS -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- DONOR INFORMATION CARD -->
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20">
                        <h2 class="text-lg font-serif font-bold text-forest mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Donor Information
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="bg-ivory/50 p-4 rounded-2xl border border-charcoal/5">
                                <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Donor Name</p>
                                <p class="font-bold text-charcoal"><?= e($donorName) ?></p>
                                <?php if ($isAnon): ?>
                                    <span class="inline-block mt-1 text-[11px] text-gold font-bold uppercase tracking-wider">
                                        (Requested Anonymous on Public Lists)
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="bg-ivory/50 p-4 rounded-2xl border border-charcoal/5">
                                <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">User Account</p>
                                <p class="font-medium text-charcoal">
                                    <?php if (!empty($contribData['user_registered_name'])): ?>
                                        Registered Member (<?= e($contribData['user_registered_name']) ?>)
                                    <?php else: ?>
                                        Guest / Non-registered Contributor
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="bg-ivory/50 p-4 rounded-2xl border border-charcoal/5">
                                <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Email Address</p>
                                <p class="font-medium text-charcoal"><?= !empty($contribData['donor_email']) ? e($contribData['donor_email']) : '—' ?></p>
                            </div>

                            <div class="bg-ivory/50 p-4 rounded-2xl border border-charcoal/5">
                                <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Phone Number</p>
                                <p class="font-medium text-charcoal"><?= !empty($contribData['donor_phone']) ? e($contribData['donor_phone']) : '—' ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- RESOURCE ITEMS TABLE (Food, Clothes, Items) -->
                    <?php if (!$isMoney): ?>
                        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20">
                            <h2 class="text-lg font-serif font-bold text-forest mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Pledged Resource Items
                            </h2>

                            <?php if (count($contributionItems) > 0): ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-sm">
                                        <thead>
                                            <tr class="border-b border-charcoal/10 text-charcoal-light uppercase text-xs tracking-wider">
                                                <th class="pb-3 pl-2">Item Description</th>
                                                <th class="pb-3">Quantity</th>
                                                <th class="pb-3">Condition</th>
                                                <th class="pb-3">Delivery Method</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-charcoal/5">
                                            <?php foreach ($contributionItems as $item): ?>
                                                <tr>
                                                    <td class="py-3 pl-2">
                                                        <div class="font-bold text-charcoal">
                                                            <?= e(!empty($item['item_name']) ? $item['item_name'] : $item['sub_type']) ?>
                                                        </div>
                                                        <?php if (!empty($item['description'])): ?>
                                                            <div class="text-xs text-charcoal-light mt-0.5"><?= e($item['description']) ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-3 font-bold text-forest whitespace-nowrap">
                                                        <?= (int)$item['quantity'] ?> <?= e(!empty($item['unit']) ? $item['unit'] : 'units') ?>
                                                    </td>
                                                    <td class="py-3 text-charcoal-light whitespace-nowrap">
                                                        <?= e(!empty($item['condition_state']) ? $item['condition_state'] : 'Good') ?>
                                                    </td>
                                                    <td class="py-3 text-charcoal-light whitespace-nowrap">
                                                        <?= e(!empty($item['delivery_method']) ? $item['delivery_method'] : 'Drop-off / Standard') ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-charcoal-light py-4 text-center">No individual item records found for this resource contribution.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- DONOR MESSAGE -->
                    <?php if (!empty($contribData['message'])): ?>
                        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20">
                            <h2 class="text-lg font-serif font-bold text-forest mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                Contributor's Message
                            </h2>
                            <div class="p-4 rounded-2xl bg-ivory/60 border border-gold/20 italic text-charcoal text-sm">
                                "<?= e($contribData['message']) ?>"
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- RIGHT COLUMN: FULFILLMENT MANAGEMENT -->
                <div class="space-y-8">

                    <?php if ($isMoney): ?>
                        <!-- MONEY CONTRIBUTION CARD -->
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gold/20">
                            <h3 class="text-base font-serif font-bold text-forest mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Monetary Payment Status
                            </h3>

                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between items-center py-2 border-b border-charcoal/5">
                                    <span class="text-charcoal-light">Amount</span>
                                    <span class="font-bold text-forest"><?= $primaryDisplay ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-charcoal/5">
                                    <span class="text-charcoal-light">Payment Status</span>
                                    <span class="font-bold uppercase tracking-wider text-xs px-2.5 py-0.5 rounded-full <?= $paymentStatus === 'completed' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' ?>">
                                        <?= e($paymentStatus) ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-charcoal/5">
                                    <span class="text-charcoal-light">Fulfillment</span>
                                    <span class="font-bold text-charcoal"><?= e(ucfirst($fulfillmentStatus)) ?></span>
                                </div>
                            </div>

                            <div class="mt-6 p-4 rounded-2xl bg-ivory text-xs text-charcoal-light leading-relaxed border border-charcoal/5">
                                <p class="font-bold text-charcoal mb-1">Financial Reconciliation Notice</p>
                                Monetary contributions are processed and verified automatically by the payment gateway. Payment status cannot be altered manually to ensure end-to-end accounting integrity.
                            </div>
                        </div>

                    <?php else: ?>

                        <!-- RESOURCE FULFILLMENT MANAGEMENT CARD -->
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gold/20">
                            <h3 class="text-base font-serif font-bold text-forest mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Fulfillment Workflow
                            </h3>

                            <!-- STEP PROGRESS -->
                            <div class="space-y-4 mb-6">
                                <!-- Step 1 -->
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= in_array($fulfillmentStatus, ['pending', 'received', 'completed'], true) ? 'bg-forest text-white' : 'bg-charcoal/10 text-charcoal' ?>">
                                        1
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-charcoal">Pledge Submitted</p>
                                        <p class="text-xs text-charcoal-light">User registered the physical pledge.</p>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= in_array($fulfillmentStatus, ['received', 'completed'], true) ? 'bg-forest text-white' : 'bg-charcoal/10 text-charcoal' ?>">
                                        2
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-charcoal">Received by NGO</p>
                                        <p class="text-xs text-charcoal-light">Goods verified at collection center.</p>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= $fulfillmentStatus === 'completed' ? 'bg-forest text-white' : 'bg-charcoal/10 text-charcoal' ?>">
                                        3
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-charcoal">Completed / Distributed</p>
                                        <p class="text-xs text-charcoal-light">Distributed to beneficiaries.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTIONS ACCORDING TO CURRENT STATUS -->
                            <div class="pt-4 border-t border-charcoal/10 space-y-3">
                                <?php if ($fulfillmentStatus === 'pending'): ?>
                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="action" value="mark_received">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-forest text-white rounded-xl font-bold hover:bg-forest-dark transition-colors shadow-sm text-sm">
                                            Mark as Received (+50 Pts)
                                        </button>
                                    </form>

                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="action" value="mark_completed">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors shadow-sm text-sm">
                                            Direct Complete
                                        </button>
                                    </form>

                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="action" value="cancel_contribution">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this contribution?');" class="w-full py-2 px-4 border border-red-200 text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors text-xs text-center">
                                            Cancel Contribution
                                        </button>
                                    </form>

                                <?php elseif ($fulfillmentStatus === 'received'): ?>
                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="action" value="mark_completed">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors shadow-sm text-sm">
                                            Mark as Completed
                                        </button>
                                    </form>

                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="action" value="cancel_contribution">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this received contribution? Any awarded Karma points will be reversed.');" class="w-full py-2 px-4 border border-red-200 text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors text-xs text-center">
                                            Cancel & Reverse Points
                                        </button>
                                    </form>

                                <?php elseif ($fulfillmentStatus === 'completed'): ?>
                                    <div class="p-3 bg-green-50 text-forest border border-green-200 rounded-xl text-center text-xs font-bold">
                                        ✓ Contribution Verified & Completed
                                    </div>
                                    <form method="POST" class="w-full pt-2">
                                        <input type="hidden" name="action" value="cancel_contribution">
                                        <input type="hidden" name="contribution_id" value="<?= (int)$contribData['id'] ?>">
                                        <button type="submit" onclick="return confirm('Warning: This contribution is marked completed. Cancelling it will reverse all awarded Karma points. Proceed?');" class="w-full py-2 px-4 border border-red-200 text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors text-xs text-center">
                                            Cancel & Reverse
                                        </button>
                                    </form>

                                <?php elseif ($fulfillmentStatus === 'cancelled'): ?>
                                    <div class="p-3 bg-charcoal/10 text-charcoal border border-charcoal/20 rounded-xl text-center text-xs font-bold">
                                        This contribution has been cancelled.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <!-- ORGANIZATION AUDIT SUMMARY -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gold/20 text-xs text-charcoal-light space-y-2">
                        <p class="font-bold text-charcoal uppercase tracking-wider">Audit Trail</p>
                        <p><strong>Created:</strong> <?= date('d M Y, h:i A', strtotime($contribData['created_at'])) ?></p>
                        <p><strong>Updated:</strong> <?= date('d M Y, h:i A', strtotime($contribData['updated_at'])) ?></p>
                        <p><strong>Organization:</strong> <?= e($contribData['ngo_name']) ?></p>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>
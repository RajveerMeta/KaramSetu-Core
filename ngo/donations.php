<?php
require_once __DIR__ . '/../includes/functions.php';
requireNgo();
require_once __DIR__ . '/../Database/db_config.php';

ob_start();

$errorMsg = '';
$ngo_id = 0;

$totalContributions = 0;
$moneyReceived = 0;
$foodQuantity = 0;
$foodUnit = '';
$itemQuantity = 0;

$contributions = [];

// FILTER VALUES
$typeFilter = $_GET['type'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';

// ALLOWED FILTER VALUES
$allowedTypes = ['all', 'money', 'resource', 'food', 'clothes', 'items'];
$allowedStatuses = ['all', 'pending', 'received', 'completed', 'cancelled'];

if (!in_array($typeFilter, $allowedTypes, true)) {
    $typeFilter = 'all';
}

if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'all';
}

// GET LOGGED-IN USER
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if ($user_id > 0) {

    // GET NGO ID
    $ngoQuery = mysqli_query(
        $con,
        "SELECT id FROM ngos WHERE user_id = $user_id LIMIT 1"
    );

    if ($ngoQuery && mysqli_num_rows($ngoQuery) > 0) {

        $ngoData = mysqli_fetch_assoc($ngoQuery);
        $ngo_id = (int) $ngoData['id'];

        // Flash messages
        $donationActionMsg = $_SESSION['ngo_donation_msg'] ?? '';
        $donationActionType = $_SESSION['ngo_donation_type'] ?? '';
        unset($_SESSION['ngo_donation_msg'], $_SESSION['ngo_donation_type']);

        // Handle NGO Verification / Rejection of Donations
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
            $action = trim($_POST['action']);
            $contribId = isset($_POST['contribution_id']) ? (int)$_POST['contribution_id'] : 0;

            if ($contribId > 0 && $ngo_id > 0) {
                $cCheck = mysqli_query($con, "SELECT c.*, n.name AS ngo_name FROM contributions c INNER JOIN ngos n ON n.id = c.ngo_id WHERE c.id = $contribId AND c.ngo_id = $ngo_id LIMIT 1");
                if ($cCheck && mysqli_num_rows($cCheck) > 0) {
                    $cRow = mysqli_fetch_assoc($cCheck);
                    $donorUserId = !empty($cRow['user_id']) ? (int)$cRow['user_id'] : 0;
                    $cType = $cRow['contribution_type'];

                    if ($cType === 'money') {
                        $_SESSION['ngo_donation_msg'] = "Payment status cannot be modified manually. Monetary donations are reconciled automatically.";
                        $_SESSION['ngo_donation_type'] = 'error';
                    } elseif ($action === 'mark_received') {
                        if ($cRow['fulfillment_status'] === 'pending') {
                            mysqli_begin_transaction($con);
                            $up = mysqli_query($con, "UPDATE contributions SET fulfillment_status = 'received', updated_at = NOW() WHERE id = $contribId");
                            if ($up) {
                                $awarded = 0;
                                if ($donorUserId > 0) {
                                    $pts = 50; // 50 Karma points for resource donation on NGO verification
                                    $tx = awardKarmaPoints($donorUserId, $pts, 'donation_resource', $contribId, "Karma points for " . ucfirst($cType) . " donation to " . ($cRow['ngo_name'] ?? 'NGO'));
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
                            $up = mysqli_query($con, "UPDATE contributions SET fulfillment_status = 'completed', updated_at = NOW() WHERE id = $contribId");
                            if ($up) {
                                $awarded = 0;
                                if ($donorUserId > 0) {
                                    $pts = 50; // 50 Karma points for resource donation on NGO verification (duplicate awards prevented by helper)
                                    $tx = awardKarmaPoints($donorUserId, $pts, 'donation_resource', $contribId, "Karma points for " . ucfirst($cType) . " donation to " . ($cRow['ngo_name'] ?? 'NGO'));
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
                            $up = mysqli_query($con, "UPDATE contributions SET fulfillment_status = 'cancelled', updated_at = NOW() WHERE id = $contribId");
                            if ($up) {
                                $reversed = false;
                                if ($donorUserId > 0) {
                                    $revId = reverseKarmaPoints($donorUserId, 'donation_resource', $contribId, "Points reversed due to cancellation of contribution #" . $cRow['contribution_reference']);
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
            }
            header("Location: " . baseUrl("ngo/donations.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')));
            exit;
        }

        // TOTAL CONTRIBUTIONS (Excluding failed payments and cancelled fulfillment)
        $totalQuery = mysqli_query(
            $con,
            "SELECT COUNT(*) AS total
             FROM contributions
             WHERE ngo_id = $ngo_id
             AND fulfillment_status != 'cancelled'
             AND NOT (contribution_type = 'money' AND payment_status = 'failed')"
        );

        if ($totalQuery) {
            $totalData = mysqli_fetch_assoc($totalQuery);
            $totalContributions = (int) $totalData['total'];
        }

        // TOTAL MONEY RECEIVED (Excluding cancelled fulfillment)
        $moneyQuery = mysqli_query(
            $con,
            "SELECT COALESCE(SUM(monetary_amount), 0) AS total_money
             FROM contributions
             WHERE ngo_id = $ngo_id
             AND contribution_type = 'money'
             AND payment_status = 'completed'
             AND fulfillment_status != 'cancelled'"
        );

        if ($moneyQuery) {
            $moneyData = mysqli_fetch_assoc($moneyQuery);
            $moneyReceived = (float) $moneyData['total_money'];
        }

        // TOTAL FOOD CONTRIBUTIONS
        $foodQuery = mysqli_query(
            $con,
            "SELECT
                COALESCE(SUM(ci.quantity), 0) AS total_quantity,
                COUNT(DISTINCT ci.unit) AS unit_count,
                MIN(ci.unit) AS single_unit
             FROM contributions c
             INNER JOIN contribution_items ci
                ON c.id = ci.contribution_id
             WHERE c.ngo_id = $ngo_id
             AND c.contribution_type = 'food'
             AND c.fulfillment_status IN ('received', 'completed')"
        );

        if ($foodQuery) {
            $foodData = mysqli_fetch_assoc($foodQuery);

            $foodQuantity = (int) $foodData['total_quantity'];

            if ($foodData['unit_count'] == 1) {
                $foodUnit = $foodData['single_unit'];
            } elseif ($foodData['unit_count'] > 1) {
                $foodUnit = 'mixed units';
            }
        }

        // TOTAL CLOTHES AND USEFUL ITEMS
        $itemQuery = mysqli_query(
            $con,
            "SELECT COALESCE(SUM(ci.quantity), 0) AS total_quantity
             FROM contributions c
             INNER JOIN contribution_items ci
                ON c.id = ci.contribution_id
             WHERE c.ngo_id = $ngo_id
             AND c.contribution_type IN ('clothes', 'items')
             AND c.fulfillment_status IN ('received', 'completed')"
        );

        if ($itemQuery) {
            $itemData = mysqli_fetch_assoc($itemQuery);
            $itemQuantity = (int) $itemData['total_quantity'];
        }

        // BUILD FILTER QUERY
        $filterCondition = "WHERE c.ngo_id = $ngo_id";

        if ($typeFilter === 'money') {
            $filterCondition .= " AND c.contribution_type = 'money'";
        } elseif ($typeFilter === 'resource') {
            $filterCondition .= " AND c.contribution_type != 'money'";
        } elseif (in_array($typeFilter, ['food', 'clothes', 'items'], true)) {
            $filterCondition .= " AND c.contribution_type = '$typeFilter'";
        }

        if ($statusFilter === 'pending') {
            $filterCondition .= "
                AND (
                    (c.contribution_type = 'money' AND c.payment_status = 'pending')
                    OR
                    (c.contribution_type != 'money' AND c.fulfillment_status = 'pending')
                )";
        } elseif ($statusFilter === 'received') {
            $filterCondition .= "
                AND (
                    (c.contribution_type = 'money' AND c.payment_status = 'completed')
                    OR
                    (c.contribution_type != 'money' AND c.fulfillment_status = 'received')
                )";
        } elseif ($statusFilter === 'completed') {
            $filterCondition .= "
                AND (
                    (c.contribution_type = 'money' AND c.payment_status = 'completed')
                    OR
                    (c.contribution_type != 'money' AND c.fulfillment_status = 'completed')
                )";
        } elseif ($statusFilter === 'cancelled') {
            $filterCondition .= "
                AND (
                    c.fulfillment_status = 'cancelled'
                    OR
                    (c.contribution_type = 'money' AND c.payment_status = 'failed')
                )";
        }

        // GET CONTRIBUTIONS
        $contributionQuery = mysqli_query(
            $con,
            "SELECT
                c.id,
                c.contribution_reference,
                c.contribution_type,
                c.donor_name,
                c.is_anonymous,
                c.monetary_amount,
                c.payment_status,
                c.fulfillment_status,
                c.created_at,
                ca.title AS cause_title,

                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(ci.item_name, ci.sub_type, ci.category),
                        ' (',
                        ci.quantity,
                        ' ',
                        COALESCE(NULLIF(ci.unit, ''), 'units'),
                        ')'
                    )
                    SEPARATOR ', '
                ) AS item_details

             FROM contributions c

             LEFT JOIN contribution_items ci
                ON c.id = ci.contribution_id

             LEFT JOIN causes ca
                ON c.cause_id = ca.id

             $filterCondition

             GROUP BY c.id

             ORDER BY c.created_at DESC"
        );

        if ($contributionQuery) {

            while ($row = mysqli_fetch_assoc($contributionQuery)) {
                $contributions[] = $row;
            }

        } else {
            $errorMsg = 'Unable to load contributions.';
        }

    } else {
        $errorMsg = 'NGO account not found.';
    }

} else {
    $errorMsg = 'Please login to access contributions.';
}

// FOOD DISPLAY
$foodDisplay = $foodQuantity;

if ($foodQuantity > 0 && !empty($foodUnit)) {
    $foodDisplay .= ' ' . $foodUnit;
}

// CONTRIBUTION TYPE COLORS
$typeClasses = [
    'money' => 'bg-forest/10 text-forest border border-forest/20',
    'food' => 'bg-forest/10 text-forest border border-forest/20',
    'clothes' => 'bg-forest/10 text-forest border border-forest/20',
    'items' => 'bg-forest/10 text-forest border border-forest/20'
];

// STATUS COLORS
$statusClasses = [
    'received' => 'bg-blue-50 text-blue-700 border border-blue-200',
    'completed' => 'bg-green-50 text-forest border border-green-200',
    'pending' => 'bg-gold/15 text-charcoal border border-gold/30',
    'failed' => 'bg-red-50 text-red-700 border border-red-200',
    'cancelled' => 'bg-charcoal/10 text-charcoal border border-charcoal/20',
    'not_applicable' => 'bg-ivory text-charcoal-light border border-charcoal/10'
];
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">
                NGO Contributions
            </h1>

            <p class="text-charcoal-light">
                View and manage contributions received by your organization.
            </p>
        </div>

        <?php if (!empty($errorMsg)): ?>

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
                <?= $errorMsg ?>
            </div>

        <?php else: ?>

            <?php if (!empty($donationActionMsg)): ?>
                <div class="mb-6 p-4 rounded-xl <?= $donationActionType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                    <p class="text-sm font-medium"><?= e($donationActionMsg) ?></p>
                </div>
            <?php endif; ?>

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:p-6 mb-10">

                <!-- TOTAL CONTRIBUTIONS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">

                    <div class="w-12 h-12 min-w-12 min-h-12 shrink-0 bg-forest/10 rounded-full flex items-center justify-center mb-3">

                        <svg
                            class="w-6 h-6 min-w-6 min-h-6 shrink-0 text-forest"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 4v16M20 12H4"></path>
                        </svg>

                    </div>

                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">
                        Total Contributions
                    </h3>

                    <p class="text-xl md:text-3xl font-serif font-bold text-forest">
                        <?= number_format($totalContributions) ?>
                    </p>

                </div>

                <!-- MONEY RECEIVED -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">

                    <div class="w-12 h-12 min-w-12 min-h-12 shrink-0 bg-forest/10 rounded-full flex items-center justify-center mb-3">

                        <svg
                            class="w-6 h-6 min-w-6 min-h-6 shrink-0 text-forest"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M16 8.5c-.7-.7-1.8-1.1-3-1.1-1.8 0-3 .9-3 2.1s1.2 1.9 3 2.2 3 .9 3 2.2-1.2 2.1-3 2.1c-1.2 0-2.3-.4-3-1.1M13 6v12"></path>
                        </svg>

                    </div>

                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">
                        Money Received
                    </h3>

                    <p class="text-xl md:text-3xl font-serif font-bold text-forest">
                        ₹<?= number_format($moneyReceived, 2) ?>
                    </p>

                </div>

                <!-- FOOD CONTRIBUTIONS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">

                    <div class="w-12 h-12 min-w-12 min-h-12 shrink-0 bg-forest/10 rounded-full flex items-center justify-center mb-3">

                        <svg
                            class="w-6 h-6 min-w-6 min-h-6 shrink-0 text-forest"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 3h2l2.4 11.5a2 2 0 0 0 2 1.5h8.8a2 2 0 0 0 1.9-1.4L22 8H6"></path>
                            <circle cx="10" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                        </svg>

                    </div>

                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">
                        Food Contributions
                    </h3>

                    <p class="text-xl md:text-3xl font-serif font-bold text-forest">
                        <?= $foodDisplay ?>
                    </p>

                </div>

                <!-- ITEM CONTRIBUTIONS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-center items-center text-center">

                    <div class="w-12 h-12 min-w-12 min-h-12 shrink-0 bg-forest/10 rounded-full flex items-center justify-center mb-3">

                        <svg
                            class="w-6 h-6 min-w-6 min-h-6 shrink-0 text-forest"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                        </svg>

                    </div>

                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wider mb-1">
                        Item Contributions
                    </h3>

                    <p class="text-xl md:text-3xl font-serif font-bold text-forest">
                        <?= number_format($itemQuantity) ?> items
                    </p>

                </div>

            </div>

            <!-- MAIN CONTENT -->
            <div class="bg-white rounded-3xl shadow-sm border border-gold/20 overflow-hidden">

                <!-- FILTERS -->
                <form method="GET" class="p-4 md:p-8 border-b border-charcoal/10 bg-ivory/30">

                    <div class="flex flex-col md:flex-row gap-4 items-end">

                        <!-- TYPE FILTER -->
                        <div class="w-full md:w-1/3">

                            <label class="block text-sm font-bold text-charcoal mb-2">
                                Contribution Type
                            </label>

                            <select
                                name="type"
                                class="w-full px-4 py-2 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest transition-all bg-white appearance-none"
                            >

                                <option value="all" <?= $typeFilter == 'all' ? 'selected' : '' ?>>
                                    All Types
                                </option>

                                <option value="money" <?= $typeFilter == 'money' ? 'selected' : '' ?>>
                                    Money
                                </option>

                                <option value="resource" <?= $typeFilter == 'resource' ? 'selected' : '' ?>>
                                    All Resources (Food, Clothes, Items)
                                </option>

                                <option value="food" <?= $typeFilter == 'food' ? 'selected' : '' ?>>
                                    Food
                                </option>

                                <option value="clothes" <?= $typeFilter == 'clothes' ? 'selected' : '' ?>>
                                    Clothes
                                </option>

                                <option value="items" <?= $typeFilter == 'items' ? 'selected' : '' ?>>
                                    Useful Items
                                </option>

                            </select>

                        </div>

                        <!-- STATUS FILTER -->
                        <div class="w-full md:w-1/3">

                            <label class="block text-sm font-bold text-charcoal mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full px-4 py-2 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest transition-all bg-white appearance-none"
                            >

                                <option value="all" <?= $statusFilter == 'all' ? 'selected' : '' ?>>
                                    All Statuses
                                </option>

                                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>
                                    Pending
                                </option>

                                <option value="received" <?= $statusFilter == 'received' ? 'selected' : '' ?>>
                                    Received
                                </option>

                                <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>
                                    Completed
                                </option>

                                <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>
                                    Cancelled
                                </option>

                            </select>

                        </div>

                        <!-- BUTTONS -->
                        <div class="flex gap-2 w-full md:w-auto">

                            <button
                                type="submit"
                                class="px-6 py-2 bg-forest text-white rounded-xl font-bold hover:bg-forest-dark transition-colors"
                            >
                                Apply Filters
                            </button>

                            <a
                                href="<?= baseUrl('ngo/donations.php') ?>"
                                class="px-6 py-2 border border-charcoal/20 text-charcoal rounded-xl font-bold hover:bg-ivory transition-colors"
                            >
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

                <!-- CONTRIBUTIONS TABLE -->
                <div class="p-4 md:p-8">

                    <div class="overflow-x-auto">

                        <table class="w-full text-left border-collapse min-w-[800px]">

                            <thead>
                                <tr class="border-b-2 border-charcoal/10 text-charcoal font-bold text-sm uppercase tracking-wider">

                                    <th class="pb-4 pl-2">Contributor</th>
                                    <th class="pb-4">Type</th>
                                    <th class="pb-4">Date</th>
                                    <th class="pb-4">Amount / Quantity</th>
                                    <th class="pb-4">Status</th>
                                    <th class="pb-4 text-right pr-2">Action</th>

                                </tr>
                            </thead>

                            <tbody>

                                <?php if (count($contributions) > 0): ?>

                                    <?php foreach ($contributions as $contribution): ?>

                                        <?php
                                        $type = $contribution['contribution_type'];

                                        $typeLabel = ($type == 'items')
                                            ? 'Useful Items'
                                            : ucfirst($type);

                                        // CONTRIBUTOR NAME
                                        if ($contribution['is_anonymous'] == 1) {
                                            $donorName = 'Anonymous';
                                        } else {
                                            $donorName = !empty($contribution['donor_name']) ? $contribution['donor_name'] : 'Anonymous';
                                        }

                                        // AMOUNT AND STATUS
                                        if ($type === 'money') {
                                            $status = $contribution['payment_status'];
                                            if ($contribution['fulfillment_status'] === 'cancelled') {
                                                $status = 'cancelled';
                                                $statusLabel = 'Cancelled';
                                            } elseif ($status === 'completed') {
                                                $statusLabel = 'Paid';
                                            } elseif ($status === 'pending') {
                                                $statusLabel = 'Pending Payment';
                                            } elseif ($status === 'failed') {
                                                $statusLabel = 'Failed';
                                            } else {
                                                $statusLabel = ucfirst($status);
                                            }

                                            $amount = '₹' . number_format(
                                                (float) $contribution['monetary_amount'],
                                                2
                                            );
                                        } else {
                                            $status = $contribution['fulfillment_status'];
                                            $statusLabel = ucfirst($status);

                                            if (!empty($contribution['item_details'])) {
                                                $amount = $contribution['item_details'];
                                            } else {
                                                $amount = '—';
                                            }
                                        }

                                        $typeClass = $typeClasses[$type] ?? 'bg-gray-100 text-gray-700';
                                        $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700';
                                        ?>

                                        <tr class="border-b border-charcoal/5 hover:bg-ivory/50 transition-colors">

                                            <!-- CONTRIBUTOR -->
                                            <td class="py-4 pl-2">

                                                <div class="font-bold text-charcoal">
                                                    <?= e($donorName) ?>
                                                </div>

                                                <div class="text-xs text-charcoal-light mt-1 font-mono">
                                                    <?= e($contribution['contribution_reference']) ?>
                                                </div>

                                                <?php if (!empty($contribution['cause_title'])): ?>

                                                    <div class="text-xs text-charcoal-light mt-1">
                                                        Cause: <?= e($contribution['cause_title']) ?>
                                                    </div>

                                                <?php endif; ?>

                                            </td>

                                            <!-- TYPE -->
                                            <td class="py-4">

                                                <span class="inline-block px-3 py-1 <?= $typeClass ?> text-xs font-bold rounded-full">
                                                    <?= $typeLabel ?>
                                                </span>

                                            </td>

                                            <!-- DATE -->
                                            <td class="py-4 text-sm text-charcoal">
                                                <?= date('d M Y', strtotime($contribution['created_at'])) ?>
                                            </td>

                                            <!-- AMOUNT / QUANTITY -->
                                            <td class="py-4 font-bold text-forest">
                                                <?= e($amount) ?>
                                            </td>

                                            <!-- STATUS -->
                                            <td class="py-4">

                                                <span class="inline-block px-3 py-1 <?= $statusClass ?> text-xs font-bold rounded-full">
                                                    <?= $statusLabel ?>
                                                </span>

                                            </td>

                                            <!-- ACTION -->
                                            <td class="py-4 text-right pr-2">
                                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                                    <a href="<?= baseUrl('ngo/donation-details.php?id=' . (int)$contribution['id']) ?>"
                                                       class="px-3 py-1.5 border border-forest/30 text-forest hover:bg-forest hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm whitespace-nowrap">
                                                        View Details
                                                    </a>

                                                    <?php if ($contribution['contribution_type'] !== 'money' && $contribution['fulfillment_status'] === 'pending'): ?>
                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="action" value="mark_received">
                                                            <input type="hidden" name="contribution_id" value="<?= (int)$contribution['id'] ?>">
                                                            <button type="submit" class="px-2.5 py-1.5 bg-forest text-white rounded-lg text-xs font-bold hover:bg-forest-dark transition-colors shadow-sm whitespace-nowrap">
                                                                Receive
                                                            </button>
                                                        </form>
                                                    <?php elseif ($contribution['contribution_type'] !== 'money' && $contribution['fulfillment_status'] === 'received'): ?>
                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="action" value="mark_completed">
                                                            <input type="hidden" name="contribution_id" value="<?= (int)$contribution['id'] ?>">
                                                            <button type="submit" class="px-2.5 py-1.5 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 transition-colors shadow-sm whitespace-nowrap">
                                                                Complete
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-charcoal-light">

                                            <div class="flex flex-col items-center">

                                                <svg
                                                    class="w-12 h-12 min-w-12 min-h-12 shrink-0 mb-3 text-charcoal-light/50"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2z"></path>
                                                </svg>

                                                <p class="font-bold text-charcoal">
                                                    No Contributions Found
                                                </p>

                                                <p class="text-sm mt-1">
                                                    No contributions match the selected filters.
                                                </p>

                                            </div>

                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

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

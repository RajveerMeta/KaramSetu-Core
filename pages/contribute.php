<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 1. GET CAUSE AND NGO IDs */
$cause_id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_GET['cause_id']) ? (int) $_GET['cause_id'] : 0);
$ngo_id = isset($_GET['ngo_id']) ? (int) $_GET['ngo_id'] : 0;

$cause = null;
$ngo = null;
$is_valid_cause = false;

/* 2. FETCH CAUSE AND NGO DETAILS FROM DATABASE */
$select_query = "
    SELECT
        c.*,
        n.id AS ngo_id,
        n.name AS ngo_name,
        n.primary_cause AS ngo_category,
        n.city AS ngo_city,
        n.state AS ngo_state,
        n.description AS ngo_description,
        n.verification_status
    FROM causes c
    INNER JOIN ngos n ON n.id = c.ngo_id
";

if ($cause_id > 0) {
    $sql = $select_query . " WHERE c.id = $cause_id LIMIT 1";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $cause = mysqli_fetch_assoc($result);
    }
}

if (!$cause && $ngo_id > 0) {
    $sql = $select_query . " WHERE c.ngo_id = $ngo_id AND c.status = 'active' AND n.verification_status = 'approved' ORDER BY c.id ASC LIMIT 1";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $cause = mysqli_fetch_assoc($result);
    }
}

if (!$cause) {
    $sql = $select_query . " WHERE c.status = 'active' AND n.verification_status = 'approved' ORDER BY c.id ASC LIMIT 1";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $cause = mysqli_fetch_assoc($result);
    }
}

if ($cause) {
    $cause_id = (int) $cause['id'];
    $ngo = [
        'id' => (int) $cause['ngo_id'],
        'name' => $cause['ngo_name'],
        'category' => $cause['ngo_category'],
        'city' => $cause['ngo_city'],
        'state' => $cause['ngo_state'],
        'description' => $cause['ngo_description'],
        'verification_status' => $cause['verification_status']
    ];

    if ($cause['status'] === 'active' && $cause['verification_status'] === 'approved') {
        $is_valid_cause = true;
    }
} else {
    $cause = [
        'id' => 0,
        'title' => 'No Active Cause Found',
        'category' => 'General Support',
        'target_amount' => 0,
        'short_description' => 'Currently there is no active cause available for contribution.',
        'detailed_description' => 'Please check back later or explore other verified organizations.',
        'cause_image' => null,
        'status' => 'inactive'
    ];
    $ngo = [
        'id' => 0,
        'name' => 'KarmaSetu',
        'category' => 'Community',
        'city' => '',
        'state' => '',
        'description' => '',
        'verification_status' => 'pending'
    ];
}

/* 3. GET ACCEPTED CONTRIBUTION TYPES FROM DATABASE */
$accepted_types = [];
if ($cause_id > 0) {
    $sql = "SELECT contribution_type FROM cause_accepted_types WHERE cause_id = $cause_id";
    $types_result = mysqli_query($con, $sql);
    if ($types_result && mysqli_num_rows($types_result) > 0) {
        while ($type_row = mysqli_fetch_assoc($types_result)) {
            $accepted_types[] = strtolower(trim($type_row['contribution_type']));
        }
    }
}

// Fallback: if no accepted types recorded in database, accept all four types
if (empty($accepted_types)) {
    $accepted_types = ['money', 'food', 'clothes', 'items'];
}

$default_type = in_array('money', $accepted_types) ? 'money' : $accepted_types[0];

/* 4. PRE-FILL LOGGED-IN USER INFORMATION */
$logged_in_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';
$user_phone = '';

if ($logged_in_user_id > 0) {
    $u_res = mysqli_query($con, "SELECT name, email, phone FROM users WHERE id = $logged_in_user_id LIMIT 1");
    if ($u_res && mysqli_num_rows($u_res) > 0) {
        $u_row = mysqli_fetch_assoc($u_res);
        $user_name = $u_row['name'];
        $user_email = $u_row['email'];
        $user_phone = $u_row['phone'] ?? '';
    }
}

/* 5. PROCESS CONTRIBUTION SUBMISSION */
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $ref = isset($_GET['ref']) ? $_GET['ref'] : '';
    $success_message = "Thank you! Your contribution has been submitted successfully." . ($ref ? " Reference: " . $ref : "");
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['contribution_type'])) {

    $type = strtolower(trim($_POST['contribution_type']));
    $donor_name = trim($_POST['full_name'] ?? '');
    $donor_email = trim($_POST['email'] ?? '');
    $donor_phone = trim($_POST['phone'] ?? '');
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $message = trim($_POST['message'] ?? '');

    $user_id_sql = ($logged_in_user_id > 0) ? $logged_in_user_id : "NULL";

    $amount = 0;
    $quantity = 1;
    $sub_type = '';
    $item_name = '';
    $unit = '';
    $condition_state = '';
    $delivery_method = '';
    $intended_for = '';
    $description = '';

    if (!$is_valid_cause || $cause_id <= 0 || $ngo['id'] <= 0) {
        $error_message = "This cause is currently not accepting contributions.";
    } elseif (!in_array($type, ['money', 'food', 'clothes', 'items'])) {
        $error_message = "Invalid contribution type selected.";
    } elseif (!in_array($type, $accepted_types)) {
        $error_message = "This cause does not accept " . ucfirst($type) . " contributions.";
    } elseif ($donor_name === '' || $donor_email === '') {
        $error_message = "Please enter your full name and email address.";
    } else {

        /* VALIDATE BASED ON SELECTED TYPE */
        if ($type === 'money') {
            $amount = isset($_POST['contribution_amount']) ? (float) $_POST['contribution_amount'] : 0;
            if ($amount <= 0) {
                $error_message = "Contribution amount must be greater than zero.";
            }
        } elseif ($type === 'food') {
            $sub_type = trim($_POST['food_type'] ?? 'Groceries');
            $quantity = (int) ($_POST['food_quantity'] ?? 0);
            $unit = trim($_POST['food_unit'] ?? 'kg');
            $delivery_method = trim($_POST['food_delivery'] ?? 'Drop off at NGO center');

            if ($quantity <= 0) {
                $error_message = "Please enter a valid food quantity.";
            }
        } elseif ($type === 'clothes') {
            $sub_type = trim($_POST['clothing_type'] ?? 'Clothes');
            $quantity = (int) ($_POST['clothes_quantity'] ?? 0);
            $condition_state = trim($_POST['clothing_condition'] ?? 'Good Condition');
            $intended_for = trim($_POST['clothing_for'] ?? 'Any');

            if ($quantity <= 0) {
                $error_message = "Please enter a valid clothing quantity.";
            }
        } elseif ($type === 'items') {
            $item_name = trim($_POST['item_name'] ?? '');
            $quantity = (int) ($_POST['item_quantity'] ?? 0);
            $condition_state = trim($_POST['item_condition'] ?? 'Good Condition');
            $description = trim($_POST['item_description'] ?? '');

            if ($item_name === '' || $quantity <= 0) {
                $error_message = "Please enter the item name and a valid quantity.";
            }
        }

        /* DATABASE INSERTION WITH TRANSACTION */
        if ($error_message === '') {
            $reference = 'KS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $payment_status = ($type === 'money') ? 'pending' : 'not_applicable';
            $fulfillment_status = 'pending';
            $monetary_amount_sql = ($type === 'money') ? $amount : "NULL";

            // Clean single quotes for SQL safety without using forbidden functions
            $safe_donor_name = str_replace("'", "''", $donor_name);
            $safe_donor_email = str_replace("'", "''", $donor_email);
            $safe_donor_phone = str_replace("'", "''", $donor_phone);
            $safe_message = str_replace("'", "''", $message);
            $safe_sub_type = str_replace("'", "''", $sub_type);
            $safe_item_name = str_replace("'", "''", $item_name);
            $safe_unit = str_replace("'", "''", $unit);
            $safe_condition_state = str_replace("'", "''", $condition_state);
            $safe_delivery_method = str_replace("'", "''", $delivery_method);
            $safe_intended_for = str_replace("'", "''", $intended_for);
            $safe_description = str_replace("'", "''", $description);

            mysqli_begin_transaction($con);

            $insert_contrib_sql = "
                INSERT INTO contributions (
                    contribution_reference,
                    user_id,
                    ngo_id,
                    cause_id,
                    contribution_type,
                    donor_name,
                    donor_email,
                    donor_phone,
                    is_anonymous,
                    monetary_amount,
                    payment_status,
                    fulfillment_status,
                    message,
                    created_at,
                    updated_at
                ) VALUES (
                    '$reference',
                    $user_id_sql,
                    " . $ngo['id'] . ",
                    $cause_id,
                    '$type',
                    '$safe_donor_name',
                    '$safe_donor_email',
                    '$safe_donor_phone',
                    $anonymous,
                    $monetary_amount_sql,
                    '$payment_status',
                    '$fulfillment_status',
                    '$safe_message',
                    NOW(),
                    NOW()
                )
            ";

            if (mysqli_query($con, $insert_contrib_sql)) {
                $contribution_id = mysqli_insert_id($con);
                $items_success = true;

                if ($type !== 'money') {
                    $insert_item_sql = "
                        INSERT INTO contribution_items (
                            contribution_id,
                            category,
                            sub_type,
                            item_name,
                            quantity,
                            unit,
                            condition_state,
                            delivery_method,
                            intended_for,
                            description,
                            created_at
                        ) VALUES (
                            $contribution_id,
                            '$type',
                            '$safe_sub_type',
                            '$safe_item_name',
                            $quantity,
                            '$safe_unit',
                            '$safe_condition_state',
                            '$safe_delivery_method',
                            '$safe_intended_for',
                            '$safe_description',
                            NOW()
                        )
                    ";

                    if (!mysqli_query($con, $insert_item_sql)) {
                        $items_success = false;
                    }
                }

                if ($items_success) {
                    mysqli_commit($con);
                    header("Location: " . baseUrl('?page=contribute&id=' . $cause_id . '&success=1&ref=' . $reference));
                    exit;
                } else {
                    mysqli_rollback($con);
                    $error_message = "Could not save contribution item details. Please try again.";
                }
            } else {
                mysqli_rollback($con);
                $error_message = "Could not record your contribution. Please try again.";
            }
        }
    }
}

/* 6. GET TOTAL RAISED AMOUNT */
$raised_amount = 0.00;

if ($cause_id > 0) {
    $sql = "
        SELECT COALESCE(SUM(monetary_amount), 0) AS total_raised
        FROM contributions
        WHERE cause_id = $cause_id
        AND payment_status IN ('completed', 'pending')
    ";

    $raised_result = mysqli_query($con, $sql);

    if ($raised_result) {
        $raised_row = mysqli_fetch_assoc($raised_result);
        $raised_amount = (float) $raised_row['total_raised'];
    }
}

/* 7. CALCULATE FUNDRAISING PROGRESS */
$target_amount = isset($cause['target_amount']) ? (float) $cause['target_amount'] : 0.00;
$progress_percentage = 0;

if ($target_amount > 0) {
    $progress_percentage = (int) round(($raised_amount / $target_amount) * 100);
}

$remaining_amount = max(0, $target_amount - $raised_amount);

/* 8. PREPARE CAUSE IMAGE */
$cause_image_url = 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';

if (!empty($cause['cause_image'])) {
    $image_path = $cause['cause_image'];
    if (strpos($image_path, 'uploads/') === 0) {
        $cause_image_url = baseUrl($image_path);
    } else {
        $cause_image_url = baseUrl('uploads/causes/' . basename($image_path));
    }
}
?>

    <!-- HERO HEADER -->
    <section class="bg-ivory pt-10 pb-8 border-b border-gold/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
            <span class="text-gold font-bold tracking-widest uppercase text-xs">Make a Difference</span>
            <h1 class="mt-3 text-4xl md:text-5xl font-serif text-forest mb-4 leading-tight">Make a Contribution That Creates Change</h1>
            <p class="text-lg text-charcoal-light font-light leading-relaxed">
                Your contribution helps trusted NGOs and communities provide essential support where it is needed most.
            </p>
        </div>
    </section>

    <!-- MAIN CONTRIBUTION SECTION -->
    <div>
        <section class="bg-ivory-dark py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- REVIEW STATE OVERLAY POPUP -->
                <div id="reviewOverlay" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-8 bg-black/60 backdrop-blur-sm" style="display: none;">
                    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl relative border border-forest">
                        <h2 class="text-2xl md:text-3xl font-serif text-forest mb-6">Review Your Contribution</h2>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between border-b border-gold/20 pb-4">
                                <span class="text-charcoal-light font-medium">Type</span>
                                <span id="reviewType" class="text-forest font-bold capitalize"></span>
                            </div>
                            <div id="reviewAmountRow" class="flex justify-between border-b border-gold/20 pb-4">
                                <span class="text-charcoal-light font-medium">Amount</span>
                                <span id="reviewAmount" class="text-forest font-bold font-serif text-xl"></span>
                            </div>
                            <div id="reviewQuantityRow" class="flex justify-between border-b border-gold/20 pb-4" style="display:none;">
                                <span class="text-charcoal-light font-medium">Quantity</span>
                                <span id="reviewQuantity" class="text-forest font-bold"></span>
                            </div>
                            <div id="reviewItemRow" class="flex justify-between border-b border-gold/20 pb-4" style="display:none;">
                                <span class="text-charcoal-light font-medium">Item</span>
                                <span id="reviewItem" class="text-forest font-bold"></span>
                            </div>
                            <div class="flex justify-between border-b border-gold/20 pb-4">
                                <span class="text-charcoal-light font-medium">Supporting</span>
                                <span class="text-charcoal font-medium text-right"><?= $cause['title'] ?></span>
                            </div>
                            <div class="flex justify-between pb-2">
                                <span class="text-charcoal-light font-medium">NGO</span>
                                <span class="text-charcoal font-medium text-right"><?= $ngo['name'] ?></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col md:flex-row gap-4">
                            <button type="button" id="contribBackBtn" class="flex-1 px-4 py-3 bg-ivory text-charcoal hover:bg-gold/20 rounded-xl font-bold transition-colors">Edit</button>
                            <button type="button" id="contribConfirmBtn" class="flex-1 px-4 py-3 bg-forest text-ivory hover:bg-forest/90 rounded-xl font-bold transition-colors shadow-md">Confirm</button>
                        </div>
                    </div>
                </div>

                <!-- ALERTS FEEDBACK -->
                <?php if ($success_message): ?>
                    <div class="bg-green-50 border border-green-300 p-5 rounded-2xl mb-8 text-green-900 flex items-center gap-4 shadow-sm">
                        <svg class="w-7 h-7 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <div class="font-bold text-base">Contribution Submitted Successfully!</div>
                            <div class="text-sm text-green-800"><?= $success_message ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="bg-red-50 border border-red-200 p-5 rounded-2xl mb-8 text-red-800 flex items-center gap-4 shadow-sm">
                        <svg class="w-7 h-7 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <div class="font-bold text-base">Notice</div>
                            <div class="text-sm text-red-700"><?= $error_message ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- MAIN 2-COLUMN GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-12 lg:gap-16 items-start">
                    
                    <!-- LEFT COLUMN -->
                    <div class="w-full space-y-12 min-w-0">
                        
                        <!-- CAUSE INFORMATION -->
                        <div class="bg-white rounded-3xl border border-gold/20 shadow-sm overflow-hidden">
                            <div class="p-8">
                                <span class="text-xs font-bold text-gold uppercase tracking-wider mb-2 block"><?= $cause['category'] ?></span>
                                <h2 class="text-3xl md:text-4xl font-serif text-forest mb-6 break-words" style="word-break: break-word; overflow-wrap: anywhere;"><?= $cause['title'] ?></h2>
                                
                                <div class="w-full aspect-[16/9] md:aspect-[21/9] lg:aspect-[16/9] rounded-2xl overflow-hidden bg-forest/5 relative mb-8">
                                    <img src="<?= $cause_image_url ?>" alt="<?= $cause['title'] ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                                </div>
                                
                                <div class="prose prose-lg text-charcoal-light font-light leading-relaxed max-w-none break-words" style="word-break: break-word; overflow-wrap: anywhere;">
                                    <p class="text-xl text-charcoal font-medium border-l-4 border-gold pl-4 py-1 mb-6 break-words" style="word-break: break-word; overflow-wrap: anywhere;">
                                        <?= !empty($cause['short_description']) ? $cause['short_description'] : 'Help provide essential resources and support to those who need it most in our communities.' ?>
                                    </p>
                                    <p class="break-words" style="word-break: break-word; overflow-wrap: anywhere;">
                                        <?= !empty($cause['detailed_description']) ? nl2br($cause['detailed_description']) : 'Your contribution directly fuels grassroots efforts. We believe that collective action forms the backbone of sustainable community development.' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- NGO SECTION -->
                        <div class="bg-white p-8 rounded-3xl border border-gold/20 shadow-sm min-w-0">
                            <h3 class="text-sm font-bold text-gold uppercase tracking-wider mb-6">Organized By</h3>
                            
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-6">
                                <div class="w-16 h-16 bg-ivory border border-gold/30 rounded-2xl flex items-center justify-center shrink-0 shadow-sm text-3xl font-serif text-forest">
                                    <?= substr($ngo['name'] ?? '', 0, 1) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-serif text-forest text-xl break-words" style="word-break: break-word; overflow-wrap: anywhere;"><?= $ngo['name'] ?></h4>
                                        <?php if (!empty($ngo['verification_status']) && $ngo['verification_status'] === 'approved'): ?>
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-charcoal-light mb-2">
                                        <svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <span><?= (!empty($ngo['city']) && !empty($ngo['state'])) ? $ngo['city'] . ', ' . $ngo['state'] : ($ngo['city'] ?? ($ngo['state'] ?? 'India')) ?></span>
                                    </div>
                                    <p class="text-sm text-charcoal-light leading-relaxed break-words" style="word-break: break-word; overflow-wrap: anywhere;"><?= !empty($ngo['description']) ? $ngo['description'] : 'A dedicated non-profit organization working to create positive community impact through meaningful initiatives.' ?></p>
                                </div>
                            </div>
                            
                            <a href="<?= baseUrl('?page=ngo-profile&id=' . $ngo['id']) ?>" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-white border border-forest text-forest hover:bg-forest hover:text-ivory px-6 py-3 rounded-xl text-sm font-bold transition-all group">
                                View NGO Profile 
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                        
                        <!-- FUNDRAISING PROGRESS -->
                        <div class="bg-white p-8 rounded-3xl border border-gold/20 shadow-sm min-w-0">
                            <h3 class="text-2xl md:text-3xl font-serif text-forest mb-8">Fundraising Progress</h3>
                            
                            <div class="flex flex-wrap justify-between items-end mb-4 gap-4">
                                <div>
                                    <span class="text-3xl md:text-4xl font-serif text-forest block mb-1">₹<?= number_format($raised_amount) ?></span>
                                    <span class="text-xs font-bold text-gold uppercase tracking-wider">Raised</span>
                                </div>
                                <div class="text-center hidden sm:block">
                                    <span class="text-2xl md:text-3xl font-serif text-forest block mb-1"><?= $progress_percentage ?>%</span>
                                    <span class="text-xs font-bold text-gold uppercase tracking-wider">Funded</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl md:text-2xl font-serif text-charcoal-light block mb-1">₹<?= number_format($target_amount) ?></span>
                                    <span class="text-xs font-bold text-charcoal-light uppercase tracking-wider">Goal</span>
                                </div>
                            </div>
                            
                            <div class="w-full bg-ivory rounded-full h-4 mb-4 border border-gold/20 overflow-hidden shadow-inner">
                                <div class="bg-forest h-full rounded-full relative" style="width: <?= min(100, $progress_percentage) ?>%">
                                    <div class="absolute inset-0 bg-white/20 w-full h-full" style="background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent); background-size: 1rem 1rem;"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm font-medium text-charcoal-light">
                                <span class="sm:hidden"><?= $progress_percentage ?>% Funded</span>
                                <span class="ml-auto">₹<?= number_format($remaining_amount) ?> remaining</span>
                            </div>
                        </div>
                        
                    </div>

                    <!-- RIGHT COLUMN: CONTRIBUTION FORM -->
                    <div class="w-full min-w-0">
                        <div class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-lg lg:sticky lg:top-24 z-10">
                            <div class="mb-8">
                                <h2 class="text-3xl md:text-4xl font-serif text-forest mb-2">Make a Contribution</h2>
                                <p class="text-sm text-charcoal-light">Choose how you'd like to support this cause.</p>
                            </div>

                            <!-- INACTIVE CAUSE NOTICE -->
                            <?php if (!$is_valid_cause): ?>
                                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl mb-6 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <div class="text-sm leading-relaxed">
                                        <strong class="font-bold">Contributions Paused:</strong> This cause is currently inactive, completed, or the organizing NGO is pending verification. Contributions are temporarily unavailable.
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- CONTRIBUTION TYPE SELECTION -->
                            <div class="mb-8">
                                <label class="block text-sm font-bold text-charcoal uppercase tracking-wider mb-4">What would you like to contribute?</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php if (in_array('money', $accepted_types)): ?>
                                    <button
                                        type="button"
                                        class="contrib-type-btn py-3 px-4 border rounded-xl font-bold transition-all text-sm capitalize flex items-center justify-center gap-2 relative overflow-hidden"
                                        data-type="money"
                                    >
                                        <span>Money</span>
                                        <div class="contrib-indicator absolute top-1 right-1 w-2 h-2 rounded-full bg-forest" style="display:none;"></div>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('food', $accepted_types)): ?>
                                    <button
                                        type="button"
                                        class="contrib-type-btn py-3 px-4 border rounded-xl font-bold transition-all text-sm capitalize flex items-center justify-center gap-2 relative overflow-hidden"
                                        data-type="food"
                                    >
                                        <span>Food</span>
                                        <div class="contrib-indicator absolute top-1 right-1 w-2 h-2 rounded-full bg-forest" style="display:none;"></div>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('clothes', $accepted_types)): ?>
                                    <button
                                        type="button"
                                        class="contrib-type-btn py-3 px-4 border rounded-xl font-bold transition-all text-sm capitalize flex items-center justify-center gap-2 relative overflow-hidden"
                                        data-type="clothes"
                                    >
                                        <span>Clothes</span>
                                        <div class="contrib-indicator absolute top-1 right-1 w-2 h-2 rounded-full bg-forest" style="display:none;"></div>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('items', $accepted_types)): ?>
                                    <button
                                        type="button"
                                        class="contrib-type-btn py-3 px-4 border rounded-xl font-bold transition-all text-sm capitalize flex items-center justify-center gap-2 relative overflow-hidden"
                                        data-type="items"
                                    >
                                        <span>Useful Items</span>
                                        <div class="contrib-indicator absolute top-1 right-1 w-2 h-2 rounded-full bg-forest" style="display:none;"></div>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <form id="contributionForm" method="POST" class="validation-form" novalidate>
                                <input type="hidden" name="contribution_type" id="contributionType" value="<?= $default_type ?>">
                                
                                <!-- MONEY FIELDS -->
                                <div class="contrib-section contrib-money">
                                    <div class="mb-8">
                                        <label class="block text-sm font-bold text-charcoal uppercase tracking-wider mb-4">Contribution Amount</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                            <?php foreach([500, 1000, 2000, 5000] as $preset): ?>
                                                <button 
                                                    type="button"
                                                    class="contrib-preset-btn py-3 border rounded-xl font-bold transition-all text-sm bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5" 
                                                    data-amount="<?= $preset ?>"
                                                >₹<?= $preset ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                        <div>
                                            <button 
                                                type="button"
                                                id="contribCustomBtn"
                                                class="w-full py-3 px-4 bg-white border border-gold/30 hover:border-forest text-forest rounded-xl transition-all text-left flex items-center mb-3"
                                            >
                                                <div class="flex-1 text-sm font-bold">Other Amount</div>
                                                <div id="contribCustomIndicator" class="w-3 h-3 rounded-full bg-forest" style="display:none;"></div>
                                            </button>
                                            <div id="contribCustomWrapper" style="display:none;">
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-charcoal-light font-medium">₹</span>
                                                    <input 
                                                        type="number" 
                                                        id="contribAmount" name="contribution_amount"
                                                        min="1" 
                                                        placeholder="Enter amount" 
                                                        class="w-full pl-8 pr-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-forest font-medium transition-all"
                                                        data-validate="required numeric">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- FOOD FIELDS -->
                                <div class="contrib-section contrib-food" style="display:none;">
                                    <div class="space-y-5 mb-8">
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Food Type</label>
                                            <select name="food_type" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                <option value="Rice / Grains">Rice / Grains</option>
                                                <option value="Packaged Food">Packaged Food</option>
                                                <option value="Cooked Meals">Cooked Meals</option>
                                                <option value="Groceries">Groceries</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Quantity</label>
                                                <input
                                                    id="contribFoodQuantity"
                                                    name="food_quantity"
                                                    data-validate="required numeric"
                                                    type="number"
                                                    min="1"
                                                    class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all"
                                                    placeholder="10"
                                                >
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Unit</label>
                                                <select id="contribFoodUnit" name="food_unit" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                    <option value="kg">kg</option>
                                                    <option value="packets">packets</option>
                                                    <option value="meals">meals</option>
                                                    <option value="boxes">boxes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Delivery Method</label>
                                            <select name="food_delivery" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                <option value="Drop off at NGO center">I will drop off at NGO center</option>
                                                <option value="Request pickup">Request pickup</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CLOTHES FIELDS -->
                                <div class="contrib-section contrib-clothes" style="display:none;">
                                    <div class="space-y-5 mb-8">
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Clothing Type</label>
                                            <select name="clothing_type" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                <option value="Childrens Clothes">Children's Clothes</option>
                                                <option value="Womens Clothes">Women's Clothes</option>
                                                <option value="Mens Clothes">Men's Clothes</option>
                                                <option value="Blankets">Blankets</option>
                                                <option value="Winter Wear">Winter Wear</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Quantity</label>
                                                <input id="contribClothesQuantity" name="clothes_quantity" type="number" min="1" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="5" data-validate="required numeric">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Condition</label>
                                                <select name="clothing_condition" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                    <option value="New">New</option>
                                                    <option value="Good Condition">Good Condition</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Intended For</label>
                                            <select name="clothing_for" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                <option value="Children">Children</option>
                                                <option value="Adults">Adults</option>
                                                <option value="Elderly">Elderly</option>
                                                <option value="Any">Any</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- USEFUL ITEMS FIELDS -->
                                <div class="contrib-section contrib-items" style="display:none;">
                                    <div class="space-y-5 mb-8">
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Item Name</label>
                                            <input data-validate="required" id="contribItemName" name="item_name" type="text" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="e.g. School Books, Furniture">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Quantity</label>
                                                <input id="contribItemQuantity" name="item_quantity" type="number" min="1" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="1" data-validate="required numeric">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Condition</label>
                                                <select name="item_condition" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all appearance-none cursor-pointer">
                                                    <option value="New">New</option>
                                                    <option value="Good Condition">Good Condition</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Description</label>
                                            <textarea data-validate="required" name="item_description" rows="2" class="w-full px-4 py-3 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all resize-none" placeholder="Describe the item..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CONTRIBUTOR INFORMATION -->
                                <div class="pt-8 border-t border-gold/20 mb-8">
                                    <h3 class="text-xl font-serif text-forest mb-6">Your Information</h3>
                                    
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Full Name</label>
                                            <input name="full_name" type="text" value="<?= $user_name ?>" required class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="Enter your name" data-validate="required alpha">
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Email Address</label>
                                                <input name="email" type="email" value="<?= $user_email ?>" required class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="Enter your email" data-validate="required email">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Phone Number</label>
                                                <input name="phone" type="tel" value="<?= $user_phone ?>" class="w-full px-4 py-3.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all" placeholder="Enter your phone number" data-validate="numeric">
                                            </div>
                                        </div>
                                        
                                        <label class="flex items-center gap-3 cursor-pointer group pt-2">
                                            <div class="relative flex items-center justify-center">
                                                <input name="anonymous" type="checkbox" class="peer sr-only">
                                                <div class="w-5 h-5 border-2 border-gold/50 rounded bg-white peer-checked:bg-forest peer-checked:border-forest transition-colors"></div>
                                                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-sm font-medium text-charcoal group-hover:text-forest transition-colors">Make my contribution anonymous</span>
                                        </label>
                                        
                                        <div class="pt-2">
                                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Message to the NGO (Optional)</label>
                                            <textarea name="message" rows="3" class="w-full px-4 py-3 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all resize-none" placeholder="Write a short message..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- SUMMARY & CTA -->
                                <div class="bg-forest/5 rounded-2xl p-6 md:p-8 mb-6 border border-forest/10">
                                    <h4 class="text-sm font-bold text-forest uppercase tracking-wider mb-4 border-b border-forest/10 pb-2">Contribution Summary</h4>
                                    
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-charcoal-light font-medium">Type</span>
                                        <span class="text-sm font-bold text-charcoal capitalize" id="summaryType"><?= ucfirst($default_type) ?></span>
                                    </div>
                                    
                                    <!-- Dynamic Summary Values -->
                                    <div class="flex justify-between items-center summary-dynamic-row" id="summaryMoneyRow">
                                        <span class="text-sm text-charcoal-light font-medium">Amount</span>
                                        <span class="text-lg font-serif font-bold text-forest" id="summaryAmount">₹1000</span>
                                    </div>

                                    <div class="flex justify-between items-center summary-dynamic-row" id="summaryFoodRow" style="display:none;">
                                        <span class="text-sm text-charcoal-light font-medium">Quantity</span>
                                        <span class="text-sm font-bold text-charcoal" id="summaryFoodQty">-</span>
                                    </div>

                                    <div class="flex justify-between items-center summary-dynamic-row" id="summaryClothesRow" style="display:none;">
                                        <span class="text-sm text-charcoal-light font-medium">Quantity</span>
                                        <span class="text-sm font-bold text-charcoal" id="summaryClothesQty">-</span>
                                    </div>

                                    <div class="flex justify-between items-center summary-dynamic-row" id="summaryItemsRow" style="display:none;">
                                        <span class="text-sm text-charcoal-light font-medium">Item</span>
                                        <span class="text-sm font-bold text-charcoal" id="summaryItemDetails">-</span>
                                    </div>

                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-forest/10">
                                        <span class="text-sm text-charcoal-light font-medium">Supporting</span>
                                        <span class="text-sm font-bold text-charcoal text-right"><?= $ngo['name'] ?></span>
                                    </div>
                                </div>
                                
                                <?php if (!$is_valid_cause): ?>
                                    <button type="button" disabled class="w-full bg-charcoal/20 text-charcoal/50 cursor-not-allowed px-6 py-4 rounded-xl font-bold transition-all shadow-none text-lg flex items-center justify-center gap-2">
                                        Contributions Closed
                                    </button>
                                <?php else: ?>
                                    <button type="submit" id="contribSubmitBtn" class="w-full bg-forest text-ivory hover:bg-forest/90 px-6 py-4 rounded-xl font-bold transition-all shadow-md text-lg flex items-center justify-center gap-2 group">
                                        Continue 
                                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                <?php endif; ?>
                                
                                <div class="mt-6 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs text-charcoal-light font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Verified NGO
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Transparent fundraising
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Multiple ways to contribute
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- HOW YOUR CONTRIBUTION HELPS -->
    <section class="py-16 md:py-24 bg-white border-t border-gold/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-serif text-forest mb-4">How Your Contribution Helps</h2>
                <p class="text-charcoal-light text-lg">Every contribution can create meaningful change in the lives of those who need it.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-ivory border border-gold/20 p-8 rounded-3xl text-center hover:-translate-y-1 hover:shadow-md transition-all">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-forest font-bold font-serif text-xl">₹</div>
                    <h3 class="text-xl font-serif text-forest mb-2">₹500</h3>
                    <p class="text-sm text-charcoal-light leading-relaxed">Can help provide essential educational materials and basic necessities.</p>
                </div>
                
                <div class="bg-ivory border border-gold/20 p-8 rounded-3xl text-center hover:-translate-y-1 hover:shadow-md transition-all">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-forest">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-serif text-forest mb-2">FOOD</h3>
                    <p class="text-sm text-charcoal-light leading-relaxed">Can help provide nutritious meals to families facing hardship.</p>
                </div>
                
                <div class="bg-ivory border border-gold/20 p-8 rounded-3xl text-center hover:-translate-y-1 hover:shadow-md transition-all">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-forest">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="text-xl font-serif text-forest mb-2">CLOTHES</h3>
                    <p class="text-sm text-charcoal-light leading-relaxed">Can help provide warm clothing and basic weather protection.</p>
                </div>
                
                <div class="bg-ivory border border-gold/20 p-8 rounded-3xl text-center hover:-translate-y-1 hover:shadow-md transition-all">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-forest">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-xl font-serif text-forest mb-2">USEFUL ITEMS</h3>
                    <p class="text-sm text-charcoal-light leading-relaxed">Can help communities access practical resources and educational tools.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY KARMASETU -->
    <section class="py-16 md:py-24 bg-forest text-ivory border-t border-gold/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-serif mb-4 text-white">Why Contribute Through KarmaSetu?</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center max-w-5xl mx-auto">
                <div>
                    <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-6 text-gold">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Verified NGOs</h3>
                    <p class="text-ivory/80 font-light leading-relaxed">All listed NGOs undergo a vetting process before being publicly listed to ensure your contribution reaches the right hands.</p>
                </div>
                
                <div>
                    <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-6 text-gold">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Clear Fundraising</h3>
                    <p class="text-ivory/80 font-light leading-relaxed">See precise fundraising goals and real-time progress for campaigns to understand exactly what is needed.</p>
                </div>
                
                <div>
                    <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-6 text-gold">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Multiple Ways to Help</h3>
                    <p class="text-ivory/80 font-light leading-relaxed">Money is not the only way to contribute. Help directly with food, clothes, or other useful items.</p>
                </div>
            </div>
        </div>
    </section>

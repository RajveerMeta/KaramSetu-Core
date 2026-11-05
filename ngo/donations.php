<?php
require_once __DIR__ . '/../includes/functions.php';
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
$allowedTypes = ['all', 'money', 'food', 'clothes', 'items'];
$allowedStatuses = ['all', 'received', 'pending'];

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

        // TOTAL CONTRIBUTIONS
        $totalQuery = mysqli_query(
            $con,
            "SELECT COUNT(*) AS total
             FROM contributions
             WHERE ngo_id = $ngo_id
             AND NOT (
                (contribution_type = 'money' AND payment_status = 'failed')
                OR
                (contribution_type != 'money' AND fulfillment_status = 'cancelled')
             )"
        );

        if ($totalQuery) {
            $totalData = mysqli_fetch_assoc($totalQuery);
            $totalContributions = (int) $totalData['total'];
        }

        // TOTAL MONEY RECEIVED
        $moneyQuery = mysqli_query(
            $con,
            "SELECT COALESCE(SUM(monetary_amount), 0) AS total_money
             FROM contributions
             WHERE ngo_id = $ngo_id
             AND contribution_type = 'money'
             AND payment_status = 'completed'"
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

        if ($typeFilter != 'all') {
            $filterCondition .= " AND c.contribution_type = '$typeFilter'";
        }

        if ($statusFilter == 'received') {

            $filterCondition .= "
                AND (
                    (c.contribution_type = 'money'
                     AND c.payment_status = 'completed')
                    OR
                    (c.contribution_type != 'money'
                     AND c.fulfillment_status IN ('received', 'completed'))
                )";

        } elseif ($statusFilter == 'pending') {

            $filterCondition .= "
                AND (
                    (c.contribution_type = 'money'
                     AND c.payment_status = 'pending')
                    OR
                    (c.contribution_type != 'money'
                     AND c.fulfillment_status = 'pending')
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
    'received' => 'bg-forest/10 text-forest border border-forest/20',
    'completed' => 'bg-forest/10 text-forest border border-forest/20',
    'pending' => 'bg-gold/15 text-charcoal border border-gold/30',
    'failed' => 'bg-charcoal/10 text-charcoal border border-charcoal/20',
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
                                    All
                                </option>

                                <option value="money" <?= $typeFilter == 'money' ? 'selected' : '' ?>>
                                    Money
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
                                    All
                                </option>

                                <option value="received" <?= $statusFilter == 'received' ? 'selected' : '' ?>>
                                    Received
                                </option>

                                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>
                                    Pending
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
                                href="<?= baseUrl('ngo/contributions.php') ?>"
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
                                            $donorName = $contribution['donor_name'];
                                        }

                                        // AMOUNT AND STATUS
                                        if ($type == 'money') {

                                            $status = $contribution['payment_status'];

                                            if ($status == 'completed') {
                                                $statusLabel = 'Received';
                                            } elseif ($status == 'not_applicable') {
                                                $statusLabel = 'Not Applicable';
                                            } else {
                                                $statusLabel = ucfirst($status);
                                            }

                                            $amount = '₹' . number_format(
                                                (float) $contribution['monetary_amount'],
                                                2
                                            );

                                        } else {

                                            $status = $contribution['fulfillment_status'];

                                            if ($status == 'received' || $status == 'completed') {
                                                $statusLabel = 'Received';
                                            } else {
                                                $statusLabel = ucfirst($status);
                                            }

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
                                                    <?= $donorName ?>
                                                </div>

                                                <div class="text-xs text-charcoal-light mt-1">
                                                    <?= $contribution['contribution_reference'] ?>
                                                </div>

                                                <?php if (!empty($contribution['cause_title'])): ?>

                                                    <div class="text-xs text-charcoal-light mt-1">
                                                        Cause: <?= $contribution['cause_title'] ?>
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
                                                <?= $amount ?>
                                            </td>

                                            <!-- STATUS -->
                                            <td class="py-4">

                                                <span class="inline-block px-3 py-1 <?= $statusClass ?> text-xs font-bold rounded-full">
                                                    <?= $statusLabel ?>
                                                </span>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="5" class="text-center py-12 text-charcoal-light">

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

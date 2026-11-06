<?php
require_once __DIR__ . '/../Database/db_config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . baseUrl('auth/login.php'));
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Summary metrics for this user
$summaryQuery = "SELECT 
                    COUNT(*) AS total_count,
                    COALESCE(SUM(CASE WHEN contribution_type = 'money' AND payment_status = 'completed' THEN monetary_amount ELSE 0 END), 0) AS total_money,
                    COALESCE(SUM(CASE WHEN contribution_type != 'money' THEN 1 ELSE 0 END), 0) AS total_resources
                 FROM contributions 
                 WHERE user_id = $user_id";
$summaryRes = mysqli_query($con, $summaryQuery);
$summaryRow = $summaryRes ? mysqli_fetch_assoc($summaryRes) : ['total_count' => 0, 'total_money' => 0, 'total_resources' => 0];

$summaries = (object)[
    'total_contributions' => '₹' . number_format((float)$summaryRow['total_money']),
    'total_count' => (int)$summaryRow['total_count'],
    'money_donated' => '₹' . number_format((float)$summaryRow['total_money']),
    'resources_donated' => (int)$summaryRow['total_resources']
];

// Filters
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, trim($_GET['search'])) : '';
$type_filter = isset($_GET['type']) ? mysqli_real_escape_string($con, trim($_GET['type'])) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, trim($_GET['status'])) : '';

$where = ["co.user_id = $user_id"];

if (!empty($search)) {
    $where[] = "(c.title LIKE '%$search%' OR n.name LIKE '%$search%' OR co.contribution_reference LIKE '%$search%')";
}

if (!empty($type_filter)) {
    $where[] = "co.contribution_type = '$type_filter'";
}

if (!empty($status_filter)) {
    if ($status_filter === 'completed') {
        $where[] = "(co.payment_status = 'completed' OR co.fulfillment_status = 'completed')";
    } elseif ($status_filter === 'pending') {
        $where[] = "(co.payment_status = 'pending' OR co.fulfillment_status = 'pending')";
    } elseif ($status_filter === 'cancelled') {
        $where[] = "(co.payment_status = 'failed' OR co.fulfillment_status = 'cancelled')";
    }
}

$whereClause = implode(" AND ", $where);

$contribQuery = "SELECT co.*, n.name AS ngo_name, n.id AS ngo_id, c.title AS cause_title
                 FROM contributions co
                 INNER JOIN ngos n ON n.id = co.ngo_id
                 LEFT JOIN causes c ON c.id = co.cause_id
                 WHERE $whereClause
                 ORDER BY co.created_at DESC";
$contribResult = mysqli_query($con, $contribQuery);

$contributions = [];
if ($contribResult) {
    while ($row = mysqli_fetch_assoc($contribResult)) {
        $displayStatus = 'Pending';
        if ($row['contribution_type'] === 'money') {
            $displayStatus = ($row['payment_status'] === 'completed') ? 'Completed' : ucfirst($row['payment_status']);
        } else {
            $displayStatus = ($row['fulfillment_status'] === 'completed' || $row['fulfillment_status'] === 'received') ? 'Completed' : ucfirst($row['fulfillment_status']);
        }

        $typeDisplay = ucfirst($row['contribution_type'] === 'items' ? 'Useful Items' : $row['contribution_type']);
        $amountDisplay = ($row['contribution_type'] === 'money' && $row['monetary_amount'] !== null) ? ('₹' . number_format($row['monetary_amount'])) : '—';
        $titleDisplay = !empty($row['cause_title']) ? $row['cause_title'] : ('Donation to ' . $row['ngo_name']);

        $contributions[] = (object)[
            'id' => (int)$row['id'],
            'reference' => $row['contribution_reference'],
            'title' => $titleDisplay,
            'ngo' => $row['ngo_name'],
            'type' => $typeDisplay,
            'amount' => $amountDisplay,
            'date' => date('d M Y', strtotime($row['created_at'])),
            'status' => $displayStatus
        ];
    }
}

$hasContributions = count($contributions) > 0 || !empty($search) || !empty($type_filter) || !empty($status_filter);

ob_start();
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-2">My Contributions</h1>
            <p class="text-charcoal-light">Track your donations and see the difference you've made through KarmaSetu.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-10">
            <!-- Card 1 -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center relative overflow-hidden transition-shadow hover:shadow-md">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-gold/10 rounded-full blur-xl pointer-events-none"></div>
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1 relative z-10">Total Donated</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1 relative z-10"><?= $summaries->total_contributions ?></p>
                <p class="text-xs font-medium text-charcoal-light relative z-10">Across <?= $summaries->total_count ?> contributions</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Money Donated</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= $summaries->money_donated ?></p>
                <p class="text-xs font-medium text-charcoal-light">Financial contributions</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Resources Donated</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= $summaries->resources_donated ?></p>
                <p class="text-xs font-medium text-charcoal-light">Food, clothes & useful items</p>
            </div>
        </div>

        <?php if ($hasContributions): ?>
            <!-- Main Content Area -->
            <div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden">
                
                <!-- Filter / Search Area -->
                <form method="GET" action="" class="p-5 md:p-8 md:p-6 border-b border-charcoal/5 bg-ivory/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-charcoal-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search by cause, NGO, or reference..." class="block w-full pl-10 pr-3 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm placeholder-charcoal-light/60 focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest transition-colors shadow-sm">
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select name="type" onchange="this.form.submit()" class="block w-full sm:w-auto px-4 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm text-charcoal font-medium focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest shadow-sm cursor-pointer">
                            <option value="">All Types</option>
                            <option value="money" <?= (isset($_GET['type']) && $_GET['type'] == 'money') ? 'selected' : '' ?>>Money</option>
                            <option value="food" <?= (isset($_GET['type']) && $_GET['type'] == 'food') ? 'selected' : '' ?>>Food</option>
                            <option value="clothes" <?= (isset($_GET['type']) && $_GET['type'] == 'clothes') ? 'selected' : '' ?>>Clothes</option>
                            <option value="items" <?= (isset($_GET['type']) && $_GET['type'] == 'items') ? 'selected' : '' ?>>Useful Items</option>
                        </select>
                        <select name="status" onchange="this.form.submit()" class="block w-full sm:w-auto px-4 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm text-charcoal font-medium focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest shadow-sm cursor-pointer">
                            <option value="">All Status</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-forest text-ivory text-sm font-bold rounded-lg hover:bg-forest/90 transition-all shadow-sm">Filter</button>
                        <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['status'])): ?>
                            <a href="<?= baseUrl('user/contributions.php') ?>" class="px-4 py-2.5 bg-white border border-charcoal/20 text-charcoal text-sm font-bold rounded-lg hover:bg-ivory transition-all text-center">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (count($contributions) > 0): ?>
                    <!-- Desktop Table (Hidden on Mobile) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="text-xs text-charcoal-light uppercase tracking-wider border-b border-charcoal/10 bg-ivory/30">
                                    <th class="px-6 py-4 font-bold text-forest">Contribution</th>
                                    <th class="px-6 py-4 font-bold text-forest">NGO / Organization</th>
                                    <th class="px-6 py-4 font-bold text-forest">Type</th>
                                    <th class="px-6 py-4 font-bold text-forest">Amount</th>
                                    <th class="px-6 py-4 font-bold text-forest">Date</th>
                                    <th class="px-6 py-4 font-bold text-forest">Status</th>
                                    <th class="px-6 py-4 font-bold text-forest text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-charcoal/5">
                                <?php foreach ($contributions as $contribution): ?>
                                <tr class="hover:bg-ivory/20 transition-colors">
                                    <td class="px-6 py-4 font-medium text-charcoal">
                                        <div><?= $contribution->title ?></div>
                                        <div class="text-xs text-charcoal-light font-mono"><?= $contribution->reference ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-charcoal-light"><?= $contribution->ngo ?></td>
                                    <td class="px-6 py-4 text-charcoal-light"><?= $contribution->type ?></td>
                                    <td class="px-6 py-4 text-charcoal font-bold"><?= $contribution->amount ?></td>
                                    <td class="px-6 py-4 text-charcoal-light"><?= $contribution->date ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($contribution->status === 'Completed'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200"><?= $contribution->status ?></span>
                                        <?php elseif ($contribution->status === 'Pending'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-yellow-50 text-yellow-700 border border-yellow-200"><?= $contribution->status ?></span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-200"><?= $contribution->status ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= baseUrl('user/contribution-details.php?id=' . (int)$contribution->id) ?>" class="inline-flex items-center justify-center px-4 py-2 border border-charcoal/10 text-xs font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile & Tablet Responsive List (Hidden on Desktop) -->
                    <div class="lg:hidden divide-y divide-charcoal/5">
                        <?php foreach ($contributions as $contribution): ?>
                        <div class="p-5 md:p-8 hover:bg-ivory/20 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="pr-2">
                                    <h3 class="font-bold text-charcoal text-base mb-1"><?= $contribution->title ?></h3>
                                    <p class="text-sm text-forest font-medium"><?= $contribution->ngo ?></p>
                                    <p class="text-xs text-charcoal-light font-mono"><?= $contribution->reference ?></p>
                                </div>
                                <div class="flex-shrink-0 mt-1">
                                    <?php if ($contribution->status === 'Completed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200"><?= $contribution->status ?></span>
                                    <?php elseif ($contribution->status === 'Pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-yellow-50 text-yellow-700 border border-yellow-200"><?= $contribution->status ?></span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-200"><?= $contribution->status ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 my-4 py-3 border-y border-charcoal/5">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Type</p>
                                    <p class="text-sm font-medium text-charcoal"><?= $contribution->type ?></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Date</p>
                                    <p class="text-sm font-medium text-charcoal"><?= $contribution->date ?></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Amount</p>
                                    <p class="text-sm font-bold text-charcoal"><?= $contribution->amount ?></p>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <a href="<?= baseUrl('user/contribution-details.php?id=' . (int)$contribution->id) ?>" class="block w-full text-center px-4 py-2.5 border border-charcoal/10 text-sm font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-12 text-center">
                        <p class="text-charcoal-light text-base mb-4">No contributions matched your search criteria.</p>
                        <a href="<?= baseUrl('user/contributions.php') ?>" class="text-sm font-bold text-forest underline">Clear all filters</a>
                    </div>
                <?php endif; ?>

            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl p-10 md:p-16 shadow-sm border border-gold/20 text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="h-20 w-20 bg-forest/5 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-forest/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
                <h3 class="text-2xl md:text-3xl font-serif font-bold text-forest mb-2">No Contributions Yet</h3>
                <p class="text-charcoal-light max-w-md mx-auto mb-8">Your contributions will appear here once you make your first donation. Start making an impact today!</p>
                <a href="<?= baseUrl('?page=causes') ?>" class="inline-flex justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-md text-white bg-forest hover:bg-forest/90 transition-all hover:shadow-lg">
                    Explore Causes
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>

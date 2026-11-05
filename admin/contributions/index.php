
<?php
require_once __DIR__ . '/../../DataBase/admin/contributions/index.php';

ob_start();
?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">
                Contributions Management
            </h1>
            <p class="text-charcoal-light max-w-2xl">
                Review donations, resources, and contributions across the platform.
            </p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= e($totalContributions) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Money Contributions</p>
            <p class="text-2xl font-bold text-forest mt-1">
                ₹<?= e(number_format($moneyTotal, 2)) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Items & Resources</p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= e($itemTotal) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Pending Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= e((int) $statusCounts['pending_total']) ?>
            </p>
        </div>

    </div>

    <!-- Filters -->
    <form method="GET" action="" class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">

            <div class="md:col-span-2 lg:col-span-4">
                <label for="searchContribution" class="block text-xs font-medium text-charcoal-light mb-1">
                    Search
                </label>
                <input
                    type="text"
                    name="search"
                    id="searchContribution"
                    value="<?= e($search) ?>"
                    placeholder="Search contributor, NGO, or contribution..."
                    class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm">
            </div>

            <div class="lg:col-span-2">
                <label for="filterType" class="block text-xs font-medium text-charcoal-light mb-1">
                    Contribution Type
                </label>
                <select name="type" id="filterType"
                        class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Types</option>
                    <option value="money" <?= $type == 'money' ? 'selected' : '' ?>>Money</option>
                    <option value="food" <?= $type == 'food' ? 'selected' : '' ?>>Food</option>
                    <option value="clothes" <?= $type == 'clothes' ? 'selected' : '' ?>>Clothes</option>
                    <option value="items" <?= $type == 'items' ? 'selected' : '' ?>>Items</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="filterStatus" class="block text-xs font-medium text-charcoal-light mb-1">
                    Status
                </label>
                <select name="status" id="filterStatus"
                        class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled / Failed</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="filterDate" class="block text-xs font-medium text-charcoal-light mb-1">
                    Date
                </label>
                <select name="date" id="filterDate"
                        class="w-full px-4 py-2 border border-charcoal/20 rounded-md focus:outline-none focus:ring-1 focus:ring-forest text-sm bg-white">
                    <option value="">All Dates</option>
                    <option value="today" <?= $dateFilter == 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $dateFilter == 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $dateFilter == 'month' ? 'selected' : '' ?>>This Month</option>
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-2 flex space-x-2">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-forest text-white text-sm font-medium rounded-md hover:bg-forest-dark transition-colors">
                    Search
                </button>

                <a href="<?= baseUrl('admin/contributions/index.php') ?>"
                   class="flex-1 text-center px-4 py-2 bg-ivory-dark text-charcoal text-sm font-medium rounded-md hover:bg-gold/20 border border-charcoal/10 transition-colors">
                    Clear
                </a>
            </div>

        </div>
    </form>

    <!-- Contributions Table -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">

        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
            <h3 class="text-lg font-serif font-bold text-forest">Recent Contributions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[150px]">Contributor</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[200px]">Contribution</th>
                        <th class="px-4 py-4 font-medium">Type</th>
                        <th class="px-4 lg:px-6 py-4 font-medium min-w-[180px]">NGO / Cause</th>
                        <th class="px-4 py-4 font-medium min-w-[120px]">Date</th>
                        <th class="px-4 py-4 font-medium min-w-[150px]">Amount / Details</th>
                        <th class="px-4 py-4 font-medium">Status</th>
                        <th class="px-4 lg:px-6 py-4 font-medium text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">

                    <?php if ($result && mysqli_num_rows($result) > 0): ?>

                        <?php while ($contrib = mysqli_fetch_assoc($result)): ?>

                            <?php
                            $typeLabel = contributionTypeLabel($contrib['contribution_type']);

                            $typeClass = 'bg-forest/10 text-forest border-forest/20';

                            $contributionStatus = contributionStatus(
                                $contrib['payment_status'],
                                $contrib['fulfillment_status']
                            );

                            $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';

                            if ($contributionStatus == 'Completed') {
                                $statusClass = 'bg-green-100 text-green-800 border-green-200';
                            } elseif ($contributionStatus == 'Cancelled') {
                                $statusClass = 'bg-red-100 text-red-800 border-red-200';
                            }

                            $contributionName = $contrib['cause_title'];

                            if (empty($contributionName)) {
                                $contributionName = $typeLabel . ' Contribution';
                            }

                            $ngoName = $contrib['ngo_name'];

                            if (empty($ngoName)) {
                                $ngoName = 'Not specified';
                            }

                            $amountDetails = 'Not specified';

                            if ($contrib['contribution_type'] == 'money') {
                                $amountDetails = '₹' . number_format((float) $contrib['monetary_amount'], 2);
                            } elseif (!empty($contrib['monetary_amount']) && (float) $contrib['monetary_amount'] > 0) {
                                $amountDetails = '₹' . number_format((float) $contrib['monetary_amount'], 2);
                            } else {
                                $amountDetails = $typeLabel . ' donation';
                            }

                            $actionLink = baseUrl('admin/contributions/view.php?id=' . (int) $contrib['id']);
                            ?>

                            <tr class="hover:bg-ivory/30 transition-colors">

                                <td class="px-4 lg:px-6 py-4">
                                    <div class="font-medium text-charcoal">
                                        <?= e($contrib['donor_name']) ?>
                                    </div>
                                    <div class="text-xs text-charcoal-light mt-1">
                                        <?= e($contrib['donor_email']) ?>
                                    </div>
                                </td>

                                <td class="px-4 lg:px-6 py-4">
                                    <div class="text-sm font-medium text-charcoal">
                                        <?= e($contributionName) ?>
                                    </div>
                                    <div class="text-xs text-charcoal-light mt-1">
                                        <?= e($contrib['contribution_reference']) ?>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border <?= $typeClass ?>">
                                        <?= e($typeLabel) ?>
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 text-sm text-charcoal-light">
                                    <div><?= e($ngoName) ?></div>
                                    <?php if (!empty($contrib['cause_title'])): ?>
                                        <div class="text-xs mt-1"><?= e($contrib['cause_title']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-4 text-sm text-charcoal-light">
                                    <?= e(date('d M Y', strtotime($contrib['created_at']))) ?>
                                </td>

                                <td class="px-4 py-4 text-sm font-medium <?= $contrib['contribution_type'] == 'money' ? 'text-forest' : 'text-charcoal' ?>">
                                    <?= e($amountDetails) ?>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                        <?= e($contributionStatus) ?>
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 text-sm text-right">
                                    <a href="<?= $actionLink ?>"
                                       class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md transition-colors">
                                        View
                                    </a>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <h3 class="text-lg font-medium text-charcoal">No contributions found.</h3>
                                <p class="text-sm text-charcoal-light mt-1">
                                    Try adjusting your search or filters.
                                </p>
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php
    $startRecord = 0;
    $endRecord = 0;

    if ($filteredTotal > 0) {
        $startRecord = $offset + 1;
        $endRecord = $offset + $limit;

        if ($endRecord > $filteredTotal) {
            $endRecord = $filteredTotal;
        }
    }
    ?>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 pb-6">

        <p class="text-sm text-charcoal-light">
            Showing
            <span class="font-medium text-charcoal"><?= e($startRecord) ?></span>
            to
            <span class="font-medium text-charcoal"><?= e($endRecord) ?></span>
            of
            <span class="font-medium text-charcoal"><?= e($filteredTotal) ?></span>
            contributions
        </p>

        <?php if ($totalPages > 1): ?>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">

                <?php if ($page > 1): ?>
                    <?php
                    $queryParams['page'] = $page - 1;
                    $previousLink = '?' . http_build_query($queryParams);
                    ?>
                    <a href="<?= e($previousLink) ?>"
                       class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal hover:bg-ivory">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $queryParams['page'] = $i;
                    $pageLink = '?' . http_build_query($queryParams);
                    ?>

                    <a href="<?= e($pageLink) ?>"
                       class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i == $page ? 'bg-forest/5 border-forest text-forest' : 'bg-white border-charcoal/20 text-charcoal-light hover:bg-ivory' ?>">
                        <?= e($i) ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <?php
                    $queryParams['page'] = $page + 1;
                    $nextLink = '?' . http_build_query($queryParams);
                    ?>
                    <a href="<?= e($nextLink) ?>"
                       class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-charcoal/20 bg-white text-sm font-medium text-charcoal hover:bg-ivory">
                        Next
                    </a>
                <?php endif; ?>

            </nav>
        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

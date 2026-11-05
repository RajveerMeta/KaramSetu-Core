
<?php
require_once __DIR__ . '/../../DataBase/admin/reports/index.php';
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8">

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-serif font-bold text-forest mb-2">Reports</h1>
        <p class="text-charcoal-light max-w-2xl">
            View platform activity, contribution trends, and community engagement summaries.
        </p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <?php
        $overviewCards = [
            ['title' => 'Total Users', 'value' => $overview['users'], 'description' => 'Registered community members'],
            ['title' => 'Total NGOs', 'value' => $overview['ngos'], 'description' => 'Registered organizations'],
            ['title' => 'Total Causes', 'value' => $overview['causes'], 'description' => 'Causes on the platform'],
            ['title' => 'Total Events', 'value' => $overview['events'], 'description' => 'Community events']
        ];
        ?>

        <?php foreach ($overviewCards as $card): ?>
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
                <p class="text-sm font-bold text-charcoal"><?= e($card['title']) ?></p>
                <p class="text-3xl font-bold text-forest my-2"><?= e($card['value']) ?></p>
                <p class="text-xs font-medium text-charcoal-light"><?= e($card['description']) ?></p>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- Contribution Summary -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">Contribution Summary</h3>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-charcoal/10">

            <div class="pt-4 sm:pt-0 sm:px-4 first:pt-0 first:px-0">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Money Contributions</p>
                <p class="text-2xl font-bold text-forest">
                    ₹<?= e(number_format((float) $contribution_summary['money'], 2)) ?>
                </p>
            </div>

            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Food Contributions</p>
                <p class="text-2xl font-bold text-forest"><?= e($contribution_summary['food']) ?></p>
            </div>

            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Clothing Contributions</p>
                <p class="text-2xl font-bold text-forest"><?= e($contribution_summary['clothes']) ?></p>
            </div>

            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Item Contributions</p>
                <p class="text-2xl font-bold text-forest"><?= e($contribution_summary['items']) ?></p>
            </div>

        </div>
    </div>

    <!-- Contribution Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- Weekly Contribution Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Weekly Money Contributions</h3>
            </div>

            <div class="p-6 flex-grow flex flex-col justify-center space-y-5">

                <?php foreach ($weekly_activity as $week): ?>
                    <?php
                    if ($max_weekly > 0) {
                        $pct = ($week['amount'] / $max_weekly) * 100;
                    } else {
                        $pct = 0;
                    }
                    ?>

                    <div>
                        <div class="flex justify-between items-end mb-1 gap-4">
                            <span class="text-sm font-medium text-charcoal"><?= e($week['week']) ?></span>
                            <span class="text-sm font-bold text-forest">
                                ₹<?= e(number_format($week['amount'], 2)) ?>
                            </span>
                        </div>

                        <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10">
                            <div class="bg-forest h-3 rounded-full"
                                 style="width: <?= e($pct) ?>%"></div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>

        <!-- Contribution Types -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                <h3 class="text-lg font-serif font-bold text-forest">Contribution Types</h3>
                <span class="text-xs font-medium text-charcoal-light">
                    Total: <?= e($total_contributions) ?>
                </span>
            </div>

            <div class="p-6 flex-grow flex flex-col justify-center space-y-5">

                <?php foreach ($contribution_types as $type): ?>
                    <?php
                    if ($total_contributions > 0) {
                        $percent = ($type['count'] / $total_contributions) * 100;
                    } else {
                        $percent = 0;
                    }
                    ?>

                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-medium text-charcoal">
                                <?= e($type['type']) ?>
                                <span class="text-xs text-charcoal-light ml-1">(<?= e($type['count']) ?>)</span>
                            </span>
                            <span class="text-sm font-bold text-forest">
                                <?= e(round($percent, 1)) ?>%
                            </span>
                        </div>

                        <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10">
                            <div class="bg-forest h-3 rounded-full"
                                 style="width: <?= e($percent) ?>%"></div>
                        </div>
                    </div>

                <?php endforeach; ?>

                <?php if (empty($contribution_types)): ?>
                    <p class="text-sm text-charcoal-light text-center">No contributions found.</p>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- NGO and Event Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- NGO Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">NGO Activity</h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">

                    <?php
                    $ngoCards = [
                        ['label' => 'Approved NGOs', 'value' => $ngo_activity['approved']],
                        ['label' => 'Pending Approval', 'value' => $ngo_activity['pending']],
                        ['label' => 'Suspended NGOs', 'value' => $ngo_activity['suspended']],
                        ['label' => 'Total NGOs', 'value' => $ngo_activity['total']]
                    ];
                    ?>

                    <?php foreach ($ngoCards as $card): ?>
                        <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                            <span class="text-2xl font-bold text-forest"><?= e($card['value']) ?></span>
                            <span class="text-xs font-medium text-charcoal-light mt-1"><?= e($card['label']) ?></span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <!-- Event Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Event Activity</h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">

                    <?php
                    $eventCards = [
                        ['label' => 'Upcoming', 'value' => $event_activity['upcoming']],
                        ['label' => 'Completed', 'value' => $event_activity['completed']],
                        ['label' => 'Cancelled', 'value' => $event_activity['cancelled']],
                        ['label' => 'Total Events', 'value' => $event_activity['total']]
                    ];
                    ?>

                    <?php foreach ($eventCards as $card): ?>
                        <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                            <span class="text-2xl font-bold text-forest"><?= e($card['value']) ?></span>
                            <span class="text-xs font-medium text-charcoal-light mt-1"><?= e($card['label']) ?></span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    </div>

    <!-- Top Contributors -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">Top Contributors</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-4 font-medium w-24">Rank</th>
                        <th class="px-6 py-4 font-medium">Contributor</th>
                        <th class="px-6 py-4 font-medium">Contributions</th>
                        <th class="px-6 py-4 font-medium">Total Amount</th>
                        <th class="px-6 py-4 font-medium">Points</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">

                    <?php foreach ($top_contributors as $user): ?>
                        <tr class="hover:bg-ivory/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-forest/10 text-forest text-xs font-bold">
                                    <?= e($user['rank']) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                <?= e($user['name']) ?>
                            </td>

                            <td class="px-6 py-4 text-sm text-charcoal-light">
                                <?= e($user['count']) ?>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-forest">
                                ₹<?= e(number_format((float) $user['amount'], 2)) ?>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-forest">
                                <?= e($user['points']) ?> pts
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($top_contributors)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-charcoal-light">
                                No contributor records found.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Most Active NGOs -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">Most Active NGOs</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-4 font-medium">NGO</th>
                        <th class="px-6 py-4 font-medium text-center">Events</th>
                        <th class="px-6 py-4 font-medium text-center">Causes</th>
                        <th class="px-6 py-4 font-medium text-right">Contributions Received</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">

                    <?php foreach ($top_ngos as $ngo): ?>
                        <tr class="hover:bg-ivory/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                <?= e($ngo['name']) ?>
                            </td>

                            <td class="px-6 py-4 text-sm text-charcoal-light text-center">
                                <?= e($ngo['events']) ?>
                            </td>

                            <td class="px-6 py-4 text-sm text-charcoal-light text-center">
                                <?= e($ngo['causes']) ?>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-forest text-right">
                                ₹<?= e(number_format((float) $ngo['received'], 2)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($top_ngos)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-charcoal-light">
                                No NGO records found.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Period -->
    <div class="text-center pt-2 pb-4">
        <p class="text-xs text-charcoal-light">
            <span class="font-medium text-charcoal">Report period: Last four weeks</span><br>
            Report values are calculated from the current database.
        </p>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

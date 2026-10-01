<?php
require_once __DIR__ . '/../../includes/functions.php';

$overview = [
    'users' => 128,
    'ngos' => 18,
    'causes' => 12,
    'events' => 15
];

$contribution_summary = [
    'money' => '₹52,500',
    'resource' => 12,
    'item' => 8,
    'time' => '46 hrs'
];

$weekly_activity = [
    ['week' => 'Week 1', 'amount' => 8500, 'label' => '₹8,500'],
    ['week' => 'Week 2', 'amount' => 14000, 'label' => '₹14,000'],
    ['week' => 'Week 3', 'amount' => 17500, 'label' => '₹17,500'],
    ['week' => 'Week 4', 'amount' => 12500, 'label' => '₹12,500'],
];
$max_weekly = 17500;

$contribution_types = [
    ['type' => 'Money', 'count' => 10, 'percent' => 42],
    ['type' => 'Resource', 'count' => 4, 'percent' => 17],
    ['type' => 'Item', 'count' => 6, 'percent' => 25],
    ['type' => 'Time', 'count' => 4, 'percent' => 16],
];
$total_contributions = 24;

$ngo_activity = [
    'approved' => 15,
    'pending' => 2,
    'suspended' => 1,
    'total' => 18
];

$event_activity = [
    'upcoming' => 8,
    'completed' => 6,
    'suspended' => 1,
    'total' => 15
];

$top_contributors = [
    ['rank' => 1, 'name' => 'Rajveer Meta', 'count' => 8, 'amount' => '₹18,500', 'points' => '1,280 pts'],
    ['rank' => 2, 'name' => 'Aarav Shah', 'count' => 6, 'amount' => '₹12,000', 'points' => '980 pts'],
    ['rank' => 3, 'name' => 'Ananya Patel', 'count' => 5, 'amount' => '₹9,500', 'points' => '860 pts'],
    ['rank' => 4, 'name' => 'Dev Mehta', 'count' => 3, 'amount' => '₹7,500', 'points' => '720 pts'],
    ['rank' => 5, 'name' => 'Krisha Joshi', 'count' => 2, 'amount' => '₹5,000', 'points' => '610 pts'],
];

$top_ngos = [
    ['name' => 'Seva Roots Initiative', 'events' => 4, 'causes' => 3, 'received' => '₹22,500'],
    ['name' => 'Sarthak Seva Foundation', 'events' => 3, 'causes' => 2, 'received' => '₹10,000'],
    ['name' => 'Helping Hands Foundation', 'events' => 3, 'causes' => 2, 'received' => '₹8,500'],
    ['name' => 'Nayi Disha Welfare Trust', 'events' => 2, 'causes' => 2, 'received' => '₹6,500'],
    ['name' => 'Asha Community Foundation', 'events' => 2, 'causes' => 1, 'received' => '₹5,000'],
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-serif font-bold text-forest mb-2">Reports</h1>
            <p class="text-charcoal-light max-w-2xl">View platform activity, contribution trends, and community engagement summaries.</p>
        </div>
        <div class="mt-4 md:mt-0 bg-ivory border border-gold/30 px-4 py-2 rounded-lg shadow-sm flex items-center justify-between gap-3">
            <label for="reportPeriod" class="text-xs font-medium text-charcoal-light uppercase tracking-wide">Report Period</label>
            <select id="reportPeriod" class="text-sm font-medium text-forest bg-transparent border-none focus:ring-0 cursor-pointer outline-none">
                <option value="sep2026">September 2026</option>
                <option value="aug2026">August 2026</option>
                <option value="jul2026">July 2026</option>
            </select>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-bold text-charcoal">Total Users</p>
            <p class="text-3xl font-bold text-forest my-2"><?= e($overview['users']) ?></p>
            <p class="text-xs font-medium text-charcoal-light">Registered community members</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-bold text-charcoal">Total NGOs</p>
            <p class="text-3xl font-bold text-forest my-2"><?= e($overview['ngos']) ?></p>
            <p class="text-xs font-medium text-charcoal-light">Approved organizations</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-bold text-charcoal">Total Causes</p>
            <p class="text-3xl font-bold text-forest my-2"><?= e($overview['causes']) ?></p>
            <p class="text-xs font-medium text-charcoal-light">Active and completed causes</p>
        </div>
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-bold text-charcoal">Total Events</p>
            <p class="text-3xl font-bold text-forest my-2"><?= e($overview['events']) ?></p>
            <p class="text-xs font-medium text-charcoal-light">Community events</p>
        </div>
    </div>

    <!-- Contribution Summary -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">Contribution Summary</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-charcoal/10">
            <div class="pt-4 sm:pt-0 sm:px-4 first:pt-0 first:px-0">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Money Contributions</p>
                <p class="text-2xl font-bold text-forest"><?= e($contribution_summary['money']) ?></p>
            </div>
            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Resource Contributions</p>
                <p class="text-2xl font-bold text-charcoal"><?= e($contribution_summary['resource']) ?></p>
            </div>
            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Item Contributions</p>
                <p class="text-2xl font-bold text-charcoal"><?= e($contribution_summary['item']) ?></p>
            </div>
            <div class="pt-4 sm:pt-0 sm:px-4">
                <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Volunteer Hours</p>
                <p class="text-2xl font-bold text-charcoal"><?= e($contribution_summary['time']) ?></p>
            </div>
        </div>
    </div>

    <!-- Analytics Two-Column -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <!-- Contribution Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Contribution Activity</h3>
            </div>
            <div class="p-6 flex-grow flex flex-col justify-center space-y-5">
                <?php foreach ($weekly_activity as $week): ?>
                <?php 
                    $pct = ($week['amount'] / $max_weekly) * 100; 
                ?>
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-sm font-medium text-charcoal"><?= e($week['week']) ?></span>
                        <span class="text-sm font-bold text-forest"><?= e($week['label']) ?></span>
                    </div>
                    <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10">
                        <div class="bg-forest h-3 rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contribution Types -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                <h3 class="text-lg font-serif font-bold text-forest">Contribution Types</h3>
                <span class="text-xs font-medium text-charcoal-light">Total: <?= $total_contributions ?> contributions</span>
            </div>
            <div class="p-6 flex-grow flex flex-col justify-center space-y-5">
                <?php foreach ($contribution_types as $type): ?>
                <?php
                    $barColor = 'bg-charcoal';
                    if ($type['type'] === 'Money') $barColor = 'bg-green-600';
                    if ($type['type'] === 'Resource') $barColor = 'bg-blue-600';
                    if ($type['type'] === 'Item') $barColor = 'bg-purple-600';
                    if ($type['type'] === 'Time') $barColor = 'bg-amber-500';
                ?>
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-sm font-medium text-charcoal"><?= e($type['type']) ?> <span class="text-xs text-charcoal-light ml-1">(<?= e($type['count']) ?>)</span></span>
                        <span class="text-sm font-bold text-charcoal"><?= e($type['percent']) ?>%</span>
                    </div>
                    <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10">
                        <div class="<?= $barColor ?> h-3 rounded-full transition-all duration-500" style="width: <?= e($type['percent']) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Entity Activity Two-Column -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">NGO Activity</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-green-700"><?= e($ngo_activity['approved']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Approved NGOs</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-yellow-600"><?= e($ngo_activity['pending']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Pending Approval</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-red-600"><?= e($ngo_activity['suspended']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Suspended</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-charcoal"><?= e($ngo_activity['total']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Activity -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">Event Activity</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-green-700"><?= e($event_activity['upcoming']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Upcoming</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-charcoal"><?= e($event_activity['completed']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Completed</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-red-600"><?= e($event_activity['suspended']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Suspended</span>
                    </div>
                    <div class="bg-ivory/50 rounded-lg p-4 border border-charcoal/5 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl font-bold text-charcoal"><?= e($event_activity['total']) ?></span>
                        <span class="text-xs font-medium text-charcoal-light mt-1">Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contributors Table -->
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
                        <td class="px-6 py-4 text-sm font-medium text-charcoal"><?= e($user['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-charcoal-light"><?= e($user['count']) ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-forest"><?= e($user['amount']) ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gold"><?= e($user['points']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Most Active NGOs Table -->
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
                        <td class="px-6 py-4 text-sm font-medium text-charcoal"><?= e($ngo['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-charcoal-light text-center"><?= e($ngo['events']) ?></td>
                        <td class="px-6 py-4 text-sm text-charcoal-light text-center"><?= e($ngo['causes']) ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-forest text-right"><?= e($ngo['received']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Period Note -->
    <div class="text-center pt-2 pb-4">
        <p class="text-xs text-charcoal-light">
            <span class="font-medium text-charcoal">Report period: September 2026</span><br>
            Figures shown on this page are demonstration data for the Admin reporting interface.
        </p>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

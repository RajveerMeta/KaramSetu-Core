<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/admin/dashboard_data.php';


ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-forest mb-2">Admin Dashboard</h1>
        <p class="text-charcoal-light">Monitor KarmaSetu activities, manage organizations, and review community contributions.</p>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex items-center space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-forest/10 text-forest">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-charcoal-light">Total Users</p>
                <p class="text-2xl font-bold text-charcoal">
                    <?= number_format($totalUsers) ?>
                </p>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex items-center space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-forest/10 text-forest">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-charcoal-light">Registered NGOs</p>
                <p class="text-2xl font-bold text-charcoal"><?= number_format($totalNGOs) ?></p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex items-center space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-amber-100 text-amber-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-charcoal-light">Pending NGO Approvals</p>
                <p class="text-2xl font-bold text-charcoal"><?= number_format($pendingNGOs) ?></p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex items-center space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-forest/10 text-forest">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
                <p class="text-2xl font-bold text-charcoal">₹<?= number_format((float)$totalContributions, 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        
        <!-- Left Column: Tables (Spans 2 columns) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Pending NGO Approvals -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 flex justify-between items-center">
                    <h2 class="text-lg font-serif font-bold text-forest">Pending NGO Approvals</h2>
                    <a href="<?= baseUrl('admin/ngos/index.php') ?>" class="text-sm font-medium text-forest hover:text-gold transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-ivory/50 text-charcoal-light text-sm">
                                <th class="px-6 py-3 font-medium">NGO</th>
                                <th class="px-6 py-3 font-medium">Location</th>
                                <th class="px-6 py-3 font-medium">Submitted</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-charcoal/5">
                            <?php if (!empty($pendingNGOList)) { ?>
                                <?php foreach ($pendingNGOList as $ngo) { ?>
                                    <tr class="hover:bg-ivory/30 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                            <?= htmlspecialchars($ngo['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-charcoal-light">
                                            <?= htmlspecialchars($ngo['city'] ?? '') ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-charcoal-light">
                                            <?= !empty($ngo['created_at']) ? date('d M Y', strtotime($ngo['created_at'])) : '-' ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                <?= htmlspecialchars(ucfirst($ngo['verification_status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right">
                                            <a href="<?= baseUrl('admin/ngos/view.php?id=' . $ngo['id']) ?>"
                                               class="inline-flex justify-center items-center px-3 py-1.5 border border-forest text-forest hover:bg-forest hover:text-white text-xs font-medium rounded-md transition-colors">
                                                Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-charcoal-light">
                                        No pending NGO approvals.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>

                    </table>
                </div>
            </div>

            <!-- Recent Contributions -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 flex justify-between items-center">
                    <h2 class="text-lg font-serif font-bold text-forest">Recent Contributions</h2>
                    <a href="<?= baseUrl('admin/contributions.php') ?>" class="text-sm font-medium text-forest hover:text-gold transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-ivory/50 text-charcoal-light text-sm">
                                <th class="px-6 py-3 font-medium">Contributor</th>
                                <th class="px-6 py-3 font-medium">Cause</th>
                                <th class="px-6 py-3 font-medium">Amount</th>
                                <th class="px-6 py-3 font-medium">Date</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-charcoal/5">
                            <?php if (!empty($recentContributions)) { ?>
                                <?php foreach ($recentContributions as $contribution) { ?>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-charcoal">
                                            #<?= htmlspecialchars($contribution['id']) ?>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-charcoal">
                                            ₹<?= number_format((float) $contribution['monetary_amount'], 2) ?>
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <?= htmlspecialchars(ucfirst($contribution['payment_status'])) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-sm text-charcoal">
                                        No contributions found.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>

        <!-- Right Column: Activity (Spans 1 column) -->
        <div class="lg:col-span-1 space-y-8">
            
            <!-- Recent Admin Activity -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6">
                <h2 class="text-lg font-serif font-bold text-forest mb-4">Recent Admin Activity</h2>
                
                <div class="space-y-6">
                    <!-- Activity 1 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 rounded-full bg-forest/10 flex items-center justify-center text-forest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal">NGO <span class="font-medium">"Seva Roots Initiative"</span> submitted for approval.</p>
                            <p class="text-xs text-charcoal-light mt-1">2 hours ago</p>
                        </div>
                    </div>
                    
                    <!-- Activity 2 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 rounded-full bg-forest/10 flex items-center justify-center text-forest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal">Admin reviewed <span class="font-medium">"Helping Hands Foundation"</span>.</p>
                            <p class="text-xs text-charcoal-light mt-1">5 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity 3 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 rounded-full bg-forest/10 flex items-center justify-center text-forest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal">New contribution received for <span class="font-medium">Education Support</span>.</p>
                            <p class="text-xs text-charcoal-light mt-1">Yesterday</p>
                        </div>
                    </div>

                    <!-- Activity 4 -->
                    <div class="flex">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-8 h-8 rounded-full bg-forest/10 flex items-center justify-center text-forest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal">Event <span class="font-medium">"Community Food Drive"</span> was created.</p>
                            <p class="text-xs text-charcoal-light mt-1">Sep 23, 2026</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-charcoal/5">
                    <a href="<?= baseUrl('admin/activity.php') ?>" class="text-sm font-medium text-forest hover:text-gold transition-colors flex items-center justify-center w-full">
                        View Complete Log
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/admin.php';
?>

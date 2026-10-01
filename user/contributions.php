<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>



<?php
    $summaries = (object)[
        'total_contributions' => '₹12,500',
        'total_count' => 8,
        'money_donated' => '₹10,000',
        'resources_donated' => 3
    ];

    $contributions = [
        (object)['id' => 1, 'title' => 'Education Support', 'ngo' => 'Smile Foundation', 'type' => 'Money', 'amount' => '₹2,000', 'date' => '28 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 2, 'title' => 'Food Distribution Drive', 'ngo' => 'Helping Hands NGO', 'type' => 'Food', 'amount' => '—', 'date' => '25 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 3, 'title' => 'Community Clothing Drive', 'ngo' => 'Hope Foundation', 'type' => 'Clothes', 'amount' => '—', 'date' => '20 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 4, 'title' => 'Child Education Fund', 'ngo' => 'Care India Initiative', 'type' => 'Money', 'amount' => '₹3,000', 'date' => '15 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 5, 'title' => 'Emergency Relief Support', 'ngo' => 'Community Relief NGO', 'type' => 'Money', 'amount' => '₹5,000', 'date' => '10 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 6, 'title' => 'Useful Items Donation', 'ngo' => 'Local Community Centre', 'type' => 'Useful Items', 'amount' => '—', 'date' => '05 Aug 2026', 'status' => 'Pending'],
    ];

    $hasContributions = count($contributions) > 0;
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
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1 relative z-10">Total Contributions</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1 relative z-10"><?= e($summaries->total_contributions) ?></p>
                <p class="text-xs font-medium text-charcoal-light relative z-10">Across <?= e($summaries->total_count) ?> contributions</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Money Donated</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= e($summaries->money_donated) ?></p>
                <p class="text-xs font-medium text-charcoal-light">Financial contributions</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Resources Donated</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= e($summaries->resources_donated) ?></p>
                <p class="text-xs font-medium text-charcoal-light">Food, clothes & useful items</p>
            </div>
        </div>

        <?php if ($hasContributions): ?>
            <!-- Main Content Area -->
            <div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden">
                
                <!-- Filter / Search Area -->
                <div class="p-5 md:p-8 md:p-6 border-b border-charcoal/5 bg-ivory/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex flex-col md:flex-row items-center pointer-events-none">
                            <svg class="h-5 w-5 text-charcoal-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Search by cause or NGO..." class="block w-full pl-10 pr-3 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm placeholder-charcoal-light/60 focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest transition-colors shadow-sm">
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select class="block w-full sm:w-auto px-4 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm text-charcoal font-medium focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest shadow-sm cursor-pointer">
                            <option value="">All Types</option>
                            <option value="money">Money</option>
                            <option value="food">Food</option>
                            <option value="clothes">Clothes</option>
                            <option value="useful-items">Useful Items</option>
                        </select>
                        <select class="block w-full sm:w-auto px-4 py-2.5 border border-charcoal/20 rounded-lg bg-white text-sm text-charcoal font-medium focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest shadow-sm cursor-pointer">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Desktop Table (Hidden on Mobile) -->
                <div class="hidden lg:block overflow-x-auto">
                    <div class="overflow-x-auto">
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
                                <td class="px-6 py-4 font-medium text-charcoal"><?= e($contribution->title) ?></td>
                                <td class="px-6 py-4 text-charcoal-light"><?= e($contribution->ngo) ?></td>
                                <td class="px-6 py-4 text-charcoal-light"><?= e($contribution->type) ?></td>
                                <td class="px-6 py-4 text-charcoal font-bold"><?= e($contribution->amount) ?></td>
                                <td class="px-6 py-4 text-charcoal-light"><?= e($contribution->date) ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($contribution->status === 'Completed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200"><?= e($contribution->status) ?></span>
                                    <?php elseif ($contribution->status === 'Pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-yellow-50 text-yellow-700 border border-yellow-200"><?= e($contribution->status) ?></span>
                                    <?php elseif ($contribution->status === 'Cancelled'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-200"><?= e($contribution->status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/user/contributions/<?= e($contribution->id) ?>" class="inline-flex items-center justify-center px-4 py-2 border border-charcoal/10 text-xs font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
</div>
                </div>

                <!-- Mobile & Tablet Responsive List (Hidden on Desktop) -->
                <div class="lg:hidden divide-y divide-charcoal/5">
                    <?php foreach ($contributions as $contribution): ?>
                    <div class="p-5 md:p-8 hover:bg-ivory/20 transition-colors">
                        <div class="flex flex-col md:flex-row justify-between items-start mb-3">
                            <div class="pr-2">
                                <h3 class="font-bold text-charcoal text-base mb-1"><?= e($contribution->title) ?></h3>
                                <p class="text-sm text-forest font-medium"><?= e($contribution->ngo) ?></p>
                            </div>
                            <div class="flex-shrink-0 mt-1">
                                <?php if ($contribution->status === 'Completed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200"><?= e($contribution->status) ?></span>
                                <?php elseif ($contribution->status === 'Pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-yellow-50 text-yellow-700 border border-yellow-200"><?= e($contribution->status) ?></span>
                                <?php elseif ($contribution->status === 'Cancelled'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gray-50 text-gray-600 border border-gray-200"><?= e($contribution->status) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 my-4 py-3 border-y border-charcoal/5">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Type</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($contribution->type) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Date</p>
                                <p class="text-sm font-medium text-charcoal"><?= e($contribution->date) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-charcoal-light mb-1">Amount</p>
                                <p class="text-sm font-bold text-charcoal"><?= e($contribution->amount) ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <a href="/user/contributions/<?= e($contribution->id) ?>" class="block w-full text-center px-4 py-2.5 border border-charcoal/10 text-sm font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <div class="p-5 md:p-8 border-t border-charcoal/5 bg-ivory/20 flex flex-col md:flex-row items-center justify-between">
                    <button class="px-4 py-2 border border-charcoal/20 text-sm font-medium rounded-lg text-charcoal-light bg-white opacity-50 cursor-not-allowed shadow-sm">
                        Previous
                    </button>
                    <div class="hidden sm:flex space-x-1">
                        <button class="px-3 py-1.5 border border-forest bg-forest text-white text-sm font-medium rounded-md shadow-sm">1</button>
                        <button class="px-3 py-1.5 border border-charcoal/10 bg-white text-charcoal hover:bg-ivory text-sm font-medium rounded-md shadow-sm transition-colors">2</button>
                        <button class="px-3 py-1.5 border border-charcoal/10 bg-white text-charcoal hover:bg-ivory text-sm font-medium rounded-md shadow-sm transition-colors">3</button>
                    </div>
                    <button class="px-4 py-2 border border-charcoal/20 text-sm font-medium rounded-lg text-charcoal hover:bg-ivory-dark bg-white shadow-sm transition-colors">
                        Next
                    </button>
                </div>

            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl p-10 md:p-16 shadow-sm border border-gold/20 text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="h-20 w-20 bg-forest/5 rounded-full flex flex-col md:flex-row items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-forest/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
                <h3 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">No Contributions Yet</h3>
                <p class="text-charcoal-light max-w-md mx-auto mb-8">Your contributions will appear here once you make your first donation. Start making an impact today!</p>
                <a href="<?= baseUrl('?page=causes') ?>"  class="inline-flex justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-md text-white bg-forest hover:bg-forest-dark transition-all hover:shadow-lg">
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
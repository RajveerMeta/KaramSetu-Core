<?php
require_once __DIR__ . '/../../includes/functions.php';

$contribution = [
    'id' => 'KS-CN-2026-0024',
    'contributor' => 'Rajveer Meta',
    'email' => 'rajveer@example.com',
    'phone' => '+91 98765 43210',
    'contribution' => 'Community Food Support',
    'type' => 'Money',
    'amount' => '₹5,000',
    'payment_method' => 'Online Payment',
    'date' => '24 Sep 2026',
    'status' => 'Completed',
    'ngo' => [
        'name' => 'Seva Roots Initiative',
        'category' => 'Community Welfare NGO',
        'location' => 'Rajkot, Gujarat',
        'status' => 'Approved'
    ],
    'cause' => [
        'name' => 'Community Food Development',
        'category' => 'Food & Nutrition',
        'status' => 'Active',
        'description' => 'Support community food initiatives that provide nutritious meals and essential food assistance to individuals and families in need.'
    ],
    'timeline' => [
        [
            'date' => '24 Sep 2026',
            'title' => 'Contribution submitted',
            'desc' => 'Rajveer Meta submitted a ₹5,000 money contribution.'
        ],
        [
            'date' => '24 Sep 2026',
            'title' => 'Payment confirmed',
            'desc' => 'The online payment was successfully confirmed.'
        ],
        [
            'date' => '24 Sep 2026',
            'title' => 'Contribution completed',
            'desc' => 'The contribution was recorded for Seva Roots Initiative.'
        ]
    ]
];

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Page Header & Back Link -->
    <div class="mb-6">
        <a href="<?= baseUrl('admin/contributions/index.php') ?>" class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Contributions
        </a>
        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-serif font-bold text-forest mb-2"><?= e($contribution['contribution']) ?></h1>
                <p class="text-charcoal-light">Review contribution details, contributor information, and contribution status.</p>
            </div>
            <div class="mt-4 md:mt-0 flex-shrink-0">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <?= e($contribution['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Contribution Summary Card -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-charcoal/10">
                <div class="col-span-2 md:col-span-1 py-3 md:py-0">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Amount</p>
                    <p class="text-2xl font-bold text-forest"><?= e($contribution['amount']) ?></p>
                </div>
                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Type</p>
                    <p class="text-lg font-medium text-charcoal"><?= e($contribution['type']) ?></p>
                </div>
                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Status</p>
                    <p class="text-lg font-medium text-green-700"><?= e($contribution['status']) ?></p>
                </div>
                <div class="py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Date</p>
                    <p class="text-lg font-medium text-charcoal"><?= e($contribution['date']) ?></p>
                </div>
                <div class="col-span-2 md:col-span-1 py-3 md:py-0 md:pl-6">
                    <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Contribution ID</p>
                    <p class="text-sm font-mono font-medium text-charcoal mt-1.5"><?= e($contribution['id']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 items-start">
        
        <!-- Left Column -->
        <div class="flex flex-col gap-6">
            
            <!-- Contributor Information -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">Contributor Information</h3>
                    <a href="<?= baseUrl('admin/users/view.php?id=1') ?>" class="text-sm font-medium text-forest hover:text-gold transition-colors flex items-center">
                        View User
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Name</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['contributor']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Email</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['email']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light mb-1">Phone</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['phone']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contribution Information -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Contribution Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Contribution</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['contribution']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Type</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['type']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Amount</p>
                            <p class="text-base font-bold text-forest"><?= e($contribution['amount']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Payment Method</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['payment_method']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Date</p>
                            <p class="text-base font-medium text-charcoal"><?= e($contribution['date']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-charcoal-light uppercase tracking-wider mb-1">Status</p>
                            <p class="text-base font-medium text-green-700"><?= e($contribution['status']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contribution Activity (Timeline) -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Contribution Activity</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col">
                        <?php foreach ($contribution['timeline'] as $index => $item): ?>
                        <div class="flex gap-4">
                            <!-- Indicator Column -->
                            <div class="flex flex-col items-center w-4 flex-shrink-0">
                                <div class="h-2.5 w-2.5 rounded-full bg-forest mt-1.5 flex-shrink-0 relative z-10"></div>
                                <?php if ($index !== count($contribution['timeline']) - 1): ?>
                                    <div class="w-[2px] bg-charcoal/10 flex-grow my-1"></div>
                                <?php else: ?>
                                    <div class="w-[2px] flex-grow my-1 bg-transparent"></div>
                                <?php endif; ?>
                            </div>
                            <!-- Content Column -->
                            <div class="pb-8 last:pb-0">
                                <p class="text-xs font-bold text-forest mb-1"><?= e($item['date']) ?></p>
                                <p class="text-sm font-bold text-charcoal"><?= e($item['title']) ?></p>
                                <p class="text-sm text-charcoal-light mt-1"><?= e($item['desc']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-6">
            
            <!-- Receiving NGO -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30 flex justify-between items-center">
                    <h3 class="text-lg font-serif font-bold text-forest">Receiving NGO</h3>
                    <a href="<?= baseUrl('admin/ngos/view.php?id=1') ?>" class="text-sm font-medium text-forest hover:text-gold transition-colors flex items-center">
                        View NGO
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4"><?= e($contribution['ngo']['name']) ?></h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Category</dt>
                            <dd class="text-sm text-charcoal"><?= e($contribution['ngo']['category']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Location</dt>
                            <dd class="text-sm text-charcoal"><?= e($contribution['ngo']['location']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Status</dt>
                            <dd class="text-sm font-medium text-green-700"><?= e($contribution['ngo']['status']) ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Supported Cause -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Supported Cause</h3>
                </div>
                <div class="p-6">
                    <h4 class="text-lg font-bold text-charcoal mb-4"><?= e($contribution['cause']['name']) ?></h4>
                    <dl class="space-y-3 mb-4">
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Category</dt>
                            <dd class="text-sm text-charcoal"><?= e($contribution['cause']['category']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-charcoal-light">Status</dt>
                            <dd class="text-sm font-medium text-green-700"><?= e($contribution['cause']['status']) ?></dd>
                        </div>
                    </dl>
                    <div class="pt-4 border-t border-charcoal/10">
                        <p class="text-sm text-charcoal-light leading-relaxed">
                            <?= e($contribution['cause']['description']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contribution Status -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h3 class="text-lg font-serif font-bold text-forest">Contribution Status</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-medium text-charcoal-light uppercase mb-1">Current Status</p>
                        <p class="text-lg font-bold text-green-700"><?= e($contribution['status']) ?></p>
                    </div>
                    <p class="text-sm text-charcoal leading-relaxed">
                        This contribution has been successfully recorded.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

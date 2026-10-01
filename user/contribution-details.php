<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>


<?php
    $contribution = (object)[
        'id' => $id ?? 1,
        'reference' => 'KS-CON-1000' . ($id ?? 1),
        'title' => 'Education Support',
        'organization' => 'Smile Foundation',
        'organization_desc' => 'Supporting children and communities through education and social development initiatives.',
        'organization_id' => 1,
        'cause' => 'Child Education Fund',
        'type' => 'Money Donation',
        'amount' => '₹2,000',
        'date' => '28 August 2026',
        'status' => 'Completed',
        'karma_points' => 200,
        'timeline' => [
            (object)['title' => 'Contribution Initiated', 'date' => '28 Aug 2026'],
            (object)['title' => 'Payment Confirmed', 'date' => '28 Aug 2026'],
            (object)['title' => 'Contribution Completed', 'date' => '28 Aug 2026'],
        ]
    ];
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-10 md:mb-12">
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-3">Contribution Details</h1>
            <p class="text-charcoal-light">Thank you for making a difference through KarmaSetu.</p>
        </div>

        <!-- Main Contribution Summary Card -->
        <div class="bg-white rounded-3xl p-6 md:p-8 md:p-10 shadow-sm border border-gold/20 mb-8 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 border-b border-charcoal/5 pb-8">
                <div>
                    <h2 class="text-2xl md:text-4xl md:text-3xl font-serif text-forest mb-2"><?= e($contribution->title) ?></h2>
                    <p class="text-lg text-forest/80 font-medium"><?= e($contribution->organization) ?></p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-sm font-bold uppercase tracking-widest text-charcoal-light mb-1">Amount</p>
                    <p class="text-3xl md:text-4xl font-serif text-forest font-bold"><?= e($contribution->amount) ?></p>
                </div>
            </div>

            <!-- Structured Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Contribution ID</p>
                    <p class="font-medium text-charcoal"><?= e($contribution->reference) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Contribution Type</p>
                    <p class="font-medium text-charcoal"><?= e($contribution->type) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Date</p>
                    <p class="font-medium text-charcoal"><?= e($contribution->date) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Payment / Contribution Status</p>
                    <?php if ($contribution->status === 'Completed'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200 shadow-sm"><?= e($contribution->status) ?></span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-gray-50 text-gray-700 border border-gray-200 shadow-sm"><?= e($contribution->status) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Organization</p>
                    <p class="font-medium text-charcoal"><?= e($contribution->organization) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Cause</p>
                    <p class="font-medium text-charcoal"><?= e($contribution->cause) ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Impact Section -->
            <div class="bg-forest rounded-3xl p-8 shadow-sm text-ivory flex flex-col justify-center border border-forest-light">
                <div class="w-12 h-12 rounded-full bg-white/10 flex flex-col md:flex-row items-center justify-center mb-5 text-gold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="text-xl font-serif mb-3">Your Impact</h3>
                <p class="text-ivory/90 leading-relaxed font-light">
                    Your <?= e($contribution->amount) ?> contribution helps support educational opportunities for children and communities in need.
                </p>
            </div>

            <!-- Karma Points Section -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gold/20 flex flex-col justify-center text-center">
                <div class="w-16 h-16 rounded-full bg-ivory border border-gold/30 flex flex-col md:flex-row items-center justify-center mx-auto mb-4 text-gold shadow-sm">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-sm font-bold uppercase tracking-widest text-charcoal-light mb-2">Karma Points Earned</h3>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-2">+<?= e($contribution->karma_points) ?></p>
                <p class="text-sm text-charcoal-light">Thank you for contributing to the KarmaSetu community.</p>
            </div>
        </div>

        <!-- Organization Section & Timeline row -->
        <div class="grid grid-cols-1 md:grid-cols-[1.5fr_1fr] gap-8 mb-10">
            
            <!-- Organization Section -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold text-gold uppercase tracking-wider mb-5">NGO / Organization</h3>
                    <div class="flex flex-col md:flex-row items-start gap-4 mb-5">
                        <div class="w-14 h-14 bg-ivory border border-gold/30 rounded-xl flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-2xl md:text-4xl font-serif text-forest">
                            <?= e(substr($contribution->organization, 0, 1)) ?>
                        </div>
                        <div>
                            <h4 class="font-serif text-forest text-xl mb-1"><?= e($contribution->organization) ?></h4>
                            <p class="text-sm text-charcoal-light leading-relaxed"><?= e($contribution->organization_desc) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 mt-auto border-t border-charcoal/5">
                    <a href="/ngos/<?= e($contribution->organization_id) ?>" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-forest hover:text-gold transition-colors group">
                        View NGO Profile 
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Contribution Timeline -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gold/20">
                <h3 class="text-xs font-bold text-gold uppercase tracking-wider mb-6">Timeline</h3>
                <div class="relative border-l-2 border-forest/20 ml-2 space-y-6">
                    <?php foreach ($contribution->timeline as $index => $event): ?>
                        <div class="relative pl-6">
                            <!-- Timeline Dot -->
                            <div class="absolute w-3 h-3 bg-forest rounded-full -left-[7px] top-1.5 shadow-sm border-2 border-white"></div>
                            
                            <h4 class="text-sm font-bold text-charcoal mb-0.5"><?= e($event->title) ?></h4>
                            <p class="text-xs font-medium text-charcoal-light"><?= e($event->date) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= baseUrl('user/contributions.php') ?>"  class="w-full sm:w-auto px-8 py-3.5 bg-white border border-charcoal/20 text-charcoal hover:bg-ivory hover:border-gold/30 rounded-xl font-bold transition-all text-center shadow-sm">
                Back to My Contributions
            </a>
            <a href="<?= baseUrl('?page=causes') ?>"  class="w-full sm:w-auto px-8 py-3.5 bg-forest text-ivory hover:bg-forest-light rounded-xl font-bold transition-all text-center shadow-md">
                Explore More Causes
            </a>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
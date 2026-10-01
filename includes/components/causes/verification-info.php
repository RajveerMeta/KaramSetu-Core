<?php /* props: 
    'title' => 'Every cause starts with verification.',
    'description' => 'Causes on KarmaSetu are connected to NGOs that have completed the platform\'s verification process. We ensure your contribution reaches legitimate organizations.'
 */ ?>
<?php
if (!isset($title)) $title = 'Every cause starts with verification.';
if (!isset($description)) $description = 'Causes on KarmaSetu are connected to NGOs that have completed the platform\'s verification process. We ensure your contribution reaches legitimate organizations.';
?>
<!-- 07 - Verification Trust Section -->
<section class="py-20 bg-ivory-dark relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-serif text-forest mb-4"><?= e($title) ?></h2>
            <p class="text-charcoal-light font-light text-lg">
                <?= e($description) ?>
            </p>
        </div>

        <div class="relative">
            <!-- Continuous line -->
            <div class="absolute top-1/2 left-0 w-full h-px bg-gold/30 -translate-y-1/2 hidden md:block"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Step 1 -->
                <div class="relative bg-white p-6 rounded-2xl border border-gold/10 text-center shadow-sm z-10">
                    <div class="w-10 h-10 mx-auto rounded-full bg-forest/5 flex items-center justify-center text-forest mb-4 border border-forest/10">
                        1
                    </div>
                    <h4 class="font-bold text-sm text-charcoal mb-2">NGO Registration</h4>
                    <p class="text-xs text-charcoal-light">Organizations submit their details and legal documents.</p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-white p-6 rounded-2xl border border-gold/10 text-center shadow-sm z-10">
                    <div class="w-10 h-10 mx-auto rounded-full bg-forest/5 flex items-center justify-center text-forest mb-4 border border-forest/10">
                        2
                    </div>
                    <h4 class="font-bold text-sm text-charcoal mb-2">Admin Verification</h4>
                    <p class="text-xs text-charcoal-light">KarmaSetu team reviews and verifies the organization.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-white p-6 rounded-2xl border border-gold/10 text-center shadow-sm z-10">
                    <div class="w-10 h-10 mx-auto rounded-full bg-forest/10 flex items-center justify-center text-forest mb-4 border border-forest/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-sm text-forest mb-2">Approved NGO</h4>
                    <p class="text-xs text-charcoal-light">The NGO profile becomes active on the platform.</p>
                </div>

                <!-- Step 4 -->
                <div class="relative bg-forest text-ivory p-6 rounded-2xl border border-forest-dark text-center shadow-lg z-10 scale-105 shadow-forest/20">
                    <div class="w-10 h-10 mx-auto rounded-full bg-gold/20 flex items-center justify-center text-gold mb-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-sm text-white mb-2">Public Cause</h4>
                    <p class="text-xs text-ivory/80">Approved NGOs can now post verified community needs.</p>
                </div>

            </div>
        </div>

    </div>
</section>


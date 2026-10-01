<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">Causes</h1>
                <p class="text-charcoal-light">Manage the causes supported by your organization.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/create-cause.php') ?>" class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm">
                    + Create New Cause
                </a>
            </div>
        </div>

        <!-- CAUSES GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- CAUSE 1 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Active</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-forest mb-2">Community Development</h2>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">Supporting local communities through essential resources, infrastructure, and development initiatives.</p>
                    
                    <div class="mb-6">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-1">
                            <span class="text-lg font-bold text-forest">₹3,75,000 <span class="text-xs text-charcoal-light font-normal">raised</span></span>
                            <span class="text-xs font-bold text-charcoal">Goal: ₹5,00,000</span>
                        </div>
                        <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                            <div class="bg-forest h-2 rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-xs font-bold text-forest text-right mt-1">75%</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('ngo/cause-details.php?id=1') ?>" class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <a href="<?= baseUrl('ngo/edit-cause.php?id=1') ?>" class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">Edit</a>
                </div>
            </div>

            <!-- CAUSE 2 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Active</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-forest mb-2">Education Support</h2>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">Providing educational resources and support to children and students from underserved communities.</p>
                    
                    <div class="mb-6">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-1">
                            <span class="text-lg font-bold text-forest">₹1,20,000 <span class="text-xs text-charcoal-light font-normal">raised</span></span>
                            <span class="text-xs font-bold text-charcoal">Goal: ₹2,00,000</span>
                        </div>
                        <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                            <div class="bg-forest h-2 rounded-full" style="width: 60%"></div>
                        </div>
                        <p class="text-xs font-bold text-forest text-right mt-1">60%</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('ngo/cause-details.php?id=2') ?>" class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <a href="<?= baseUrl('ngo/edit-cause.php?id=2') ?>" class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">Edit</a>
                </div>
            </div>

            <!-- CAUSE 3 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Active</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-forest mb-2">Food & Nutrition</h2>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">Helping families and communities access nutritious food and essential resources.</p>
                    
                    <div class="mb-6">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-1">
                            <span class="text-lg font-bold text-forest">₹90,000 <span class="text-xs text-charcoal-light font-normal">raised</span></span>
                            <span class="text-xs font-bold text-charcoal">Goal: ₹1,50,000</span>
                        </div>
                        <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                            <div class="bg-forest h-2 rounded-full" style="width: 60%"></div>
                        </div>
                        <p class="text-xs font-bold text-forest text-right mt-1">60%</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('ngo/cause-details.php?id=3') ?>" class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <a href="<?= baseUrl('ngo/edit-cause.php?id=3') ?>" class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">Edit</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>

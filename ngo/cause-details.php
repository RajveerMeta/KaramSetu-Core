<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <a href="<?= baseUrl('ngo/causes.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Causes
            </a>
        </div>
        
        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Active</span>
                    <span class="text-sm font-bold text-charcoal-light uppercase tracking-wide">Community Development</span>
                </div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest">Community Development</h1>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/edit-cause.php?id=1') ?>" class="inline-block bg-white text-gold border border-gold font-bold py-2.5 px-6 rounded-full hover:bg-gold/5 transition-colors shadow-sm">
                    Edit Cause
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">
            <!-- MAIN CONTENT (Left 2 Columns) -->
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                    <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-4 border-b border-charcoal/10 pb-4">Cause Details</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">Short Description</h3>
                            <p class="text-lg text-charcoal font-medium">Supporting local communities through essential resources, infrastructure, and development initiatives.</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">Detailed Description</h3>
                            <p class="text-base text-charcoal leading-relaxed">
                                We are committed to empowering local communities by building infrastructure, supplying vital resources, and establishing lasting development initiatives. Our ongoing efforts involve close collaboration with community leaders and stakeholders to ensure that every project meets the specific needs of the people it serves. 
                                <br><br>
                                This cause actively seeks contributions to expand our current reach, allowing us to build more community centers and deploy clean water infrastructure.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:p-6 pt-4 border-t border-charcoal/5">
                            <div>
                                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-1">Start Date</h3>
                                <p class="text-base font-bold text-charcoal">10 Jan 2026</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-1">End Date</h3>
                                <p class="text-base font-bold text-charcoal">31 Dec 2026</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 border-b border-charcoal/10 pb-4">
                        <h2 class="text-xl font-serif font-bold text-forest">Recent Contributions</h2>
                        <a href="<?= baseUrl('ngo/contributions.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors">View All</a>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-ivory/30 rounded-xl border border-charcoal/5">
                            <div>
                                <p class="text-sm font-bold text-charcoal">Rajveer Meta</p>
                                <p class="text-xs text-charcoal/70 mt-1">Money &bull; Today, 10:30 AM</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-sm font-bold rounded-lg">₹2,000</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-ivory/30 rounded-xl border border-charcoal/5">
                            <div>
                                <p class="text-sm font-bold text-charcoal">Amit Patel</p>
                                <p class="text-xs text-charcoal/70 mt-1">Money &bull; 16 Sep 2026</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-sm font-bold rounded-lg">₹5,000</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR CONTENT -->
            <div class="space-y-6 lg:space-y-8">
                
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">
                    <h3 class="text-lg font-serif font-bold text-forest mb-6">Funding Progress</h3>
                    
                    <div class="mb-6">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-2">
                            <div>
                                <p class="text-xs font-bold text-charcoal-light uppercase tracking-wide">Raised</p>
                                <p class="text-xl md:text-3xl font-bold text-forest">₹3,75,000</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-charcoal-light uppercase tracking-wide">Goal</p>
                                <p class="text-lg font-bold text-charcoal">₹5,00,000</p>
                            </div>
                        </div>
                        
                        <div class="w-full bg-ivory rounded-full h-3 mb-2 overflow-hidden border border-charcoal/5">
                            <div class="bg-forest h-3 rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-sm font-bold text-forest text-right">75% Achieved</p>
                    </div>
                    
                    <div class="space-y-3 pt-6 border-t border-charcoal/10">
                        <div class="flex flex-wrap items-center justify-between gap-4 text-sm">
                            <span class="text-charcoal-light font-medium">Total Donors</span>
                            <span class="font-bold text-charcoal">128</span>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-4 text-sm">
                            <span class="text-charcoal-light font-medium">Days Remaining</span>
                            <span class="font-bold text-charcoal">102</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>

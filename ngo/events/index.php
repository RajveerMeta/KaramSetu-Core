<?php
require_once __DIR__ . '/../../includes/functions.php';
ob_start();
?>
<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">Events</h1>
                <p class="text-charcoal-light">Manage your organization's upcoming and past events.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/events/create.php') ?>" class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm">
                    + Create New Event
                </a>
            </div>
        </div>

        <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6">Upcoming Events</h2>
        <!-- UPCOMING EVENTS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:p-8 mb-12">
            
            <!-- EVENT 1 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Upcoming</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-forest mb-2">Hope For Every Child – Charity Evening</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            2026-09-24
                        </div>
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Rajkot, Gujarat
                        </div>
                    </div>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">A charity evening focused on supporting educational opportunities and essential resources for children.</p>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('pages/event-details.php?id=1') ?>" class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <a href="<?= baseUrl('ngo/events/edit.php?id=1') ?>" class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">Edit</a>
                </div>
            </div>

            <!-- EVENT 2 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Upcoming</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-forest mb-2">Skills For Tomorrow</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            2026-10-05
                        </div>
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Rajkot, Gujarat
                        </div>
                    </div>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">A skill development event designed to help young people build practical skills for future opportunities.</p>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('pages/event-details.php?id=2') ?>" class="flex-1 text-center bg-ivory text-forest font-bold py-2 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <a href="<?= baseUrl('ngo/events/edit.php?id=2') ?>" class="flex-1 text-center bg-white text-gold font-bold py-2 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">Edit</a>
                </div>
            </div>

        </div>

        <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6 opacity-75">Past Events</h2>
        <!-- PAST EVENTS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:p-8 opacity-75">
            <!-- PAST EVENT 1 -->
            <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-charcoal/10 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-2.5 py-1 bg-charcoal/10 text-charcoal text-xs font-bold uppercase tracking-wider rounded-md">Completed</span>
                    </div>
                    <h2 class="text-xl font-serif font-bold text-charcoal mb-2">Community Health Awareness Camp</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-charcoal-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            2026-08-15
                        </div>
                        <div class="flex items-center text-sm text-charcoal">
                            <svg class="w-4 h-4 mr-2 text-charcoal-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Rajkot, Gujarat
                        </div>
                    </div>
                    <p class="text-sm text-charcoal-light mb-6 line-clamp-2">A community awareness event focused on basic health education and wellness.</p>
                </div>
                
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-charcoal/10">
                    <a href="<?= baseUrl('pages/event-details.php?id=3') ?>" class="flex-1 text-center bg-ivory text-charcoal font-bold py-2 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/10 text-sm">View Details</a>
                    <!-- Removed edit for completed event typically, or keep it generic -->
                </div>
            </div>
        </div>

    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/main.php';
?>

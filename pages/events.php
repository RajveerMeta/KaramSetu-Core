<?php
$events = require __DIR__ . '/../data/events.php';
?>




    <!-- 01. EVENTS HERO SECTION -->
    <section class="relative pt-20 pb-10 overflow-hidden bg-ivory">
        <!-- Subtle Visual Illustrations -->
        <div class="absolute inset-0 pointer-events-none opacity-40 overflow-hidden">
            <!-- Left decorative shape -->
            <div class="absolute top-10 -left-10 w-48 h-48 bg-gold/10 rounded-full blur-2xl"></div>
            <!-- Right decorative shape -->
            <div class="absolute -bottom-20 -right-10 w-full md:w-64 h-64 bg-forest/5 rounded-full blur-3xl"></div>
            
            <!-- Abstract calendar/community visual (SVG) on right -->
            <div class="absolute top-1/2 right-4 md:right-24 lg:right-40 -translate-y-1/2 opacity-10">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="text-forest">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <circle cx="12" cy="15" r="2"></circle>
                </svg>
            </div>
            
            <!-- Abstract people visual (SVG) on left -->
            <div class="absolute bottom-10 left-4 md:left-20 lg:left-32 opacity-10">
                <svg width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="text-gold">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block border border-gold/30 bg-white/50 backdrop-blur text-gold font-bold tracking-widest uppercase text-xs px-4 py-1.5 rounded-full mb-4 shadow-sm">Events & Experiences</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-forest mb-6 leading-tight">Events That Turn Moments Into Impact</h1>
            <p class="text-lg md:text-xl text-charcoal-light font-light max-w-2xl mx-auto leading-relaxed">
                Discover events hosted by trusted NGOs and communities. Join meaningful experiences, support important causes, and be part of something that makes a difference.
            </p>
        </div>
    </section>

    <!-- Wrapper for Filtering -->
    <div id="eventsWrapper">
        
        <!-- 02. SEARCH + FILTER BAR -->
        <section class="bg-white border-y border-gold/20 py-4 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between w-full">
                    
                    <!-- Search -->
                    <div class="relative w-full md:flex-1 md:max-w-lg">
                        <div class="absolute left-4 top-0 bottom-0 flex flex-col md:flex-row items-center justify-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="eventsSearchQuery" placeholder="Search events, NGOs, or locations..." 
                            class="w-full pl-12 pr-4 py-2.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal transition-all font-medium"  style="padding-left: 52px;">
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full md:w-auto">
                        <select id="eventsCategory" class="px-4 py-2.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal font-medium flex-1 md:flex-none cursor-pointer">
                            <option value="all">All Events</option>
                            <option value="fundraising">Fundraising</option>
                            <option value="workshop">Workshop</option>
                            <option value="sports">Sports</option>
                            <option value="environment">Environment</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="food & hunger">Food & Hunger</option>
                            <option value="community">Community</option>
                            <option value="animal welfare">Animal Welfare</option>
                            <option value="education">Education</option>
                        </select>

                        <select id="eventsDate" class="px-4 py-2.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal font-medium flex-1 md:flex-none cursor-pointer">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="this week">This Week</option>
                            <option value="this month">This Month</option>
                        </select>

                        <select id="eventsType" class="px-4 py-2.5 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal font-medium flex-1 md:flex-none cursor-pointer">
                            <option value="all">Any Price</option>
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- 03. FEATURED EVENTS -->
        <section class="pt-16 pb-12 bg-ivory relative border-b border-gold/10" id="eventsFeaturedSection">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-gold/10 pb-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-serif text-forest mb-2">Featured Events</h2>
                        <p class="text-charcoal-light font-medium text-base">Handpicked events you won't want to miss.</p>
                    </div>
                    <a href="#upcoming" class="text-sm font-bold text-forest hover:text-gold transition-colors inline-flex items-center group">
                        View All Featured
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
                    <?php $featured = array_slice(array_filter($events, function($e) { return !empty($e['featured']); }), 0, 3); foreach($featured as $event): ?>
                        <div class="flex">
                            <?php $id = $event['id']; $title = $event['title']; $category = $event['category']; $date = $event['date']; $ngoName = $event['ngo_name']; $location = $event['location']; $description = $event['description']; $entryFee = $event['entry_fee']; $status = $event['status']; $image = $event['image']; $featured = true; include __DIR__ . "/../includes/components/events/event-card.php"; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 04. UPCOMING EVENTS -->
        <section id="upcoming" class="pt-16 pb-20 bg-white relative min-h-[400px]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-gold/10 pb-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-serif text-forest mb-2">Upcoming Events</h2>
                        <p class="text-charcoal-light font-medium text-base">Explore more opportunities to connect, participate, and support meaningful causes.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch" x-ref="eventGrid">
                    <?php foreach($events as $event): ?>
                        <!-- Event Container (Filtered via JS) -->
                        <div class="flex flex-col md:flex-row event-item" 
                             data-category="<?= e(strtolower($event['category'])) ?>"
                             data-price="<?= e(strtolower($event['entry_fee']) === 'Free' ? 'free' : 'paid') ?>"
                             >
                            <?php $id = $event['id']; $title = $event['title']; $category = $event['category']; $date = $event['date']; $ngoName = $event['ngo_name']; $location = $event['location']; $description = $event['description']; $entryFee = $event['entry_fee']; $status = $event['status']; $image = $event['image']; $featured = false; include __DIR__ . "/../includes/components/events/event-card.php"; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 05. NO RESULTS STATE -->
                <div id="eventsEmptyState" style="display: none;" class="text-center py-20 bg-ivory rounded-3xl border border-gold/20 shadow-sm mt-8">
                    <div class="w-20 h-20 bg-white rounded-full flex flex-col md:flex-row items-center justify-center mx-auto mb-6 shadow-sm border border-gold/10">
                        <svg class="w-10 h-10 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl md:text-4xl font-serif text-forest mb-3">No events found</h3>
                    <p class="text-charcoal-light mb-8 text-base max-w-md mx-auto">We couldn't find any events matching your search. Try changing your filters or search for something else.</p>
                    <button id="eventsClearBtn" class="bg-forest text-ivory hover:bg-forest/90 px-8 py-3 rounded-xl font-bold transition-all shadow-md">
                        Clear Filters
                    </button>
                </div>

                <!-- 06. PAGINATION (Frontend Only) -->
                <div class="mt-16 flex flex-wrap justify-center items-center gap-2" id="eventsPagination">
                    <button class="px-4 py-2 text-sm font-bold text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        &larr; Previous
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center text-sm font-bold text-white bg-forest rounded-lg shadow-sm">1</button>
                    <button class="w-10 h-10 flex items-center justify-center text-sm font-medium text-charcoal bg-white border border-gray-200 hover:bg-ivory hover:border-gold/30 rounded-lg transition-colors">2</button>
                    <button class="w-10 h-10 flex items-center justify-center text-sm font-medium text-charcoal bg-white border border-gray-200 hover:bg-ivory hover:border-gold/30 rounded-lg transition-colors">3</button>
                    <button class="px-4 py-2 text-sm font-bold text-charcoal bg-white border border-gray-200 hover:bg-ivory hover:border-gold/30 rounded-lg transition-colors">
                        Next &rarr;
                    </button>
                </div>

            </div>
        </section>
    </div>

    <!-- 07. ORGANIZER CTA SECTION -->
    <section class="py-12 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-ivory rounded-3xl p-8 md:p-10 border border-gold/20 shadow-sm flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden">
                <!-- Subtle background illustration -->
                <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none w-full md:w-64 h-64 text-gold">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                
                <div class="flex-1 relative z-10">
                    <span class="inline-block bg-white text-gold font-bold tracking-widest uppercase text-[10px] px-3 py-1.5 rounded-full mb-3 shadow-sm border border-gold/20">For Organizers</span>
                    <h2 class="text-2xl md:text-4xl md:text-3xl font-serif text-forest mb-2">Have an Event That Can Create Change?</h2>
                    <p class="text-charcoal-light text-sm md:text-base font-light max-w-xl">
                        Approved NGOs can organize fundraising events and bring their communities together through Karma Setu. Let's make a difference together.
                    </p>
                </div>
                
                <div class="relative z-10 shrink-0">
                    <a href="<?= baseUrl('?page=ngos') ?>" class="inline-flex items-center gap-2 bg-forest text-ivory hover:bg-forest/90 px-6 py-3 rounded-xl font-bold transition-colors shadow-md group whitespace-nowrap">
                        Learn About NGO Registration
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>




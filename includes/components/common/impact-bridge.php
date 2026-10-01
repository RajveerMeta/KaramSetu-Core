<section class="py-24 bg-ivory relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-serif text-forest">Where help is needed right now.</h2>
        </div>

        <!-- Visual Bridge -->
        <div class="relative py-16 hidden md:block">
            <!-- Continuous Bridge Line -->
            <svg class="absolute top-1/2 left-0 w-full h-24 -translate-y-1/2 -z-10" preserveAspectRatio="none" viewBox="0 0 1000 100">
                <path d="M0,50 Q250,10 500,50 T1000,50" stroke="var(--color-gold)" stroke-width="2" stroke-dasharray="6 6" fill="none" opacity="0.4" />
            </svg>

            <div class="flex justify-between items-center relative z-10 px-8">
                
                <!-- Node 1 -->
                <div class="flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-white border-2 border-forest shadow-lg flex items-center justify-center text-forest group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="mt-4 font-serif text-lg text-forest">You</span>
                </div>

                <!-- Node 2 -->
                <div class="flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-ivory-dark border-2 border-gold shadow-md flex items-center justify-center text-gold group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="mt-4 font-serif text-lg text-forest">Contribution</span>
                </div>

                <!-- Node 3 -->
                <div class="flex flex-col items-center text-center group">
                    <div class="w-20 h-20 rounded-full bg-forest text-ivory shadow-xl flex items-center justify-center group-hover:scale-110 transition-transform relative">
                        <span class="absolute -top-2 -right-2 bg-gold text-xs px-2 py-1 rounded-full text-forest font-bold">Verified</span>
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="mt-4 font-serif text-xl font-bold text-forest">Verified NGO</span>
                </div>

                <!-- Node 4 -->
                <div class="flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-ivory-dark border-2 border-gold shadow-md flex items-center justify-center text-gold group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="mt-4 font-serif text-lg text-forest">Community</span>
                </div>

                <!-- Node 5 -->
                <div class="flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-white border-2 border-forest shadow-lg flex items-center justify-center text-forest group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <span class="mt-4 font-serif text-lg text-forest">Impact</span>
                </div>

            </div>

            <!-- Floating Need labels -->
            <div class="absolute top-0 left-1/4 bg-white px-3 py-1 rounded-full text-xs font-medium text-charcoal border border-gold/30 shadow-sm">Food support</div>
            <div class="absolute bottom-4 left-1/3 bg-white px-3 py-1 rounded-full text-xs font-medium text-charcoal border border-gold/30 shadow-sm">Children's education</div>
            <div class="absolute top-8 right-1/4 bg-white px-3 py-1 rounded-full text-xs font-medium text-charcoal border border-gold/30 shadow-sm">Winter clothing</div>
            <div class="absolute bottom-0 right-1/3 bg-white px-3 py-1 rounded-full text-xs font-medium text-charcoal border border-gold/30 shadow-sm">Community assistance</div>
        </div>

        <!-- Causes Filter & Grid -->
        <div id="impactCausesSection" class="mt-16">
            
            <!-- Filter -->
            <div class="flex flex-wrap justify-center gap-2 mb-10" id="impactFilterButtons">
                <button data-id="all" class="impact-filter-btn bg-forest text-ivory px-5 py-2 rounded-full text-sm font-medium transition-colors">All Needs</button>
                <button data-id="food" class="impact-filter-btn bg-transparent text-charcoal hover:bg-gold/10 px-5 py-2 rounded-full text-sm font-medium transition-colors">Food</button>
                <button data-id="education" class="impact-filter-btn bg-transparent text-charcoal hover:bg-gold/10 px-5 py-2 rounded-full text-sm font-medium transition-colors">Education</button>
                <button data-id="health" class="impact-filter-btn bg-transparent text-charcoal hover:bg-gold/10 px-5 py-2 rounded-full text-sm font-medium transition-colors">Health</button>
                <button data-id="clothes" class="impact-filter-btn bg-transparent text-charcoal hover:bg-gold/10 px-5 py-2 rounded-full text-sm font-medium transition-colors">Clothes</button>
                <button data-id="community" class="impact-filter-btn bg-transparent text-charcoal hover:bg-gold/10 px-5 py-2 rounded-full text-sm font-medium transition-colors">Community</button>
            </div>

            <!-- Previews -->
            <?php include __DIR__ . '/causes-preview.php'; ?>

            <div class="mt-12 text-center">
                <a href="#" class="inline-flex items-center text-forest font-semibold hover:text-gold transition-colors">
                    <span class="border-b-2 border-forest hover:border-gold pb-1 transition-colors">Explore all causes</span>
                    <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

        </div>

    </div>
</section>



<?php
$ngos = require __DIR__ . '/../data/ngos.php';
?>



    <!-- 01 - Page Introduction / Hero -->
    <section class="relative pt-12 pb-10 overflow-hidden bg-ivory">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="text-gold font-bold tracking-widest uppercase text-xs">Our Partners</span>
            <h1 class="mt-3 text-4xl md:text-5xl lg:text-6xl font-serif text-forest mb-4 leading-tight">Organizations turning compassion into action.</h1>
            <p class="text-lg md:text-xl text-charcoal-light font-light max-w-2xl mx-auto leading-relaxed">
                KarmaSetu connects you with trustworthy, verified NGOs working across different causes. Discover organizations making a real impact and find one you want to support.
            </p>
            <div class="mt-7">
                <button class="bg-forest text-ivory hover:bg-forest/90 px-8 py-3.5 rounded-full font-bold transition-colors shadow hover:shadow-md">
                    Explore NGOs
                </button>
            </div>
        </div>
        
        <!-- Subtle bridge line -->
        <svg class="absolute top-1/2 left-0 w-full h-32 -translate-y-1/2 opacity-20 pointer-events-none text-gold" preserveAspectRatio="none" viewBox="0 0 1000 100">
            <path d="M0,50 Q250,90 500,50 T1000,50" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" fill="none" />
        </svg>
    </section>

    <!-- 02 - Trust / Verification Strip -->
    <section class="bg-forest text-ivory py-4 border-y border-gold/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row flex-wrap justify-center items-center gap-4 sm:gap-8 md:gap-12 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Verified organizations</span>
                </div>
                <div class="hidden sm:block text-gold/30">&bull;</div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"></path></svg>
                    <span>Transparent profiles</span>
                </div>
                <div class="hidden sm:block text-gold/30">&bull;</div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Multiple ways to contribute</span>
                </div>
            </div>
        </div>
    </section>

   
    <div id="ngoWrapper">
        <section class="bg-white border-b border-gold/10 py-3 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col xl:flex-row gap-3 items-center justify-between">
                    <!-- Search -->
                    <div class="relative w-full xl:w-1/4 shrink-0">
                        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input
                            type="text"
                            id="ngoSearchQuery"
                            placeholder="Search NGOs..."
                            class="w-full h-12 pl-10 pr-4 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-sm text-charcoal transition-shadow"
                        >                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap xl:flex-nowrap gap-3 w-full xl:w-auto flex-1 xl:justify-end">
                        <select id="ngoSelectedCategory" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all">All Causes</option>
                            <option value="education">Education</option>
                            <option value="food & hunger">Food & Hunger</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="environment">Environment</option>
                            <option value="women & children">Women & Children</option>
                            <option value="disaster relief">Disaster Relief</option>
                            <option value="animal welfare">Animal Welfare</option>
                            <option value="community development">Community Development</option>
                        </select>

                        <select id="ngoSelectedLocation" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all">All Locations</option>
                            <option value="ahmedabad">Ahmedabad</option>
                            <option value="vadodara">Vadodara</option>
                            <option value="surat">Surat</option>
                            <option value="rajkot">Rajkot</option>
                            <option value="mumbai">Mumbai</option>
                            <option value="pune">Pune</option>
                        </select>

                        <select id="ngoVerification" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="all">Any Status</option>
                            <option value="verified">Verified Only</option>
                        </select>
                        
                        <select id="ngoSort" class="h-10 px-3 bg-white border border-gray-200 rounded-md focus:ring-2 focus:ring-forest outline-none text-sm text-charcoal flex-1 sm:flex-none min-w-[130px] transition-shadow">
                            <option value="recommended">Recommended</option>
                            <option value="name_asc">Name A-Z</option>
                            <option value="recent">Recently Added</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 04 - Featured NGO Area -->
        <section class="pt-6 pb-10 bg-ivory">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-forest rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row relative group">
                    <div class="md:w-1/2 p-8 lg:px-12 lg:py-14 flex flex-col justify-center relative z-10 text-ivory">
                        <div class="w-full">
                            <div class="flex items-center gap-3 mb-5">
                                <span class="inline-flex items-center gap-1.5 bg-green-500/20 text-green-300 text-[11px] font-bold px-3.5 py- rounded-full uppercase tracking-widest border border-green-400/30">
                                    <svg class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Verified Partner
                                </span>
                                <span class="text-[11px] font-bold text-gold uppercase tracking-widest">Education</span>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif text-white mb-3 leading-tight">Udaan Community Foundation</h2>
                            
                            <p class="text-ivory/80 text-sm flex items-center mb-5 font-medium">
                                <svg class="w-4 h-4 mr-1.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Ahmedabad
                            </p>
                            
                            <p class="text-ivory/90 mb-8 font-light text-base lg:text-lg leading-relaxed max-w-lg">Working to improve access to education and learning resources for underprivileged children in urban slums.</p>
                            
                            <div class="mb-10">
                                <span class="block text-[10px] font-bold text-ivory/60 uppercase tracking-widest mb-3">Supported Contributions</span>
                                <div class="flex flex-wrap gap-3">
                                    <span class="bg-ivory/10 border border-ivory/20 text-ivory text-xs px-3 py-1.5 rounded-md font-medium shadow-sm">Money</span>
                                    <span class="bg-ivory/10 border border-ivory/20 text-ivory text-xs px-3 py-1.5 rounded-md font-medium shadow-sm">Books</span>
                                    <span class="bg-ivory/10 border border-ivory/20 text-ivory text-xs px-3 py-1.5 rounded-md font-medium shadow-sm">Useful Items</span>
                                    <span class="bg-ivory/10 border border-ivory/20 text-ivory text-xs px-3 py-1.5 rounded-md font-medium shadow-sm">Volunteer Time</span>
                                </div>
                            </div>

                            <div>
                                <a href="#" class="inline-flex items-center justify-center bg-gold text-forest hover:bg-white shadow-md hover:shadow-lg px-8 py-4 rounded-xl text-sm font-bold transition-all group/btn">
                                    <span>View NGO Profile</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-1/2 bg-charcoal relative overflow-hidden h-64 md:h-auto">
                        <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=800&q=80" alt="Udaan Community Foundation" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-r from-forest via-forest/80 to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

        
        
        <!-- 05 - NGO Grid -->
        <section class="pb-8 bg-ivory relative min-h-[400px]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" x-ref="grid">
                    <?php foreach($ngos as $ngo): ?>
                        <div class="ngo-item ngo-card-item"
                             data-name="<?= e(strtolower($ngo['name'])) ?>"
                             data-category="<?= e(strtolower($ngo['category'])) ?>"
                             data-location="<?= e(strtolower($ngo['location'])) ?>"
                             data-description="<?= e(strtolower($ngo['description'])) ?>"
                             data-verified="<?= e($ngo['verified'] ? 'true' : 'false') ?>"
                        >
                            <?php $id = $ngo['id']; $name = $ngo['name']; $category = $ngo['category']; $location = $ngo['location']; $description = $ngo['description']; $accepts = $ngo['accepts']; $verified = $ngo['verified']; include __DIR__ . "/../includes/components/ngos/ngo-card.php"; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 06 - Empty / No Results State -->
                <div id="ngoEmptyState2" style="display: none;">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h3 class="text-xl font-serif text-forest mb-2">No organizations match your current filters.</h3>
                    <p class="text-charcoal-light mb-6">Try adjusting your search or filter criteria to find more NGOs.</p>
                    <button id="ngoClearFiltersBtn" class="bg-forest text-ivory hover:bg-forest/90 px-6 py-2 rounded-full font-medium transition-colors">
                        Clear all filters
                    </button>
                </div>
                
            </div>
        </section>
        
    </div>
        <!-- 07 - NGO Registration CTA -->
    <section class="pt-10 pb-12 bg-charcoal text-ivory text-center border-t border-gold/20">
        </br>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <span class="text-gold font-bold tracking-widest uppercase text-xs mb-2">For NGOs</span>
            <h2 class="text-white text-3xl md:text-4xl font-serif mb-5 leading-tight w-full">Are you an organization creating change?</h2>
            <p class="text-white text-lg mb-8 font-light leading-relaxed max-w-xl mx-auto">
                Join KarmaSetu and connect your work with people who want to contribute. Reach a wider audience and manage your contributions transparently.
            </p>
            <a href="<?= baseUrl('auth/ngo-register.php') ?>" class="bg-gold text-forest hover:bg-white shadow-md px-8 py-3.5 rounded-full font-bold transition-colors inline-block text-center">
                Register Your NGO
            </a>
        </div>
    </section>

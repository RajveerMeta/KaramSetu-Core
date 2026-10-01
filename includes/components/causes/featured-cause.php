<!-- 04 - Featured Urgent Need -->
<section class="py-16 bg-white border-b border-gold/10" id="featuredCauseSection">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col lg:flex-row bg-ivory border border-gold/20 rounded-3xl overflow-hidden shadow-lg group">
            
            <!-- Image side -->
            <div class="lg:w-1/2 relative h-64 lg:h-auto overflow-hidden">
                <img src="https://images.pexels.com/photos/6995247/pexels-photo-6995247.jpeg?auto=compress&cs=tinysrgb&w=1000" alt="Community Kitchen" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                
                <div class="absolute top-6 left-6 flex gap-2">
                    <span class="bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider flex items-center gap-1 shadow-md">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Urgent
                    </span>
                    <span class="bg-white/90 backdrop-blur text-forest text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 shadow-md">
                        <svg class="w-3 h-3 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verified NGO
                    </span>
                </div>
            </div>

            <!-- Content side -->
            <div class="lg:w-1/2 p-8 md:p-12 flex flex-col justify-center relative">
                <!-- Abstract bridge line decoration -->
                <svg class="absolute top-0 right-0 w-32 h-32 opacity-10 text-gold" viewBox="0 0 100 100">
                    <path d="M100,0 Q50,50 100,100" stroke="currentColor" stroke-width="2" fill="none" />
                </svg>

                <div class="flex items-center text-sm text-charcoal-light mb-4">
                    <svg class="w-4 h-4 mr-1.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Ahmedabad, Gujarat
                </div>

                <h3 class="text-3xl md:text-4xl font-serif text-forest mb-4">Community Kitchen Support</h3>
                
                <p class="text-charcoal-light mb-8 text-lg font-light leading-relaxed">
                    "A community kitchen currently needs food supplies to continue serving daily meals to families in nearby communities affected by recent localized flooding."
                </p>

                <!-- Requirement Visual -->
                <div class="bg-white p-6 rounded-2xl border border-gold/10 shadow-sm mb-8">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1">Needed</span>
                            <span class="text-lg font-serif text-forest">Food supplies</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1">Current</span>
                            <span class="text-xl font-bold text-forest">68%</span>
                        </div>
                    </div>
                    <div class="w-full bg-ivory-dark rounded-full h-2.5">
                        <div class="bg-gold h-2.5 rounded-full relative" style="width: 68%">
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-gold rounded-full shadow"></div>
                        </div>
                    </div>
                </div>

                <!-- Ways to help buttons -->
                <div>
                    <span class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-3">Ways you can help</span>
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= baseUrl('?page=contribute&id=7') ?>" class="bg-forest text-ivory px-6 py-2.5 rounded-full text-sm font-medium hover:bg-forest-dark transition-colors shadow-sm inline-block">
                            Give Food
                        </a>
                        <a href="<?= baseUrl('?page=contribute&id=7') ?>" class="bg-ivory border border-gold/40 text-forest px-6 py-2.5 rounded-full text-sm font-medium hover:bg-white hover:border-gold transition-colors inline-block">
                            Contribute Money
                        </a>
                        <a href="<?= baseUrl('?page=contribute&id=7') ?>" class="bg-transparent text-forest px-6 py-2.5 rounded-full text-sm font-medium hover:bg-forest/5 transition-colors underline underline-offset-4 decoration-gold/50 hover:decoration-gold inline-block">
                            Volunteer
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>


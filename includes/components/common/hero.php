<section class="relative pt-20 pb-24 lg:pt-32 lg:pb-36 overflow-hidden">
    <!-- Abstract bridge lines in background -->
    <svg class="absolute top-0 right-0 w-3/4 h-full -z-10 text-gold/20" viewBox="0 0 800 600" fill="none">
        <path d="M-100,500 Q300,100 900,400" stroke="currentColor" stroke-width="1.5" fill="none" class="bridge-path" />
        <path d="M0,600 Q400,200 1000,500" stroke="currentColor" stroke-width="1" fill="none" class="bridge-path" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
            
            <!-- Left Text Content -->
            <div class="lg:col-span-6 lg:pr-8">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif text-forest leading-[1.1] tracking-tight mb-8">
                    Every good intention <br/><span class="italic font-light text-gold">needs a bridge.</span>
                </h1>
                <p class="text-lg md:text-xl text-charcoal-light mb-10 max-w-lg leading-relaxed font-light">
                    KarmaSetu connects people willing to help with causes, NGOs, and communities that need support.
                </p>

                <!-- Contribution Selector -->
                <div id="heroContribSection" class="bg-white p-6 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gold/10 mb-8">
                    <p class="text-sm font-semibold text-charcoal mb-4 uppercase tracking-wider">I want to contribute</p>
                    <div class="flex flex-wrap gap-2 mb-6" id="heroContribButtons">
                        <button data-id="money" class="hero-contrib-btn bg-forest text-ivory border-forest px-4 py-2 rounded-full border text-sm font-medium transition-colors">Money</button>
                        <button data-id="food" class="hero-contrib-btn bg-ivory border-gold/30 text-charcoal hover:border-gold px-4 py-2 rounded-full border text-sm font-medium transition-colors">Food</button>
                        <button data-id="clothes" class="hero-contrib-btn bg-ivory border-gold/30 text-charcoal hover:border-gold px-4 py-2 rounded-full border text-sm font-medium transition-colors">Clothes</button>
                        <button data-id="items" class="hero-contrib-btn bg-ivory border-gold/30 text-charcoal hover:border-gold px-4 py-2 rounded-full border text-sm font-medium transition-colors">Useful Items</button>
                        <button data-id="time" class="hero-contrib-btn bg-ivory border-gold/30 text-charcoal hover:border-gold px-4 py-2 rounded-full border text-sm font-medium transition-colors">Time</button>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?= baseUrl('?page=causes') ?>" class="bg-forest text-ivory hover:bg-forest-dark px-8 py-3.5 rounded-full font-medium transition-all shadow-md w-full sm:w-auto text-center inline-block">
                            Find a way to help
                        </a>
                        <a href="<?= baseUrl('?page=causes') ?>" class="bg-transparent border border-forest text-forest hover:bg-forest/5 px-8 py-3.5 rounded-full font-medium transition-all w-full sm:w-auto text-center inline-block">
                            Explore causes
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 text-sm text-charcoal-light font-medium">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Not just about money
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Verified NGOs
                    </span>
                </div>
            </div>

            <!-- Right Image Content (Asymmetric) -->
            <div class="lg:col-span-6 mt-16 lg:mt-0 relative">
                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-forest/10 rounded-full blur-3xl"></div>
                
                <div class="relative rounded-t-full rounded-b-3xl overflow-hidden aspect-[4/5] md:aspect-[3/4] shadow-2xl border-4 border-white">
                    <!-- Placeholder for real community photo -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-forest/80 to-transparent mix-blend-multiply z-10"></div>
                    <img src="https://images.pexels.com/photos/6591154/pexels-photo-6591154.jpeg?auto=compress&cs=tinysrgb&w=1000" alt="Community Support" class="object-cover w-full h-full" />
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 z-20 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                        <p class="text-white font-serif text-xl md:text-2xl italic">"Bridging the gap between a willing heart and a waiting hand."</p>
                    </div>
                </div>
                
               
            </div>
            
        </div>
    </div>
</section>



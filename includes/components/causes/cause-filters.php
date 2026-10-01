<!-- 02 - Contribution Type Selector -->
<section class="bg-white border-y border-gold/10 sticky top-20 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center py-4 gap-4">
            <span class="text-xs font-bold text-charcoal uppercase tracking-widest whitespace-nowrap shrink-0">I want to help with</span>
            
            <div class="overflow-x-auto pb-2 md:pb-0 hide-scrollbar -mx-4 px-4 md:mx-0 md:px-0">
                <div class="flex gap-2">
                    <button data-id="all" class="cause-filter-btn bg-forest text-ivory border-forest px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Everything</button>
<button data-id="money" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Money</button>
<button data-id="food" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Food</button>
<button data-id="clothes" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Clothes</button>
<button data-id="books" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Books</button>
<button data-id="items" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Useful Items</button>
<button data-id="time" class="cause-filter-btn bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">Time</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 03 - Location / Search Toolbar -->
<section class="bg-ivory-dark py-4 border-b border-gold/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            
            <!-- Search -->
            <div class="relative w-full md:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-charcoal-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchQuery" 
                    class="block w-full pl-10 pr-3 py-2 border border-gold/30 rounded-lg leading-5 bg-white placeholder-charcoal-light focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm transition-colors" 
                    placeholder="Search causes, NGOs or needs..."
                >
            </div>

            <div class="flex w-full md:w-auto gap-4">
                <!-- Location -->
                <div class="relative flex-1 md:w-48">
                    <select id="locationFilter" class="block w-full pl-3 pr-10 py-2 border border-gold/30 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm appearance-none cursor-pointer">
                        <option value="all">Any location</option>
                        <option value="ahmedabad">Ahmedabad</option>
                        <option value="mumbai">Mumbai</option>
                        <option value="pune">Pune</option>
                        <option value="surat">Surat</option>
                        <option value="vadodara">Vadodara</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-charcoal-light">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Sort -->
                <div class="relative flex-1 md:w-48">
                    <select id="sortFilter" class="block w-full pl-3 pr-10 py-2 border border-gold/30 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm appearance-none cursor-pointer">
                        <option value="urgent">Most urgent</option>
                        <option value="recent">Recently added</option>
                        <option value="closest">Closest to me</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-charcoal-light">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>


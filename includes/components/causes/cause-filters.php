
<form method="GET" action="<?= baseUrl('?page=causes') ?>">

    <input type="hidden" name="page" value="causes">

    <!-- 02 - Contribution Type Selector -->
    <section class="bg-white border-y border-gold/10 sticky top-20 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center py-4 gap-4">
                <span class="text-xs font-bold text-charcoal uppercase tracking-widest whitespace-nowrap shrink-0">I want to help with</span>
                
                <div class="overflow-x-auto pb-2 md:pb-0 hide-scrollbar -mx-4 px-4 md:mx-0 md:px-0">
                    <div class="flex gap-2">

                        <?php
                        $filterTypes = [
                            'all' => 'Everything',
                            'money' => 'Money',
                            'food' => 'Food',
                            'clothes' => 'Clothes',
                            'books' => 'Books',
                            'items' => 'Useful Items',
                            'time' => 'Time'
                        ];
                        ?>

                        <?php foreach ($filterTypes as $filterValue => $filterLabel): ?>

                            <button
                                type="submit"
                                name="type"
                                value="<?= $filterValue ?>"
                                class="cause-filter-btn <?= $type == $filterValue ? 'bg-forest text-ivory border-forest' : 'bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white' ?> px-5 py-2 rounded-full border text-sm font-medium transition-colors whitespace-nowrap">
                                <?= $filterLabel ?>
                            </button>

                        <?php endforeach; ?>

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
                        name="search"
                        id="searchQuery" 
                        value="<?= $search ?>"
                        class="block w-full pl-10 pr-3 py-2 border border-gold/30 rounded-lg leading-5 bg-white placeholder-charcoal-light focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm transition-colors" 
                        placeholder="Search causes, NGOs or needs..."
                    >
                </div>

                <div class="flex w-full md:w-auto gap-4">
                    <!-- Location -->
                    <div class="relative flex-1 md:w-48">
                        <select name="location" id="locationFilter" class="block w-full pl-3 pr-10 py-2 border border-gold/30 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm appearance-none cursor-pointer">
                            <option value="all">Any location</option>

                            <?php if ($locationsResult): ?>
                                <?php while ($locationRow = mysqli_fetch_assoc($locationsResult)): ?>

                                    <option value="<?= $locationRow['city'] ?>" <?= $location == $locationRow['city'] ? 'selected' : '' ?>>
                                        <?= $locationRow['city'] ?>
                                    </option>

                                <?php endwhile; ?>
                            <?php endif; ?>

                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-charcoal-light">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="relative flex-1 md:w-48">
                        <select name="sort" id="sortFilter" class="block w-full pl-3 pr-10 py-2 border border-gold/30 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest sm:text-sm appearance-none cursor-pointer">
                            <option value="urgent" <?= $sort == 'urgent' ? 'selected' : '' ?>>Most urgent</option>
                            <option value="recent" <?= $sort == 'recent' ? 'selected' : '' ?>>Recently added</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-charcoal-light">
                            <svg class="h-4 w-4 text-charcoal-light" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-forest text-ivory px-6 py-2 rounded-lg text-sm font-medium hover:bg-forest-dark transition-colors">
                    Apply Filters
                </button>

            </div>
        </div>
    </section>

</form>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

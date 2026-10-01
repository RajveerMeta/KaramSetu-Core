<?php /* props: 
    'id',
    'title',
    'category',
    'date',
    'ngoName',
    'location',
    'description',
    'entryFee',
    'status' => 'Open',
    'featured' => false,
    'image'
 */ ?>

<?php
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = date('M', $timestamp);
    $year = date('Y', $timestamp);
?>

<div class="bg-white rounded-2xl overflow-hidden <?= e($featured ? 'border-2 border-gold/40 shadow-lg' : 'border border-gold/20 shadow-sm') ?> hover:-translate-y-1 hover:border-forest/40 hover:shadow-xl transition-all duration-300 flex flex-col h-full group w-full">
    
    <!-- Image Area -->
    <div class="w-full aspect-[16/9] relative overflow-hidden shrink-0 bg-forest/5">
        <!-- Event Image -->
        <img src="<?= e($image) ?>" alt="<?= e($title) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        
        <!-- Dark overlay to ensure badge readability -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

        <!-- Badges Overlay (Flex row to prevent overlap) -->
        <div class="absolute top-4 left-4 right-4 z-20 flex justify-between items-start gap-2">
            <!-- Category Badge (Top Left) -->
            <div class="shrink min-w-0">
                <span class="inline-block bg-gold text-forest font-bold tracking-widest uppercase text-[10px] px-3 py-1.5 rounded-full shadow-md truncate max-w-full">
                    <?= e($category) ?>
                </span>
            </div>

            <!-- Price/Status Badges (Top Right) -->
            <div class="flex flex-col items-end gap-2 shrink-0">
                <span class="inline-block bg-white text-forest font-bold tracking-widest uppercase text-[10px] px-3 py-1.5 rounded-full shadow-md border border-gold/20">
                    <?= e($entryFee) ?>
                </span>
                <?php if($status !== 'Open'): ?>
                    <span class="inline-block <?= e($status === 'Sold Out' ? 'bg-red-500' : 'bg-amber-500') ?> text-white font-bold tracking-widest uppercase text-[10px] px-3 py-1.5 rounded-full shadow-md">
                        <?= e($status) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Unified Content Area -->
    <div class="p-6 md:p-6 flex-1 flex flex-col min-h-0 relative z-10 bg-white">
        
        <div class="mb-3 shrink-0">
            <p class="text-[10px] font-bold text-charcoal-light flex items-center uppercase tracking-widest w-full">
                <svg class="w-3.5 h-3.5 mr-1.5 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="break-words"><?= e($ngoName) ?></span>
            </p>
        </div>

        <!-- Title -->
        <h3 class="<?= e($featured ? 'text-2xl' : 'text-xl') ?> font-serif text-forest mb-4 group-hover:text-gold transition-colors leading-snug break-words shrink-0">
            <?= e($title) ?>
        </h3>
        
        <!-- Description -->
        <div class="mb-6 flex-1 min-h-0">
            <p class="text-charcoal-light text-sm font-normal line-clamp-2 leading-relaxed break-words"><?= e($description) ?></p>
        </div>

        <!-- Metadata Footer -->
        <div class="shrink-0 pt-4 border-t border-gold/10 flex flex-col gap-4">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-medium text-charcoal w-full">
                <div class="flex items-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span><?= e($day) ?> <?= e($month) ?> <?= e($year) ?></span>
                </div>
                <div class="flex items-center gap-1.5 min-w-0 flex-1">
                    <svg class="w-4 h-4 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="truncate"><?= e($location) ?></span>
                </div>
            </div>
            
            <a href="<?= baseUrl('?page=event-details&id=' . $id)  ?>" class="w-full text-center bg-ivory text-forest border border-gold/20 hover:bg-forest hover:text-ivory hover:border-forest px-4 py-3 rounded-xl text-sm font-bold transition-all flex justify-between items-center group/btn shrink-0">
                <span>View Event</span>
                <svg class="w-4 h-4 shrink-0 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        
    </div>
</div>




<?php /* props: 
    'id' => null,
    'name',
    'category',
    'location',
    'description',
    'accepts' => [],
    'verified' => false
 */ ?>

<div class="bg-white rounded-2xl overflow-hidden border border-gold/20 shadow-sm hover:shadow-lg transition-all flex flex-col h-full group">
    
    <!-- Image Area -->
    <div class="h-32 relative border-b border-gold/10 overflow-hidden bg-forest flex items-center justify-center shrink-0">
        <!-- CSS Fallback -->
        <span class="absolute text-3xl font-serif text-ivory/30 select-none"><?= e(substr($name, 0, 1)) ?></span>

        <?php
            $imageUrl = 'https://images.unsplash.com/photo-1593113565694-c6f1752b0be6?w=600&q=80';
        ?>
        
        <img src="<?= e($imageUrl) ?>" 
             alt="<?= e($name) ?>" 
             class="relative z-10 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
             onerror="this.style.display='none';"
        >
        
        <!-- Verified Badge -->
        <?php if($verified): ?>
        <div class="absolute top-3 right-3 z-20">
            <span class="inline-flex items-center gap-1 bg-green-500 text-white shadow-md text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> Verified
            </span>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gold/10 relative shrink-0">
        <div class="mb-2">
            <span class="text-[11px] font-bold text-gold uppercase tracking-widest"><?= e($category) ?></span>
        </div>

        <h3 class="text-xl md:text-2xl font-serif text-forest mb-2 group-hover:text-gold transition-colors leading-tight"><?= e($name) ?></h3>
        
        <p class="text-sm text-charcoal-light flex items-center font-medium">
            <svg class="w-4 h-4 mr-1.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <?= e($location) ?>
        </p>
    </div>

    <!-- Body -->
<div class="px-6 pt-5 pb-10 flex-1 flex flex-col">        
        <div class="mb-5 flex-1">
            <span class="block text-[11px] font-bold text-charcoal/60 uppercase tracking-widest mb-2">Mission</span>
            <p class="text-charcoal-light text-sm font-medium leading-relaxed"><?= e($description) ?></p>
        </div>

        <!-- Supported Contribution Types -->
</br>
        <div class="mb-6 shrink-0">
            <span class="block text-[10px] font-bold text-charcoal/50 uppercase tracking-wider mb-3">Ways to Contribute</span>
            <div class="flex flex-wrap gap-2">
                <?php foreach($accepts as $type): ?>
                    <span class="bg-ivory border border-gold/30 text-charcoal text-[11px] px-3 py-1 rounded font-medium capitalize shadow-sm">
                        <?= e($type == 'items' ? 'Useful Items' : $type) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action -->
        <div class="mt-auto pt-5 shrink-0">
            <a href="<?= $id ? baseUrl('?page=ngo-profile&id=' . $id) : '#' ?>" class="w-full text-center bg-forest/5 text-forest border border-forest/20 hover:bg-forest hover:text-ivory hover:shadow-md px-4 py-3 rounded-xl text-sm font-bold transition-all flex justify-center gap-2 items-center group/btn">
                <span>View NGO</span>
                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        
    </div>
</div>





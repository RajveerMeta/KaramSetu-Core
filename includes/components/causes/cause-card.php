<?php /* props: 
    'id' => 1,
    'title',
    'needText',
    'location',
    'progress',
    'accepts' => [],
    'ctaText' => 'View Cause',
    'status' => 'active' // active, urgent, nearly-complete, completed
 */ ?>

<div class="bg-white rounded-2xl overflow-hidden border border-gold/20 shadow-sm hover:shadow-lg transition-all flex flex-col h-full group cause-card-item" data-accepts="<?= e(htmlspecialchars(json_encode($accepts))) ?>" data-location="<?= e(strtolower($location)) ?>" data-title="<?= e(htmlspecialchars(strtolower($title))) ?>" data-needtext="<?= e(htmlspecialchars(strtolower($needText))) ?>" data-status="<?= e($status) ?>" data-rand="<?= e(rand(1, 10)) ?>" 
    class="cause-card-item cause-card-wrapper" data-accepts="<?= e(htmlspecialchars(json_encode($accepts))) ?>" data-location="<?= e(strtolower($location)) ?>" data-title="<?= e(htmlspecialchars(strtolower($title))) ?>" data-needtext="<?= e(htmlspecialchars(strtolower($needText))) ?>" data-status="<?= e($status) ?>" data-rand="<?= e(rand(1, 10)) ?>">
    
    <!-- Header -->
    <div class="p-6 border-b border-gold/10 relative overflow-hidden">
        <!-- Status Badge -->
        <div class="mb-4">
            <?php if($status === 'urgent'): ?>
                <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span> Urgent
                </span>
            <?php elseif($status === 'nearly-complete'): ?>
                <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider border border-green-100">
                    Nearly Complete
                </span>
            <?php elseif($status === 'completed'): ?>
                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider border border-gray-200">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Completed
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 bg-forest/5 text-forest text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider border border-forest/10">
                    Active
                </span>
            <?php endif; ?>
        </div>

        <h3 class="text-xl font-serif text-forest mb-2 line-clamp-2 group-hover:text-gold transition-colors"><?= e($title) ?></h3>
        
        <p class="text-sm text-charcoal-light flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <?= e($location) ?>
        </p>

        <!-- Subtle bg pattern on hover -->
        <svg class="absolute -right-10 -bottom-10 w-40 h-40 opacity-0 group-hover:opacity-5 text-gold transition-opacity duration-500 pointer-events-none" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4" />
        </svg>
    </div>

    <!-- Body -->
    <div class="p-6 flex-1 flex flex-col">
        
        <div class="mb-6 flex-1">
            <span class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1">Need</span>
            <p class="text-charcoal-light text-sm font-medium"><?= e($needText) ?></p>
        </div>

        <!-- Progress -->
        <div class="mb-6">
            <div class="flex justify-between text-xs font-bold mb-2">
                <span class="text-forest uppercase tracking-wider">Progress</span>
                <span class="text-charcoal"><?= e($progress) ?>%</span>
            </div>
            <div class="w-full bg-ivory-dark rounded-full h-1.5">
                <div class="bg-gold h-1.5 rounded-full" style="width: <?= e($progress) ?>%"></div>
            </div>
        </div>

        <!-- Supported Types -->
        <div class="mb-6">
            <span class="block text-[10px] font-bold text-charcoal-light uppercase tracking-wider mb-2">Support Types</span>
            <div class="flex flex-wrap gap-1.5">
                <?php foreach($accepts as $type): ?>
                    <span class="bg-ivory border border-gold/30 text-charcoal text-[10px] px-2 py-1 rounded font-medium capitalize">
                        <?= e($type == 'items' ? 'Useful Items' : $type) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action -->
        <div class="mt-auto pt-4 border-t border-gold/10">
            <a href="<?= baseUrl('?page=contribute&id=' . e($id)) ?>" class="w-full bg-forest/5 text-forest border border-forest/20 hover:bg-forest hover:text-ivory px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex justify-between items-center group/btn">
                <span><?= e($ctaText) ?></span>
                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        
    </div>
</div>




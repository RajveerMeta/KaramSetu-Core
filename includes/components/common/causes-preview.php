<?php
$causes_preview_query = "
    SELECT 
        c.*,
        n.name AS ngo_name,
        n.city AS ngo_city,
        n.state AS ngo_state,
        COALESCE((
            SELECT SUM(co.monetary_amount)
            FROM contributions co
            WHERE co.cause_id = c.id
            AND co.payment_status = 'completed'
        ), 0) AS raised_amount,
        (
            SELECT GROUP_CONCAT(DISTINCT cat.contribution_type SEPARATOR ' ')
            FROM cause_accepted_types cat
            WHERE cat.cause_id = c.id
        ) AS accepted_types_str
    FROM causes c
    INNER JOIN ngos n ON n.id = c.ngo_id
    WHERE c.status = 'active'
    AND n.verification_status = 'approved'
    ORDER BY c.id DESC
    LIMIT 6
";

$causes_preview_res = mysqli_query($con, $causes_preview_query);
$has_causes = ($causes_preview_res && mysqli_num_rows($causes_preview_res) > 0);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    
    <?php if ($has_causes): ?>
        <?php while ($cause_row = mysqli_fetch_assoc($causes_preview_res)): ?>
            <?php
            $cause_img = 'https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
            if (!empty($cause_row['cause_image'])) {
                if (strpos($cause_row['cause_image'], 'uploads/') === 0) {
                    $cause_img = baseUrl($cause_row['cause_image']);
                } else {
                    $cause_img = baseUrl('uploads/causes/' . basename($cause_row['cause_image']));
                }
            }
            
            $data_filter = strtolower(trim(($cause_row['category'] ?? '') . ' ' . ($cause_row['accepted_types_str'] ?? '')));
            $target = (float) $cause_row['target_amount'];
            $raised = (float) $cause_row['raised_amount'];
            $pct = $target > 0 ? min(100, round(($raised / $target) * 100)) : 0;
            $needed = max(0, $target - $raised);
            $loc = (!empty($cause_row['ngo_city']) && !empty($cause_row['ngo_state'])) 
                ? ($cause_row['ngo_city'] . ', ' . $cause_row['ngo_state']) 
                : ($cause_row['ngo_city'] ?? ($cause_row['ngo_state'] ?? 'India'));
            ?>
            <div class="cause-preview-item bg-white rounded-2xl overflow-hidden border border-gold/20 shadow-sm hover:shadow-lg transition-all group" data-filter="<?= $data_filter ?>">
                <div class="h-48 bg-gray-200 relative overflow-hidden">
                    <img src="<?= $cause_img ?>" alt="<?= $cause_row['title'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-forest capitalize"><?= $cause_row['category'] ?></div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-serif text-forest mb-2"><?= $cause_row['title'] ?></h3>
                    <p class="text-sm text-charcoal-light mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <?= $loc ?>
                    </p>
                    
                    <div class="mb-4">
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-forest">Progress</span>
                            <span class="text-charcoal-light">₹<?= number_format($raised) ?> / ₹<?= number_format($target) ?></span>
                        </div>
                        <div class="w-full bg-ivory-dark rounded-full h-2">
                            <div class="bg-gold h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gold/10">
                        <span class="text-sm font-medium text-charcoal">Needed: <span class="text-forest font-bold">₹<?= number_format($needed) ?></span></span>
                        <a href="<?= baseUrl('?page=contribute&id=' . $cause_row['id']) ?>" class="bg-forest text-ivory px-4 py-2 rounded-full text-xs font-medium hover:bg-forest-dark transition-colors inline-block">Support</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gold/20 p-8 shadow-sm">
            <div class="w-16 h-16 bg-ivory rounded-full flex items-center justify-center mx-auto mb-4 text-forest font-serif text-2xl font-bold">
                KS
            </div>
            <h3 class="text-2xl font-serif text-forest mb-2">No Active Causes Found</h3>
            <p class="text-sm text-charcoal-light max-w-md mx-auto mb-6">There are currently no active fundraising causes available. Please check back soon or explore our verified NGO partners.</p>
            <a href="<?= baseUrl('?page=ngos') ?>" class="inline-block bg-forest text-ivory px-6 py-2.5 rounded-full text-xs font-bold hover:bg-forest-dark transition-colors">
                Explore NGOs
            </a>
        </div>
    <?php endif; ?>

    <div id="causesEmptyFilter" style="display:none;" class="col-span-full text-center py-12 bg-white rounded-2xl border border-gold/20 p-6 shadow-sm">
        <p class="text-base text-charcoal font-medium mb-1">No causes found for this category</p>
        <p class="text-xs text-charcoal-light">Try selecting a different need or view all causes.</p>
    </div>

</div>

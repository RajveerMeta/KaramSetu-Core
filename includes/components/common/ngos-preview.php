<?php
$ngos_preview_query = "
    SELECT 
        n.id, 
        n.name, 
        n.primary_cause, 
        n.city, 
        n.state,
        (
            SELECT c.title 
            FROM causes c 
            WHERE c.ngo_id = n.id AND c.status = 'active' 
            ORDER BY c.id DESC LIMIT 1
        ) AS active_cause_title,
        (
            SELECT GROUP_CONCAT(DISTINCT cat.contribution_type SEPARATOR ',')
            FROM causes c
            INNER JOIN cause_accepted_types cat ON cat.cause_id = c.id
            WHERE c.ngo_id = n.id AND c.status = 'active'
        ) AS accepted_types
    FROM ngos n
    WHERE n.verification_status = 'approved'
    ORDER BY n.id DESC
    LIMIT 3
";

$ngos_preview_res = mysqli_query($con, $ngos_preview_query);
$has_ngos = ($ngos_preview_res && mysqli_num_rows($ngos_preview_res) > 0);
?>

<section class="py-24 bg-ivory-dark relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mb-16">
            <h2 class="text-4xl md:text-5xl font-serif text-forest mb-6">On the other side of every contribution is a real organization doing the work.</h2>
            <p class="text-charcoal-light text-lg">We strictly vet all our NGO partners to ensure your help reaches those who truly need it.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <?php if ($has_ngos): ?>
                <?php while ($ngo = mysqli_fetch_assoc($ngos_preview_res)): ?>
                    <?php
                    $initial = strtoupper(substr(trim($ngo['name'] ?? 'N'), 0, 1));
                    $location_str = (!empty($ngo['city']) && !empty($ngo['state'])) 
                        ? ($ngo['city'] . ', ' . $ngo['state']) 
                        : ($ngo['city'] ?? ($ngo['state'] ?? 'India'));
                    $active_initiative = !empty($ngo['active_cause_title']) 
                        ? ('Active: ' . $ngo['active_cause_title']) 
                        : 'Active in community outreach';
                    
                    $types_arr = !empty($ngo['accepted_types']) ? explode(',', $ngo['accepted_types']) : ['money'];
                    $types_arr = array_slice($types_arr, 0, 3);
                    ?>
                    <div class="bg-white rounded-2xl p-6 border border-gold/20 shadow-sm hover:shadow-lg hover:border-gold transition-all group flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-forest/10 rounded-full flex items-center justify-center text-forest font-bold text-xl"><?= $initial ?></div>
                                <span class="bg-gold/20 text-forest text-xs px-2 py-1 rounded-full font-bold flex items-center gap-1">
                                    <svg class="w-3 h-3 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Verified
                                </span>
                            </div>
                            <h3 class="text-lg font-serif text-forest mb-1">
                                <a href="<?= baseUrl('?page=ngo-profile&id=' . $ngo['id']) ?>" class="hover:text-gold transition-colors"><?= $ngo['name'] ?></a>
                            </h3>
                            <p class="text-xs text-gold uppercase tracking-wider mb-4"><?= !empty($ngo['primary_cause']) ? $ngo['primary_cause'] : 'Community Welfare' ?></p>
                            
                            <div class="space-y-2 mb-6 text-sm text-charcoal-light">
                                <p class="flex items-center gap-2"><svg class="w-4 h-4 text-forest/50 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> <?= $location_str ?></p>
                                <p class="flex items-center gap-2 truncate"><svg class="w-4 h-4 text-forest/50 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> <span class="truncate"><?= $active_initiative ?></span></p>
                            </div>
                        </div>
                        
                        <div class="border-t border-gold/10 pt-4">
                            <p class="text-xs text-charcoal font-medium mb-2">Support needed:</p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($types_arr as $t): ?>
                                    <span class="bg-ivory text-forest text-xs px-2 py-1 rounded border border-gold/20 capitalize"><?= trim($t) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white rounded-2xl p-6 border border-gold/20 shadow-sm text-center flex flex-col justify-center items-center">
                    <p class="text-forest font-serif text-lg mb-2">No verified NGOs found</p>
                    <p class="text-xs text-charcoal-light">NGO applications are currently under verification.</p>
                </div>
            <?php endif; ?>

            <!-- See All -->
            <a href="<?= baseUrl('?page=ngos') ?>" class="bg-forest/5 rounded-2xl p-6 border border-forest/10 hover:bg-forest hover:border-forest text-forest transition-all group flex flex-col justify-center items-center text-center min-h-[300px]">
                <div class="w-16 h-16 rounded-full bg-white group-hover:bg-ivory-dark flex items-center justify-center mb-4 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
                <h3 class="text-xl font-serif mb-2 group-hover:text-ivory">Meet verified NGOs</h3>
                <p class="text-sm opacity-80 group-hover:text-ivory">Explore organizations making a difference across India.</p>
            </a>

        </div>
    </div>
</section>

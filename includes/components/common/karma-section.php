<?php
$monthly_leaderboard_query = "
    SELECT 
        u.id,
        u.name,
        SUM(kt.points) AS total_karma,
        (
            SELECT COUNT(*) 
            FROM contributions c 
            WHERE c.user_id = u.id 
            AND c.payment_status IN ('completed', 'not_applicable')
            AND c.fulfillment_status != 'cancelled'
            AND MONTH(c.created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(c.created_at) = YEAR(CURRENT_DATE())
        ) AS monthly_contributions
    FROM karma_transactions kt
    INNER JOIN users u ON u.id = kt.user_id
    WHERE MONTH(kt.created_at) = MONTH(CURRENT_DATE())
      AND YEAR(kt.created_at) = YEAR(CURRENT_DATE())
      AND u.status = 'active'
    GROUP BY u.id, u.name
    HAVING total_karma > 0
    ORDER BY total_karma DESC
    LIMIT 3
";

$leaderboard_res = mysqli_query($con, $monthly_leaderboard_query);
$leaders = [];
if ($leaderboard_res) {
    while ($lrow = mysqli_fetch_assoc($leaderboard_res)) {
        $leaders[] = $lrow;
    }
}
?>

<section class="py-24 bg-ivory-dark border-y border-gold/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-serif text-forest mb-4">Your kindness leaves a trail.</h2>
            <p class="text-charcoal-light text-lg max-w-2xl mx-auto">Every action on KarmaSetu contributes to your Karma Score—a reflection of your positive impact on the community.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <!-- Journey Visualization -->
            <div class="relative pl-8 md:pl-16">
                <!-- Vertical Line -->
                <div class="absolute left-0 top-0 bottom-0 w-px bg-gold/30"></div>
                
                <div class="space-y-12">
                    <div class="relative">
                        <div class="absolute -left-10 w-4 h-4 rounded-full bg-forest border-2 border-ivory-dark shadow-[0_0_0_4px_rgba(26,64,39,0.1)]"></div>
                        <h4 class="text-lg font-serif text-forest mb-1">Food Donated</h4>
                        <p class="text-sm text-charcoal-light">+50 Karma Points</p>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute -left-10 w-4 h-4 rounded-full bg-forest border-2 border-ivory-dark shadow-[0_0_0_4px_rgba(26,64,39,0.1)]"></div>
                        <h4 class="text-lg font-serif text-forest mb-1">Volunteer Hours Completed</h4>
                        <p class="text-sm text-charcoal-light">+120 Karma Points</p>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute -left-10 w-4 h-4 rounded-full bg-forest border-2 border-ivory-dark shadow-[0_0_0_4px_rgba(26,64,39,0.1)]"></div>
                        <h4 class="text-lg font-serif text-forest mb-1">NGO Supported</h4>
                        <p class="text-sm text-charcoal-light">+30 Karma Points</p>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute -left-10 w-4 h-4 rounded-full bg-gold border-2 border-ivory-dark shadow-[0_0_0_4px_rgba(197,160,89,0.2)] animate-pulse"></div>
                        <h4 class="text-xl font-serif text-gold mb-1">Impact Created</h4>
                        <p class="text-sm font-medium text-forest">Community Leader Status Unlocked</p>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-gold/10">
                <div class="flex items-center justify-between mb-8 border-b border-gold/10 pb-4">
                    <h3 class="text-2xl font-serif text-forest">Community Leaders</h3>
                    <span class="text-xs font-bold uppercase tracking-wider text-gold">This Month</span>
                </div>
                
                <div class="space-y-6">
                    <?php if (!empty($leaders)): ?>
                        <?php foreach ($leaders as $idx => $leader): ?>
                            <?php
                            $rank = $idx + 1;
                            $init = strtoupper(substr(trim($leader['name'] ?? 'U'), 0, 1));
                            $contrib_cnt = (int) $leader['monthly_contributions'];
                            $contrib_label = $contrib_cnt . ' Contribution' . ($contrib_cnt === 1 ? '' : 's');
                            $rank_color = ($rank === 1) ? 'text-gold font-bold text-2xl' : 'text-charcoal-light font-bold text-xl';
                            ?>
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 text-center font-serif <?= $rank_color ?>"><?= $rank ?></div>
                                    <div class="w-12 h-12 rounded-full bg-forest/10 flex items-center justify-center text-forest font-bold"><?= $init ?></div>
                                    <div>
                                        <p class="font-bold text-charcoal group-hover:text-forest transition-colors"><?= $leader['name'] ?></p>
                                        <p class="text-xs text-charcoal-light"><?= $contrib_label ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-serif text-xl text-forest"><?= number_format($leader['total_karma']) ?></p>
                                    <p class="text-[10px] uppercase tracking-widest text-gold font-bold">Karma</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-8 text-center bg-ivory rounded-2xl border border-gold/20 p-6">
                            <p class="text-sm font-medium text-forest mb-1">No community scores for this month yet</p>
                            <p class="text-xs text-charcoal-light">Make a contribution or volunteer to earn Karma points and appear on the monthly leaderboard!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="<?= baseUrl('?page=leaderboard') ?>" class="block w-full text-center mt-8 py-3 rounded-full border border-forest text-forest hover:bg-forest hover:text-ivory transition-all font-medium text-sm">
                    View Full Leaderboard
                </a>
            </div>

        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedInUserId = (isset($_SESSION['user_id']) && strtolower(trim($_SESSION['user_role'] ?? '')) === 'user') ? (int) $_SESSION['user_id'] : 0;

$currentUser = null;
$leaderboard = [];

// Query registered active members ordered by Karma points (highest rank 1)
$sql = "
    SELECT
        u.id,
        u.name,
        COALESCE(up.karma_points, 0) AS karma_points,
        (
            SELECT COUNT(*)
            FROM contributions c
            WHERE c.user_id = u.id
              AND c.payment_status IN ('completed', 'not_applicable')
              AND c.fulfillment_status != 'cancelled'
        ) AS contributions_count,
        (
            SELECT COALESCE(SUM(va.hours_completed), 0)
            FROM volunteer_activities va
            WHERE va.user_id = u.id
              AND va.status = 'completed'
        ) AS volunteer_hours
    FROM users u
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE u.role = 'user'
      AND u.status = 'active'
    ORDER BY karma_points DESC, u.id ASC
";

$result = mysqli_query($con, $sql);
$rank = 1;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $userId = (int) $row['id'];
        $karma = (int) $row['karma_points'];
        $contribs = (int) $row['contributions_count'];
        $volHours = (float) $row['volunteer_hours'];
        $isCurrentUser = ($loggedInUserId > 0 && $userId === $loggedInUserId);

        $userData = (object)[
            'id' => $userId,
            'rank' => $rank,
            'name' => $row['name'] ?: 'Member',
            'karma_points' => number_format($karma),
            'contributions' => $contribs,
            'volunteer_hours' => ($volHours > 0 ? $volHours : 0) . 'h',
            'is_current_user' => $isCurrentUser
        ];

        if ($isCurrentUser) {
            $currentUser = (object)[
                'id' => $userId,
                'name' => $userData->name,
                'rank' => $rank,
                'karma_points' => $userData->karma_points
            ];
        }

        $leaderboard[] = $userData;
        $rank++;
    }
}

$topThree = array_slice($leaderboard, 0, 3);
$mainList = array_slice($leaderboard, 3);

$getInitials = function($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if ($word !== '') {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2) ?: 'U';
};
?>

<div class="bg-ivory/40 min-h-screen py-8 sm:py-10 lg:py-12">
    <div class="max-w-[1100px] w-full mx-auto px-5 md:px-8 lg:px-10">
        
        <!-- Page Header -->
        <div class="mb-8 text-center lg:text-left">
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-3">KarmaSetu Leaderboard</h1>
            <p class="text-charcoal-light max-w-2xl mx-auto lg:mx-0">Celebrate the people making a difference through their contributions, volunteering, and participation.</p>
        </div>

        <!-- Main Desktop Grid -->
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 mb-12 items-start">
            
            <!-- LEFT SIDEBAR -->
            <div class="w-full lg:w-[210px] min-w-0 space-y-6 shrink-0">
                
                <!-- Card 1: Your Rank -->
                <div class="w-full bg-forest rounded-xl p-5 md:p-8 shadow-sm border border-gold/30 text-white relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gold/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-ivory/80 mb-2">Your Rank</p>
                        <?php if ($currentUser): ?>
                            <div class="flex flex-col md:flex-row items-end gap-3 mb-4">
                                <span class="text-4xl font-serif font-bold text-gold">#<?= e($currentUser->rank) ?></span>
                            </div>
                            <div class="flex flex-col md:flex-row items-center gap-3 mb-4">
                                <div class="h-10 w-10 rounded-full bg-white text-forest flex flex-col md:flex-row items-center justify-center text-sm font-bold shadow-sm shrink-0">
                                    <?= e($getInitials($currentUser->name)) ?>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-white text-sm truncate"><?= e($currentUser->name) ?></h3>
                                    <p class="text-xs text-gold font-medium"><?= e($currentUser->karma_points) ?> Karma Points</p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-white/20">
                                <p class="text-xs text-ivory/80 leading-relaxed">Keep contributing to climb the leaderboard.</p>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col md:flex-row items-end gap-3 mb-4">
                                <span class="text-2xl font-serif font-bold text-gold">Track Your Karma</span>
                            </div>
                            <div class="flex flex-col md:flex-row items-center gap-3 mb-4">
                                <div class="h-10 w-10 rounded-full bg-white text-forest flex flex-col md:flex-row items-center justify-center text-sm font-bold shadow-sm shrink-0">
                                    --
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-white text-sm">Guest Visitor</h3>
                                    <p class="text-xs text-ivory/70">Sign in to view your rank</p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-white/20">
                                <a href="<?= baseUrl('auth/login.php') ?>" class="inline-block text-xs font-bold text-forest bg-white hover:bg-ivory px-4 py-2 rounded-full transition shadow-sm">Sign In</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card 2: Filters -->
                <div class="w-full bg-white rounded-xl p-5 md:p-8 shadow-sm border border-charcoal/5">
                    <h3 class="font-bold text-charcoal text-sm mb-3">Time Period</h3>
                    <div class="space-y-1">
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold bg-forest text-white shadow-sm transition-colors text-center sm:text-left">This Month</a>
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold text-charcoal hover:bg-ivory transition-colors text-center sm:text-left">This Year</a>
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold text-charcoal hover:bg-ivory transition-colors text-center sm:text-left">All Time</a>
                    </div>
                    
                    <h3 class="font-bold text-charcoal text-sm mb-3 mt-6">Category</h3>
                    <div class="space-y-1">
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold bg-ivory text-forest border border-forest/20 shadow-sm transition-colors text-center sm:text-left">All Categories</a>
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold text-charcoal hover:bg-ivory transition-colors text-center sm:text-left">Donations</a>
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold text-charcoal hover:bg-ivory transition-colors text-center sm:text-left">Volunteering</a>
                        <a href="#" class="block w-full px-3 py-2 rounded-md text-xs font-bold text-charcoal hover:bg-ivory transition-colors text-center sm:text-left">Events</a>
                    </div>
                </div>

                <!-- Card 3: How Karma Points Work -->
                <div class="w-full bg-white  rounded-xl p-5 md:p-8 shadow-sm border border-gold/20">
                    <h3 class="font-serif font-bold text-forest text-base mb-4">How Karma Points Work</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-charcoal mb-1">Donate</h4>
                            <p class="text-[11px] text-charcoal-light leading-relaxed">Earn points for meaningful financial and resource contributions.</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-charcoal mb-1">Volunteer</h4>
                            <p class="text-[11px] text-charcoal-light leading-relaxed">Earn points by giving your time and skills.</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-charcoal mb-1">Participate</h4>
                            <p class="text-[11px] text-charcoal-light leading-relaxed">Support fundraising events and community activities.</p>
                        </div>
                    </div>
                </div>
                

            </div>
            
            
            <!-- RIGHT CONTENT -->
            <div class="flex-1 min-w-0 space-y-5 lg:space-y-8">
                
                <!-- TOP 3 CONTRIBUTORS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <?php if (empty($topThree)): ?>
                        <div class="col-span-1 sm:col-span-3 bg-white rounded-xl p-8 text-center border border-charcoal/10 shadow-sm">
                            <p class="text-sm font-medium text-charcoal">No contributors found yet.</p>
                            <p class="text-xs text-charcoal-light mt-1">Be the first to contribute or volunteer to earn Karma points!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($topThree as $index => $user): ?>
                        <?php
                            $isFirst = $user->rank === 1;
                            $cardBg = $isFirst ? 'bg-gradient-to-br from-white to-ivory border-gold/40' : 'bg-white border-charcoal/10';
                            $rankColor = $isFirst ? 'text-gold' : 'text-forest';
                            $rankBg = $isFirst ? 'bg-gold/10' : 'bg-forest/5';
                        ?>
                        <div class="w-full <?= e($cardBg) ?> rounded-xl p-4 md:p-8 shadow-sm border flex flex-col items-center text-center relative overflow-hidden">
                            <?php if($isFirst): ?>
                                <div class="absolute -right-10 -top-10 w-32 h-32 bg-gold/10 rounded-full blur-2xl pointer-events-none"></div>
                            <?php endif; ?>
                            
                            <div class="<?= e($rankBg) ?> text-xl font-serif font-bold <?= e($rankColor) ?> w-10 h-10 rounded-full flex flex-col md:flex-row items-center justify-center mx-auto mb-3 border border-white/50 shadow-sm shrink-0">
                                #<?= e($user->rank) ?>
                            </div>
                            
                            <div class="h-12 w-12 rounded-full bg-forest text-white flex flex-col md:flex-row items-center justify-center text-sm font-bold mx-auto mb-3 shadow-sm shrink-0">
                                <?= e($getInitials($user->name)) ?>
                            </div>
                            
                            <h3 class="font-bold text-charcoal text-sm mb-1 truncate max-w-full"><?= e($user->name) ?></h3>
                            <p class="text-xs font-bold text-forest mb-3"><?= e($user->karma_points) ?> Karma Points</p>
                            
                            <div class="w-full pt-3 border-t border-charcoal/10 flex flex-col md:flex-row justify-between text-[10px] text-charcoal-light mt-auto">
                                <span><?= e($user->contributions) ?> Contribs</span>
                                <span><?= e($user->volunteer_hours) ?> Vol</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- COMPLETE LEADERBOARD TABLE -->
                <div class="w-full bg-white rounded-xl shadow-sm border border-charcoal/10 overflow-hidden">
                    
                    <div class="px-5 py-4 border-b border-charcoal/10 bg-white">
                        <h2 class="font-bold text-forest text-sm uppercase tracking-wider m-0">LEADERBOARD</h2>
                    </div>

                    <!-- Desktop Header -->
                    <div class="hidden md:grid grid-cols-12 gap-2 px-4 py-3 bg-ivory/60 border-b border-charcoal/10 items-center">
                        <div class="col-span-1 text-center text-[10px] font-bold uppercase text-charcoal-light">Rank</div>
                        <div class="col-span-5 text-[10px] font-bold uppercase text-charcoal-light">User</div>
                        <div class="col-span-2 text-right text-[10px] font-bold uppercase text-charcoal-light">Karma Points</div>
                        <div class="col-span-2 text-center text-[10px] font-bold uppercase text-charcoal-light">Contributions</div>
                        <div class="col-span-2 text-center text-[10px] font-bold uppercase text-charcoal-light">Volunteer Hours</div>
                    </div>
                    
                    <div class="divide-y divide-charcoal/5">
                        
                        <!-- Main List (Ranks 4+) -->
                        <?php if (empty($mainList)): ?>
                            <div class="px-5 py-8 text-center text-sm text-charcoal-light">
                                <?php if (!empty($topThree)): ?>
                                    All current contributors are featured in the top ranks above.
                                <?php else: ?>
                                    No contributors to display on the leaderboard yet.
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php foreach($mainList as $user): ?>
                            <?php
                                $rowClass = $user->is_current_user ? 'bg-forest/5 border-l-4 border-l-forest' : 'hover:bg-ivory/30 transition-colors border-l-4 border-l-transparent';
                                $nameClass = $user->is_current_user ? 'text-forest' : 'text-charcoal';
                            ?>
                            
                            <div class="w-full <?= e($rowClass) ?> border-b border-charcoal/5 last:border-b-0">
                                
                              
                                
                                <!-- DESKTOP GRID -->
                                <div class="hidden md:grid grid-cols-12 gap-2 px-4 py-3 items-center">

        <!-- Rank -->
        <div class="col-span-1 text-center">
            <span class="font-serif font-bold text-base text-charcoal-light">
                #<?= e($user->rank) ?>
            </span>
        </div>

        <!-- User -->
        <div class="col-span-5 flex flex-col md:flex-row items-center gap-3 min-w-0">

            <div class="h-10 w-10 rounded-full bg-forest text-white flex flex-col md:flex-row items-center justify-center text-[11px] font-bold shrink-0">
                <?= e($getInitials($user->name)) ?>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-2 min-w-0">

                <h4 class="font-bold text-sm <?= e($nameClass) ?> truncate">
                    <?= e($user->name) ?>
                </h4>

                <?php if ($user->is_current_user): ?>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-forest text-white shrink-0">
                        YOU
                    </span>
                <?php endif; ?>

            </div>

        </div>

        <!-- Karma Points -->
        <div class="col-span-2 text-right">
            <span class="font-bold text-sm text-forest">
                <?= e($user->karma_points) ?>
            </span>
        </div>

        <!-- Contributions -->
        <div class="col-span-2 text-center">
            <span class="text-sm font-medium text-charcoal">
                <?= e($user->contributions) ?>
            </span>
        </div>

        <!-- Volunteer Hours -->
        <div class="col-span-2 text-center">
            <span class="text-sm font-medium text-charcoal">
                <?= e($user->volunteer_hours) ?>
            </span>
        </div>

    </div>
                                                            
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
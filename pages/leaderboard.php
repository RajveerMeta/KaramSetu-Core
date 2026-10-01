<?php
    $currentUser = (object)[
        'name' => 'Rajveer',
        'rank' => 10,
        'karma_points' => '1,280'
    ];

    $leaderboard = [
        (object)['rank' => 1, 'name' => 'Aarav Shah', 'karma_points' => '2,450', 'contributions' => 15, 'volunteer_hours' => '24h', 'is_current_user' => false],
        (object)['rank' => 2, 'name' => 'Ananya Patel', 'karma_points' => '2,180', 'contributions' => 13, 'volunteer_hours' => '20h', 'is_current_user' => false],
        (object)['rank' => 3, 'name' => 'Dev Mehta', 'karma_points' => '1,950', 'contributions' => 11, 'volunteer_hours' => '18h', 'is_current_user' => false],
        (object)['rank' => 4, 'name' => 'Priya Shah', 'karma_points' => '1,760', 'contributions' => 10, 'volunteer_hours' => '16h', 'is_current_user' => false],
        (object)['rank' => 5, 'name' => 'Rohan Patel', 'karma_points' => '1,620', 'contributions' => 9, 'volunteer_hours' => '14h', 'is_current_user' => false],
        (object)['rank' => 6, 'name' => 'Neha Joshi', 'karma_points' => '1,540', 'contributions' => 9, 'volunteer_hours' => '13h', 'is_current_user' => false],
        (object)['rank' => 7, 'name' => 'Karan Mehta', 'karma_points' => '1,430', 'contributions' => 8, 'volunteer_hours' => '12h', 'is_current_user' => false],
        (object)['rank' => 8, 'name' => 'Isha Patel', 'karma_points' => '1,390', 'contributions' => 8, 'volunteer_hours' => '11h', 'is_current_user' => false],
        (object)['rank' => 9, 'name' => 'Mehul Shah', 'karma_points' => '1,340', 'contributions' => 7, 'volunteer_hours' => '10h', 'is_current_user' => false],
        (object)['rank' => 10, 'name' => 'Rajveer', 'karma_points' => '1,280', 'contributions' => 8, 'volunteer_hours' => '12h', 'is_current_user' => true],
        (object)['rank' => 11, 'name' => 'Aisha Desai', 'karma_points' => '1,150', 'contributions' => 6, 'volunteer_hours' => '8h', 'is_current_user' => false],
        (object)['rank' => 12, 'name' => 'Vikram Singh', 'karma_points' => '1,090', 'contributions' => 5, 'volunteer_hours' => '9h', 'is_current_user' => false],
    ];

    $topThree = array_slice($leaderboard, 0, 3);
    $mainList = array_slice($leaderboard, 3);

    $getInitials = function($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
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
                        <div class="flex flex-col md:flex-row items-end gap-3 mb-4">
                            <span class="text-4xl font-serif font-bold text-gold">#<?= e($currentUser->rank) ?></span>
                        </div>
                        <div class="flex flex-col md:flex-row items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-full bg-white text-forest flex flex-col md:flex-row items-center justify-center text-sm font-bold shadow-sm shrink-0">
                                <?= e($getInitials($currentUser->name)) ?>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-white text-sm"><?= e($currentUser->name) ?></h3>
                                <p class="text-xs text-gold font-medium"><?= e($currentUser->karma_points) ?> Karma Points</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-xs text-ivory/80 leading-relaxed">Keep contributing to climb the leaderboard.</p>
                        </div>
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
                        
                        <h3 class="font-bold text-charcoal text-sm mb-1"><?= e($user->name) ?></h3>
                        <p class="text-xs font-bold text-forest mb-3"><?= e($user->karma_points) ?> Karma Points</p>
                        
                        <div class="w-full pt-3 border-t border-charcoal/10 flex flex-col md:flex-row justify-between text-[10px] text-charcoal-light mt-auto">
                            <span><?= e($user->contributions) ?> Contribs</span>
                            <span><?= e($user->volunteer_hours) ?> Vol</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
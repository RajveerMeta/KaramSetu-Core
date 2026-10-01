<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>



<?php
    $user = (object)[
        'name' => 'Rajveer',
        'email' => 'rajveer@example.com',
        'member_since' => 'August 2026',
        'total_contributions' => '₹12,500',
        'contributions_count' => 8,
        'karma_points' => '1,280',
        'events_joined' => 3,
        'events_upcoming' => 2,
        'volunteer_activities' => 5,
        'volunteer_hours' => 12,
        'next_reward' => '1,500 points',
        'next_reward_name' => 'Silver Member',
        'progress_percentage' => 85,
        'rank' => 24,
        'next_activity' => 'Community Food Distribution'
    ];

    $contributions = [
        (object)['id' => 1, 'title' => 'Education Support', 'type' => 'Donation', 'amount' => '₹2,000', 'date' => '28 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 2, 'title' => 'Food Distribution Drive', 'type' => 'Food', 'amount' => '—', 'date' => '25 Aug 2026', 'status' => 'Completed'],
        (object)['id' => 3, 'title' => 'Community Clothing Drive', 'type' => 'Clothes', 'amount' => '—', 'date' => '20 Aug 2026', 'status' => 'Completed'],
    ];

    $upcomingEvents = [
        (object)['id' => 101, 'name' => 'Charity Music Evening', 'date' => '15 Sep 2026', 'location' => 'Ahmedabad', 'ticket' => '₹500', 'status' => 'Booked'],
        (object)['id' => 102, 'name' => 'Community Food Fundraiser', 'date' => '22 Sep 2026', 'location' => 'Ahmedabad', 'ticket' => '₹250', 'status' => 'Booked'],
    ];
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Quick Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-2">Welcome back, <?= e($user->name) ?>!</h1>
                <p class="text-charcoal-light">Thank you for being part of the KarmaSetu community.</p>
            </div>
            
            <div class="flex flex-col md:flex-row flex-wrap gap-3">
                <a href="<?= baseUrl('?page=causes') ?>"  class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-forest hover:bg-forest-dark transition-colors">
                    Donate Now
                </a>
                <a href="<?= baseUrl('?page=causes') ?>"  class="inline-flex items-center justify-center px-5 py-2.5 border border-charcoal/10 text-sm font-bold rounded-full text-charcoal bg-white hover:bg-ivory-dark transition-colors shadow-sm hover:shadow-md">
                    Explore Causes
                </a>
                <a href="<?= baseUrl('?page=events') ?>"  class="inline-flex items-center justify-center px-5 py-2.5 border border-charcoal/10 text-sm font-bold rounded-full text-charcoal bg-white hover:bg-ivory-dark transition-colors shadow-sm hover:shadow-md">
                    Browse Events
                </a>
                <a href="#" class="inline-flex items-center justify-center px-5 py-2.5 border border-charcoal/10 text-sm font-bold rounded-full text-charcoal bg-white hover:bg-ivory-dark transition-colors shadow-sm hover:shadow-md">
                    Volunteer
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN (Main Content) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- STATISTICS CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                        <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Total Contributions</p>
                        <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= e($user->total_contributions) ?></p>
                        <p class="text-xs font-medium text-charcoal-light">Across <?= e($user->contributions_count) ?> contributions</p>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center relative overflow-hidden transition-shadow hover:shadow-md">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-gold/10 rounded-full blur-xl pointer-events-none"></div>
                        <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1 relative z-10">Karma Points</p>
                        <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1 relative z-10"><?= e($user->karma_points) ?></p>
                        <p class="text-xs font-medium text-charcoal-light relative z-10">Keep making a difference</p>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                        <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Events Joined</p>
                        <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= e($user->events_joined) ?></p>
                        <p class="text-xs font-medium text-charcoal-light"><?= e($user->events_upcoming) ?> upcoming</p>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                        <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Volunteer Activities</p>
                        <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1"><?= e($user->volunteer_activities) ?></p>
                        <p class="text-xs font-medium text-charcoal-light">Activities completed</p>
                    </div>
                </div>

                <!-- KARMA POINTS / IMPACT SUMMARY -->
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-forest/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                            <div>
                                <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest">Your Karma Impact</h2>
                                <p class="text-sm text-charcoal-light mt-1 max-w-sm">You're making a meaningful difference through your contributions and participation.</p>
                            </div>
                            <div class="mt-4 sm:mt-0 text-left sm:text-right">
                                <span class="text-sm text-charcoal-light block mb-1 font-medium">Karma Points</span>
                                <span class="text-4xl font-serif font-bold text-gold"><?= e($user->karma_points) ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex flex-col md:flex-row justify-between text-sm mb-2">
                                <span class="font-bold text-charcoal">Next Reward: <?= e($user->next_reward_name) ?></span>
                                <span class="text-charcoal-light font-medium"><?= e($user->next_reward) ?></span>
                            </div>
                            <div class="w-full bg-ivory-dark rounded-full h-3">
                                <div class="bg-gold h-3 rounded-full transition-all duration-1000" style="width: <?= e($user->progress_percentage) ?>%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <a href="#" class="inline-flex items-center text-sm font-bold text-forest hover:text-gold transition-colors">
                                View Rewards
                                <svg class="ml-1.5 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RECENT CONTRIBUTIONS -->
                <div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center border-b border-charcoal/5">
                        <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest">Recent Contributions</h2>
                        <a href="#" class="text-sm font-bold text-forest hover:text-gold transition-colors hidden sm:block">View All Contributions</a>
                    </div>
                    
                    <div class="divide-y divide-charcoal/5">
                        <?php foreach ($contributions as $contribution): ?>
                        <div class="p-4 md:p-8 sm:px-8 sm:py-5 hover:bg-ivory/20 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row items-center gap-3 mb-1">
                                    <h3 class="font-bold text-charcoal text-base"><?= e($contribution->title) ?></h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-50 text-forest border border-green-200"><?= e($contribution->status) ?></span>
                                </div>
                                <p class="text-sm text-charcoal-light font-medium"><?= e($contribution->type) ?> &bull; <?= e($contribution->date) ?></p>
                            </div>
                            
                            <div class="flex flex-col md:flex-row items-center justify-between sm:justify-end w-full sm:w-auto gap-6 sm:gap-8">
                                <div class="text-left sm:text-right">
                                    <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-0.5">Amount</p>
                                    <p class="font-bold text-charcoal"><?= e($contribution->amount) ?></p>
                                </div>
                                
                                <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-charcoal/10 text-xs font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm">
                                    View Details
                                </a>
                            </div>

                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="p-4 md:p-8 sm:hidden border-t border-charcoal/5">
                        <a href="#" class="block text-center text-sm font-bold text-forest hover:text-gold transition-colors">View All Contributions</a>
                    </div>
                </div>

                <!-- UPCOMING EVENTS -->
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest">Upcoming Events</h2>
                        <a href="<?= baseUrl('?page=events') ?>"  class="text-sm font-bold text-forest hover:text-gold transition-colors hidden sm:block">View All Events</a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                        <?php foreach ($upcomingEvents as $event): ?>
                        <div class="border border-charcoal/10 rounded-xl p-5 md:p-8 hover:border-gold/30 transition-colors bg-ivory/10 flex flex-col h-full">
                            <div class="flex flex-col md:flex-row justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-charcoal text-base"><?= e($event->name) ?></h3>
                                    <p class="text-sm font-medium text-charcoal-light mt-1 flex flex-col md:flex-row items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?= e($event->date) ?>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200"><?= e($event->status) ?></span>
                            </div>
                            <div class="flex flex-col md:flex-row justify-between items-end mt-auto pt-5 border-t border-charcoal/10">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Location</p>
                                    <p class="text-sm font-bold text-charcoal"><?= e($event->location) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Ticket</p>
                                    <p class="text-sm font-bold text-charcoal"><?= e($event->ticket) ?></p>
                                </div>
                            </div>
                            <div class="mt-5">
                                <a href="#" class="block text-center w-full px-4 py-2.5 border border-charcoal/10 text-sm font-bold rounded-lg text-charcoal bg-white hover:bg-ivory transition-colors shadow-sm">
                                    View Ticket
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 sm:hidden">
                        <a href="<?= baseUrl('?page=events') ?>"  class="block text-center w-full text-sm font-bold text-forest hover:text-gold transition-colors">View All Events</a>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Sidebar) -->
            <div class="space-y-6 lg:space-y-8">
                
                <!-- USER PROFILE SUMMARY -->
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20">
                    <div class="flex flex-col md:flex-row items-center space-x-4 mb-5">
                        <div class="h-16 w-16 rounded-full bg-forest text-white flex flex-col md:flex-row items-center justify-center text-2xl md:text-4xl font-serif font-bold shadow-sm" id="userInitial">
                            <?= e(substr($user->name, 0, 1)) ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-serif font-bold text-forest" id="userNameDisplay"><?= e($user->name) ?></h2>
                            <p class="text-sm text-charcoal-light font-medium" id="userEmailDisplay"><?= e($user->email) ?></p>
                        </div>
                    </div>
                    <div class="pt-5 border-t border-charcoal/10">
                        <p class="text-sm text-charcoal-light mb-4 flex flex-col md:flex-row items-center justify-between">
                            <span class="font-medium">Member since:</span>
                            <span class="font-bold text-charcoal"><?= e($user->member_since) ?></span>
                        </p>
                        <div class="flex flex-col md:flex-row gap-2">
                            <a href="#" class="block text-center flex-1 px-4 py-2.5 border border-charcoal/10 text-sm font-bold rounded-full text-charcoal bg-ivory/30 hover:bg-ivory-dark transition-colors shadow-sm hover:shadow-md">
                                View Profile
                            </a>
                            <button id="demoLogoutBtn" class="block text-center flex-1 px-4 py-2.5 border border-red-200 text-sm font-bold rounded-full text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm hover:shadow-md">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- VOLUNTEER SUMMARY -->
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-forest/5 rounded-full blur-xl pointer-events-none"></div>
                    <h3 class="text-xl font-serif font-bold text-forest mb-5 relative z-10">Volunteer Activities</h3>
                    
                    <div class="space-y-4 mb-6 relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-center bg-ivory/50 px-4 py-3 rounded-xl">
                            <span class="text-sm font-medium text-charcoal-light">Activities Completed</span>
                            <span class="text-base font-bold text-forest"><?= e($user->volunteer_activities) ?></span>
                        </div>
                        <div class="flex flex-col md:flex-row justify-between items-center bg-ivory/50 px-4 py-3 rounded-xl">
                            <span class="text-sm font-medium text-charcoal-light">Hours Contributed</span>
                            <span class="text-base font-bold text-forest"><?= e($user->volunteer_hours) ?>h</span>
                        </div>
                    </div>
                    
                    <div class="p-4 md:p-8 bg-white rounded-xl border border-gold/30 mb-6 relative z-10 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1.5">Next Activity</p>
                        <p class="text-sm font-bold text-charcoal"><?= e($user->next_activity) ?></p>
                    </div>

                    <a href="#" class="block text-center w-full px-4 py-2.5 border border-charcoal/10 text-sm font-bold rounded-full text-charcoal bg-ivory/30 hover:bg-ivory-dark transition-colors shadow-sm hover:shadow-md relative z-10">
                        View Activities
                    </a>
                </div>

                <!-- LEADERBOARD PREVIEW -->
                <div class="bg-gradient-to-br from-white to-ivory rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20">
                    <h3 class="text-xl font-serif font-bold text-forest mb-5">Leaderboard</h3>
                    
                    <div class="flex flex-col md:flex-row items-center justify-between mb-6 bg-white p-4 md:p-8 rounded-xl shadow-sm border border-charcoal/5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Current Rank</p>
                            <div class="flex flex-col md:flex-row items-center">
                                <span class="text-3xl md:text-4xl font-serif font-bold text-gold">#<?= e($user->rank) ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Karma Points</p>
                            <span class="text-xl font-bold text-forest"><?= e($user->karma_points) ?></span>
                        </div>
                    </div>

                    <a href="#" class="block text-center w-full px-4 py-2.5 border border-gold/30 text-sm font-bold rounded-full text-forest hover:bg-gold hover:text-white transition-colors shadow-sm hover:shadow-md">
                        View Leaderboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const demoUserString = localStorage.getItem('karmaSetuDemoUser');
        if (demoUserString) {
            try {
                const demoUser = JSON.parse(demoUserString);
                
                const welcomeHeading = document.querySelector('h1');
                if (welcomeHeading) {
                    welcomeHeading.textContent = 'Welcome back, ' + demoUser.name + '!';
                }
                
                const userNameDisplay = document.getElementById('userNameDisplay');
                if (userNameDisplay) userNameDisplay.textContent = demoUser.name;
                
                const userEmailDisplay = document.getElementById('userEmailDisplay');
                if (userEmailDisplay) userEmailDisplay.textContent = demoUser.email;
                
                const userInitial = document.getElementById('userInitial');
                if (userInitial && demoUser.name) userInitial.textContent = demoUser.name.charAt(0);
                
            } catch (e) {
                console.error("Error parsing demo user", e);
            }
        }
        
        const logoutBtn = document.getElementById('demoLogoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem('karmaSetuDemoUser');
                window.location.href = '<?= baseUrl('auth/login.php') ?>';
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>
<?php
require_once __DIR__ . '/../includes/functions.php';

$ngos = require __DIR__ . '/../data/ngos.php';
$ngo = $ngos[0]; // Seva Roots Initiative

$all_events = require __DIR__ . '/../data/events.php';
$upcoming_events = array_slice($all_events, 0, 2);

$dashboardData = [
    'total_funds_raised' => '₹3,75,000',
    'active_causes' => 4,
    'upcoming_events_count' => 3,
    'total_contributions' => 128,
    'fundraising_raised' => 375000,
    'fundraising_goal' => 500000,
    'fundraising_progress' => 75, // percentage
    'recent_contributions' => [
        ['donor' => 'Rajveer Meta', 'type' => 'Money', 'amount' => '₹2,000', 'date' => 'Today, 10:30 AM'],
        ['donor' => 'Anonymous', 'type' => 'Food', 'amount' => '25 kg', 'date' => 'Yesterday'],
        ['donor' => 'Amit Patel', 'type' => 'Clothes', 'amount' => '15 items', 'date' => '16 Sep 2026'],
        ['donor' => 'Priya Singh', 'type' => 'Useful Items', 'amount' => '10 items', 'date' => '14 Sep 2026']
    ],
    'active_causes_list' => [
        ['name' => 'Community Development', 'raised' => '₹3,75,000', 'goal' => '₹5,00,000', 'progress' => 75],
        ['name' => 'Education Support', 'raised' => '₹1,20,000', 'goal' => '₹2,00,000', 'progress' => 60]
    ]
];

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">Welcome back, <?= htmlspecialchars($ngo['name']) ?></h1>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="bg-forest/10 text-forest font-bold px-3 py-1 rounded-full flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Verified NGO
                    </span>
                    <span class="text-charcoal-light">|</span>
                    <span class="text-charcoal-light">Dashboard Overview</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/profile.php') ?>" class="inline-block bg-white text-forest border border-forest font-bold py-2.5 px-6 rounded-full hover:bg-forest/5 transition-colors shadow-sm">
                    View Public Profile
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">
            
            <!-- MAIN DASHBOARD CONTENT (Left 2 Columns) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Total Funds</p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest"><?= $dashboardData['total_funds_raised'] ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Active Causes</p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest"><?= $dashboardData['active_causes'] ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Upcoming Events</p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest"><?= $dashboardData['upcoming_events_count'] ?></p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Contributions</p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest"><?= $dashboardData['total_contributions'] ?></p>
                    </div>
                </div>

                <!-- FUNDRAISING OVERVIEW -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
                    <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6">Fundraising Progress</h2>
                    <div class="flex flex-wrap items-end justify-between gap-4 mb-2">
                        <div>
                            <p class="text-sm font-bold text-charcoal-light">Total Raised</p>
                            <p class="text-xl md:text-3xl font-bold text-forest"><?= $dashboardData['total_funds_raised'] ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-charcoal-light">Overall Goal</p>
                            <p class="text-xl font-bold text-charcoal">₹5,00,000</p>
                        </div>
                    </div>
                    
                    <div class="w-full bg-ivory rounded-full h-4 mb-4 overflow-hidden border border-charcoal/10 shadow-inner">
                        <div class="bg-forest h-4 rounded-full" style="width: <?= $dashboardData['fundraising_progress'] ?>%"></div>
                    </div>
                    <p class="text-sm font-medium text-forest text-right"><?= $dashboardData['fundraising_progress'] ?>% to goal</p>
                </div>

                <!-- TWO COLUMNS: RECENT CONTRIBUTIONS & ACTIVE CAUSES -->
                <div class="flex flex-col md:grid md:grid-cols-2 gap-8 w-full">
                    
                    <!-- RECENT CONTRIBUTIONS -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h2 class="text-xl font-serif font-bold text-forest">Recent Contributions</h2>
                            <a href="<?= baseUrl('ngo/donations.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors">View All</a>
                        </div>
                        
                        <div class="space-y-4">
                            <?php foreach($dashboardData['recent_contributions'] as $contribution): ?>
                            <div class="flex flex-wrap items-center justify-between gap-4 p-3 bg-ivory/30 rounded-xl border border-charcoal/5">
                                <div>
                                    <p class="text-sm font-bold text-charcoal"><?= htmlspecialchars($contribution['donor']) ?></p>
                                    <p class="text-xs text-charcoal/70"><?= htmlspecialchars($contribution['type']) ?> &bull; <?= htmlspecialchars($contribution['date']) ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-sm font-bold rounded-lg"><?= htmlspecialchars($contribution['amount']) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ACTIVE CAUSES -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h2 class="text-xl font-serif font-bold text-forest">Active Causes</h2>
                            <a href="<?= baseUrl('ngo/causes.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors">View All</a>
                        </div>

                        <div class="space-y-6">
                            <?php foreach($dashboardData['active_causes_list'] as $cause): ?>
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-4 mb-1">
                                    <h3 class="text-sm font-bold text-charcoal"><?= htmlspecialchars($cause['name']) ?></h3>
                                    <span class="text-xs font-bold text-forest"><?= $cause['progress'] ?>%</span>
                                </div>
                                <p class="text-xs text-charcoal-light mb-2"><?= htmlspecialchars($cause['raised']) ?> / <?= htmlspecialchars($cause['goal']) ?></p>
                                <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                                    <div class="bg-gold h-2 rounded-full" style="width: <?= $cause['progress'] ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-8">
                            <a href="<?= baseUrl('ngo/create-cause.php') ?>" class="block w-full py-2.5 px-4 text-center border border-dashed border-gold text-gold font-bold text-sm rounded-xl hover:bg-gold/5 transition-colors">
                                + Create New Cause
                            </a>
                        </div>
                    </div>

                </div>

                <!-- UPCOMING EVENTS -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-serif font-bold text-forest">Upcoming Events</h2>
                        <a href="<?= baseUrl('ngo/events/index.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors">View All Events</a>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach($upcoming_events as $event): ?>
                        <div class="bg-ivory/30 border border-charcoal/10 rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md mb-3"><?= htmlspecialchars($event['type'] ?? 'Event') ?></span>
                                <h3 class="text-lg font-serif font-bold text-forest mb-2 leading-snug"><?= htmlspecialchars($event['title']) ?></h3>
                                <div class="flex items-center text-xs text-charcoal-light mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <?= htmlspecialchars($event['date']) ?>
                                </div>
                            </div>
                            <a href="<?= baseUrl('pages/event-details.php') ?>" class="text-sm font-bold text-gold hover:text-forest transition-colors mt-2 block">View Details &rarr;</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Sidebar) -->
            <div class="space-y-6 lg:space-y-8">
                
                <!-- VERIFICATION STATUS -->
                <div class="bg-gradient-to-br from-white to-ivory rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-forest/5 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <h3 class="text-xl font-serif font-bold text-forest mb-4 relative z-10">NGO Verification</h3>
                    
                    <div class="flex items-start space-x-4 mb-4 relative z-10">
                        <div class="w-12 h-12 rounded-full bg-forest text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-charcoal">Verified</h4>
                            <p class="text-sm text-charcoal-light leading-relaxed">Your organization has been verified by KarmaSetu. You have full access to platform features.</p>
                        </div>
                    </div>
                </div>

                <!-- PROFILE COMPLETION -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">
                    <h3 class="text-lg font-serif font-bold text-forest mb-4">Profile Completion</h3>
                    
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-sm font-bold text-charcoal">Overall Status</span>
                        <span class="text-xl font-bold text-forest">85%</span>
                    </div>
                    
                    <div class="w-full bg-ivory rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-forest h-2.5 rounded-full" style="width: 85%"></div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Organization information</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Contact information</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-charcoal-light">Address & Documents</span>
                        </div>
                        <div class="flex items-center text-sm opacity-60">
                            <div class="w-5 h-5 rounded-full border-2 border-charcoal/30 mr-2 flex-shrink-0"></div>
                            <span class="text-charcoal-light">Impact information</span>
                        </div>
                    </div>
                    
                    <a href="<?= baseUrl('ngo/profile.php') ?>" class="block mt-6 text-center text-sm font-bold text-gold hover:text-forest transition-colors">
                        Complete Profile &rarr;
                    </a>
                </div>

                <!-- QUICK ACTIONS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">
                    <h3 class="text-lg font-serif font-bold text-forest mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="<?= baseUrl('ngo/profile.php') ?>" class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-charcoal">Manage Profile</span>
                        </a>
                        
                        <a href="<?= baseUrl('ngo/create-cause.php') ?>" class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-charcoal">Create Cause</span>
                        </a>

                        <a href="<?= baseUrl('ngo/events/create.php') ?>" class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-charcoal">Create Event</span>
                        </a>

                        <a href="<?= baseUrl('ngo/donations.php') ?>" class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-charcoal">View Contributions</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/main.php';
?>

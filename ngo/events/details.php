<?php
require_once __DIR__ . '/../../includes/functions.php';
ob_start();

$events = [
    1 => [
        'title' => 'Hope For Every Child – Charity Evening',
        'category' => 'Education & Child Welfare',
        'status' => 'Upcoming',
        'date' => '2026-09-24',
        'time' => '06:00 PM – 09:00 PM',
        'location' => 'Rajkot, Gujarat',
        'venue' => 'Community Hall, Rajkot',
        'short_description' => 'A charity evening focused on supporting educational opportunities and essential resources for children.',
        'detailed_description' => 'This charity evening brings together community members and supporters to help create better educational opportunities for children. The event focuses on raising awareness, encouraging community participation, and supporting access to essential learning resources.',
        'target_participants' => 100,
        'registered_participants' => 64,
        'entry_type' => 'Free',
        'organizer' => 'Seva Roots Initiative'
    ],
    2 => [
        'title' => 'Skills For Tomorrow',
        'category' => 'Skill Development',
        'status' => 'Upcoming',
        'date' => '2026-10-05',
        'time' => '10:00 AM – 04:00 PM',
        'location' => 'Rajkot, Gujarat',
        'venue' => 'Community Learning Center, Rajkot',
        'short_description' => 'A skill development event designed to help young people build practical skills for future opportunities.',
        'detailed_description' => 'This event focuses on practical skill development and learning opportunities for young people. Participants will take part in educational activities designed to improve confidence, knowledge, and practical abilities.',
        'target_participants' => 150,
        'registered_participants' => 92,
        'entry_type' => 'Free',
        'organizer' => 'Seva Roots Initiative'
    ],
    3 => [
        'title' => 'Community Health Awareness Camp',
        'category' => 'Health & Wellness',
        'status' => 'Completed',
        'date' => '2026-08-15',
        'time' => '09:00 AM – 02:00 PM',
        'location' => 'Rajkot, Gujarat',
        'venue' => 'Community Health Center, Rajkot',
        'short_description' => 'A community awareness event focused on basic health education and wellness.',
        'detailed_description' => 'This community health awareness camp focused on spreading basic health knowledge and encouraging healthy practices among local community members.',
        'target_participants' => 100,
        'registered_participants' => 87,
        'entry_type' => 'Free',
        'organizer' => 'Seva Roots Initiative'
    ]
];

$registrations = [
    [
        'name' => 'Rajveer Meta',
        'status' => 'Registered',
        'time' => 'Today, 10:30 AM'
    ],
    [
        'name' => 'Amit Patel',
        'status' => 'Registered',
        'time' => '16 Sep 2026'
    ],
    [
        'name' => 'Ananya Patel',
        'status' => 'Registered',
        'time' => '15 Sep 2026'
    ]
];

$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$event = isset($events[$eventId]) ? $events[$eventId] : null;

?>
<div class="min-h-screen bg-ivory py-8 sm:py-10 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- TOP BACK LINK -->
        <div class="mb-6">
            <a href="<?= baseUrl('ngo/events/index.php') ?>" class="inline-flex items-center text-sm font-bold text-gold hover:text-forest transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Events
            </a>
        </div>

        <?php if (!$event): ?>
            <!-- INVALID EVENT -->
            <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-12 text-center">
                <h1 class="text-3xl font-serif font-bold text-forest mb-4">Event Not Found</h1>
                <p class="text-charcoal-light mb-8 text-lg">The requested event could not be found.</p>
                <a href="<?= baseUrl('ngo/events/index.php') ?>" class="inline-block bg-forest text-white hover:bg-forest-dark transition-colors rounded-xl font-bold py-3 px-8">
                    Back to Events
                </a>
            </div>
        <?php else: ?>

            <!-- EVENT HEADER -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <?php if ($event['status'] === 'Upcoming'): ?>
                                <span class="px-2 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">Upcoming</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-charcoal/10 text-charcoal text-xs font-bold uppercase tracking-wider rounded-md">Completed</span>
                            <?php endif; ?>
                            <span class="text-sm font-bold uppercase tracking-wider text-charcoal-light"><?= htmlspecialchars($event['category']) ?></span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-forest"><?= htmlspecialchars($event['title']) ?></h1>
                    </div>
                    
                    <?php if ($event['status'] === 'Upcoming'): ?>
                        <div class="shrink-0    ">
                            <a href="<?= baseUrl('ngo/events/edit.php?id=' . $eventId) ?>" class="inline-block bg-forest text-white border border-gold hover:bg-gold hover:text-forest transition-colors rounded-full font-bold py-2.5 px-6">
                                Edit Event
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MAIN TWO-COLUMN SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                
                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    
                    <!-- Event Details Card -->
                    <div class="bg-white rounded-3xl border border-gold/20 shadow-sm p-6 sm:p-8">
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-forest mb-6">Event Details</h2>
                        
                        <div class="mb-6">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-2">Short Description</h3>
                            <p class="text-charcoal"><?= htmlspecialchars($event['short_description']) ?></p>
                        </div>
                        
                        <div class="mb-8">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-2">Detailed Description</h3>
                            <p class="text-charcoal leading-relaxed"><?= nl2br(htmlspecialchars($event['detailed_description'])) ?></p>
                        </div>
                        
                        <div class="border-t border-charcoal/10 my-6 sm:my-8"></div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Event Date</h3>
                                <p class="text-charcoal font-medium"><?= date('d Sep Y', strtotime($event['date'])) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Event Time</h3>
                                <p class="text-charcoal font-medium"><?= htmlspecialchars($event['time']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Location</h3>
                                <p class="text-charcoal font-medium"><?= htmlspecialchars($event['location']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Venue</h3>
                                <p class="text-charcoal font-medium"><?= htmlspecialchars($event['venue']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Registrations Card -->
                    <div class="bg-white rounded-3xl border border-gold/20 shadow-sm overflow-hidden">
                        <div class="p-6 sm:p-8 flex justify-between items-center border-b border-charcoal/10">
                            <h2 class="text-xl sm:text-2xl font-serif font-bold text-forest">Recent Registrations</h2>
                            <a href="#" class="text-sm font-bold text-gold hover:text-forest transition-colors">View All</a>
                        </div>
                        
                        <div class="divide-y divide-charcoal/10">
                            <?php foreach ($registrations as $reg): ?>
                                <div class="p-6 sm:px-8 flex justify-between items-center hover:bg-ivory/50 transition-colors">
                                    <div>
                                        <h3 class="font-bold text-charcoal mb-1"><?= htmlspecialchars($reg['name']) ?></h3>
                                        <p class="text-sm text-charcoal-light">Registered • <?= htmlspecialchars($reg['time']) ?></p>
                                    </div>
                                    <div class="bg-forest/10 text-forest px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">
                                        <?= htmlspecialchars($reg['status']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                
                <!-- RIGHT COLUMN -->
                <div class="lg:col-span-1 space-y-6 sm:space-y-8">
                    
                    <!-- Event Summary Card -->
                    <div class="bg-white rounded-2xl border border-gold/20 shadow-sm p-6">
                        <h2 class="text-lg sm:text-xl font-serif font-bold text-forest mb-5">Event Summary</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Event Date</h3>
                                <p class="text-sm text-charcoal font-medium"><?= date('d M Y', strtotime($event['date'])) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Event Time</h3>
                                <p class="text-sm text-charcoal font-medium"><?= htmlspecialchars($event['time']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Location</h3>
                                <p class="text-sm text-charcoal font-medium"><?= htmlspecialchars($event['location']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Venue</h3>
                                <p class="text-sm text-charcoal font-medium"><?= htmlspecialchars($event['venue']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Status</h3>
                                <p class="text-sm font-medium <?= $event['status'] === 'Upcoming' ? 'text-forest' : 'text-charcoal' ?>"><?= htmlspecialchars($event['status']) ?></p>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">Category</h3>
                                <p class="text-sm text-charcoal font-medium"><?= htmlspecialchars($event['category']) ?></p>
                            </div>
                        </div>
                        
                        <div class="border-t border-charcoal/10 pt-5 mt-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-3">Participation</h3>
                            
                            <div class="mb-2">
                                <span class="text-xl font-bold text-forest"><?= (int)$event['registered_participants'] ?></span>
                                <span class="text-sm font-bold text-charcoal-light">/ <?= (int)$event['target_participants'] ?></span>
                            </div>
                            
                            <?php 
                            $percentage = ($event['target_participants'] > 0) ? floor(($event['registered_participants'] / $event['target_participants']) * 100) : 0; 
                            ?>
                            <div class="w-full bg-ivory rounded-full h-1.5 mb-2 overflow-hidden">
                                <div class="bg-forest h-1.5 rounded-full" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <div class="text-xs font-bold text-forest">
                                <?= $percentage ?>% Registered
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/main.php';
?>

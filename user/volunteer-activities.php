<?php
require_once __DIR__ . '/../includes/functions.php';
ob_start();
?>



<?php
    $volunteerActivities = [
        (object)[
            'id' => 1,
            'event_id' => null,
            'organization' => 'Helping Hands NGO',
            'title' => 'Community Food Distribution',
            'date' => '12 September 2026',
            'time' => '10:00 AM – 2:00 PM',
            'location' => 'Ahmedabad',
            'role' => 'Food Distribution Volunteer',
            'hours' => 4,
            'status' => 'Registered',
        ],
        (object)[
            'id' => 2,
            'event_id' => null,
            'organization' => 'Green Community Initiative',
            'title' => 'Community Clean-Up Drive',
            'date' => '20 September 2026',
            'time' => '7:00 AM – 11:00 AM',
            'location' => 'Ahmedabad',
            'role' => 'Community Volunteer',
            'hours' => 4,
            'status' => 'Registered',
        ],
        (object)[
            'id' => 3,
            'event_id' => null,
            'organization' => 'Hope Foundation',
            'title' => 'Community Clothing Drive',
            'date' => '18 August 2026',
            'time' => null,
            'location' => 'Ahmedabad',
            'role' => 'Distribution Volunteer',
            'hours' => 3,
            'status' => 'Completed',
        ],
        (object)[
            'id' => 4,
            'event_id' => null,
            'organization' => 'Smile Foundation',
            'title' => 'Education Support Workshop',
            'date' => '10 August 2026',
            'time' => null,
            'location' => 'Ahmedabad',
            'role' => 'Event Volunteer',
            'hours' => 4,
            'status' => 'Completed',
        ],
        (object)[
            'id' => 5,
            'event_id' => null,
            'organization' => 'Helping Hands NGO',
            'title' => 'Community Meal Service',
            'date' => '02 August 2026',
            'time' => null,
            'location' => 'Ahmedabad',
            'role' => 'Food Service Volunteer',
            'hours' => 2,
            'status' => 'Completed',
        ],
        (object)[
            'id' => 6,
            'event_id' => null,
            'organization' => 'Community Relief NGO',
            'title' => 'Donation Collection Drive',
            'date' => '25 July 2026',
            'time' => null,
            'location' => 'Ahmedabad',
            'role' => 'Collection Volunteer',
            'hours' => 3,
            'status' => 'Completed',
        ],
    ];

    $upcomingActivities = array_filter($volunteerActivities, function($a) { return $a->status === 'Registered'; });
    $completedActivities = array_filter($volunteerActivities, function($a) { return $a->status === 'Completed'; });
?>

<div class="bg-ivory/40 min-h-screen py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-10 text-center lg:text-left">
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-serif font-bold text-forest mb-2">Volunteer Activities</h1>
            <p class="text-charcoal-light">Make a difference with your time, skills, and participation.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-10">
            <!-- Activities Completed -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Activities Completed</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1">5</p>
                <p class="text-xs font-medium text-charcoal-light">Held across 4 organizations</p>
            </div>
            
            <!-- Hours Contributed -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Hours Contributed</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1">12h</p>
                <p class="text-xs font-medium text-charcoal-light">Time given to the community</p>
            </div>
            
            <!-- Upcoming Activities -->
            <div class="bg-white rounded-2xl p-4 md:p-8 shadow-sm border border-gold/20 flex flex-col justify-center transition-shadow hover:shadow-md">
                <p class="text-sm font-bold uppercase tracking-wide text-charcoal-light mb-1">Upcoming Activities</p>
                <p class="text-3xl md:text-4xl font-serif font-bold text-forest mb-1">2</p>
                <p class="text-xs font-medium text-charcoal-light">Ready to make an impact</p>
            </div>
        </div>

        <!-- Filters (Static Design) -->
        <div class="flex flex-wrap items-center gap-3 mb-8 pb-4 border-b border-charcoal/5">
            <a href="#" class="px-4 py-2 rounded-full text-sm font-bold bg-forest text-white shadow-sm">All Activities</a>
            <a href="#" class="px-4 py-2 rounded-full text-sm font-bold bg-white text-charcoal border border-charcoal/10 hover:bg-ivory transition-colors shadow-sm">Upcoming</a>
            <a href="#" class="px-4 py-2 rounded-full text-sm font-bold bg-white text-charcoal border border-charcoal/10 hover:bg-ivory transition-colors shadow-sm">Completed</a>
            
            <div class="ml-auto mt-2 sm:mt-0 w-full sm:w-auto">
                <select class="w-full sm:w-auto px-4 py-2 rounded-lg text-sm font-medium bg-white text-charcoal border border-charcoal/10 focus:outline-none focus:border-forest/50 shadow-sm">
                    <option>All Organizations</option>
                    <option>Helping Hands NGO</option>
                    <option>Green Community Initiative</option>
                    <option>Hope Foundation</option>
                    <option>Smile Foundation</option>
                    <option>Community Relief NGO</option>
                </select>
            </div>
        </div>

      <?php
    $renderCard = function ($activity) {
        $statusColor = 'bg-gray-50 text-gray-700 border-gray-200';

        if ($activity->status === 'Registered') {
            $statusColor = 'bg-amber-50 text-amber-700 border-amber-200';
        } elseif ($activity->status === 'Completed') {
            $statusColor = 'bg-green-50 text-forest border-green-200';
        }

        ob_start();
?>

<div class="bg-white rounded-2xl shadow-sm border border-gold/20 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow">
    <div class="p-6 md:p-8 flex-1">
        <div class="flex flex-col md:flex-row justify-between items-start mb-4">
            <div>
                <h3 class="font-bold text-charcoal text-lg mb-1">
                    <?= htmlspecialchars($activity->title) ?>
                </h3>

                <p class="text-sm font-medium text-forest">
                    <?= htmlspecialchars($activity->organization) ?>
                </p>
            </div>

            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border <?= $statusColor ?>">
                <?= htmlspecialchars($activity->status) ?>
            </span>
        </div>

        <div class="space-y-2 mb-6">
            <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                <span class="mr-2">📅</span>
                <span class="font-medium">
                    <?= htmlspecialchars($activity->date) ?>
                </span>
            </div>

            <?php if ($activity->time): ?>
                <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                    <span class="mr-2">🕐</span>
                    <span class="font-medium">
                        <?= htmlspecialchars($activity->time) ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row items-center text-sm text-charcoal-light">
                <span class="mr-2">📍</span>
                <span class="font-medium">
                    <?= htmlspecialchars($activity->location) ?>
                </span>
            </div>
        </div>

        <div class="border-t border-charcoal/5 pt-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wide text-charcoal-light">
                    Role
                </span>

                <span class="text-sm font-bold text-charcoal">
                    <?= htmlspecialchars($activity->role) ?>
                </span>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wide text-charcoal-light">
                    Hours
                </span>

                <span class="text-sm font-bold text-charcoal">
                    <?= htmlspecialchars($activity->hours) ?> hours
                </span>
            </div>
        </div>
    </div>

    <div class="p-4 md:p-8 bg-ivory/20 border-t border-charcoal/5 mt-auto">
        <a
            href="<?= baseUrl('?page=events') ?>" 
            class="block text-center w-full px-4 py-2 border border-charcoal/10 text-sm font-bold rounded-lg text-forest bg-white hover:bg-ivory-dark transition-colors shadow-sm"
        >
            View Activity
        </a>
    </div>
</div>

<?php
        return ob_get_clean();
    };
?>

        <!-- Upcoming Activities Section -->
        <div class="mb-12">
            <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-6">Upcoming Activities</h2>
            <?php if (count($upcomingActivities) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($upcomingActivities as $activity): ?>
                        <?= $renderCard($activity) ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Prepared Empty State (Hidden via data) -->
                <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-charcoal/5">
                    <h3 class="text-xl font-bold text-charcoal mb-2">No Upcoming Activities</h3>
                    <p class="text-charcoal-light mb-6">Find an opportunity to contribute your time and make a difference.</p>
                    <a href="<?= baseUrl('?page=events') ?>"  class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-forest hover:bg-forest-dark transition-colors">Find Volunteer Opportunities</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Activities Section -->
        <div class="mb-12">
            <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-6">Completed Activities</h2>
            <?php if (count($completedActivities) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($completedActivities as $activity): ?>
                        <?= $renderCard($activity) ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-charcoal/5">
                    <h3 class="text-xl font-bold text-charcoal mb-2">No Volunteer Activities Yet</h3>
                    <p class="text-charcoal-light mb-6">Find an opportunity to contribute your time and make a difference.</p>
                    <a href="<?= baseUrl('?page=events') ?>"  class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-forest hover:bg-forest-dark transition-colors">Find Volunteer Opportunities</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Impact Section -->
        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gold/20 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-forest/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">Your Volunteer Impact</h2>
                    <p class="text-sm md:text-base text-charcoal-light max-w-2xl">Every hour you contribute helps strengthen the communities and organizations supported through KarmaSetu.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-6 sm:gap-8 justify-center text-center md:text-right">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Completed</p>
                        <p class="text-2xl md:text-4xl font-serif font-bold text-forest">5 Activities</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">Contributed</p>
                        <p class="text-2xl md:text-4xl font-serif font-bold text-forest">12 Hours</p>
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

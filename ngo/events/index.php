<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$ngo_id = 0;
$upcomingEvents = [];
$pastEvents = [];

if ($user_id > 0) {

    // Get NGO ID
    $ngoQuery = "SELECT id FROM ngos WHERE user_id = $user_id";
    $ngoResult = mysqli_query($con, $ngoQuery);

    if ($ngoResult && mysqli_num_rows($ngoResult) > 0) {
        $ngoData = mysqli_fetch_assoc($ngoResult);
        $ngo_id = (int) $ngoData['id'];
    }

    if ($ngo_id > 0) {

        // Get upcoming events
        $upcomingQuery = "
            SELECT *
            FROM events
            WHERE ngo_id = $ngo_id
            AND status = 'upcoming'
            AND event_date >= CURDATE()
            ORDER BY event_date ASC
        ";

        $upcomingResult = mysqli_query($con, $upcomingQuery);

        if ($upcomingResult) {
            while ($event = mysqli_fetch_assoc($upcomingResult)) {
                $upcomingEvents[] = $event;
            }
        }

        // Get past events
        $pastQuery = "
            SELECT *
            FROM events
            WHERE ngo_id = $ngo_id
            AND (
                status = 'completed'
                OR event_date < CURDATE()
            )
            ORDER BY event_date DESC
        ";

        $pastResult = mysqli_query($con, $pastQuery);

        if ($pastResult) {
            while ($event = mysqli_fetch_assoc($pastResult)) {
                $pastEvents[] = $event;
            }
        }
    }
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto w-full min-w-0">

        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">

            <div class="min-w-0">
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">
                    Events
                </h1>

                <p class="text-charcoal-light">
                    Manage your organization's upcoming and past events.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?= baseUrl('ngo/events/create.php') ?>"
                   class="inline-block bg-forest text-white font-bold py-2.5 px-6 rounded-full hover:bg-forest-dark transition-colors shadow-sm">
                    + Create New Event
                </a>
            </div>

        </div>


        <!-- UPCOMING EVENTS -->
        <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6">
            Upcoming Events
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full min-w-0 mb-12">

            <?php if (!empty($upcomingEvents)) { ?>

                <?php foreach ($upcomingEvents as $event) { ?>

                    <!-- UPCOMING EVENT CARD -->
                    <div class="w-full min-w-0 overflow-hidden bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20 flex flex-col justify-between">

                        <div class="w-full min-w-0">

                            <div class="flex justify-between items-start mb-3">

                                <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">
                                    Upcoming
                                </span>

                            </div>

                            <!-- EVENT TITLE -->
                            <h2 class="w-full min-w-0 text-xl font-serif font-bold text-forest mb-2 break-words">
                                <?= $event['title'] ?>
                            </h2>

                            <!-- EVENT INFORMATION -->
                            <div class="space-y-2 mb-4 w-full min-w-0">

                                <!-- DATE -->
                                <div class="flex items-start min-w-0 text-sm text-charcoal">

                                    <svg class="w-4 h-4 mr-2 mt-0.5 shrink-0 text-gold"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>

                                    </svg>

                                    <span class="min-w-0 break-words">
                                        <?= $event['event_date'] ?>
                                    </span>

                                </div>

                                <!-- LOCATION -->
                                <div class="flex items-start min-w-0 text-sm text-charcoal">

                                    <svg class="w-4 h-4 mr-2 mt-0.5 shrink-0 text-gold"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                    </svg>

                                    <span class="min-w-0 break-words">
                                        <?= $event['location'] ?>
                                    </span>

                                </div>

                            </div>

                            <!-- DESCRIPTION -->
                            <p class="w-full min-w-0 text-sm text-charcoal-light mb-6 line-clamp-2 break-words">
                                <?= $event['short_description'] ?>
                            </p>

                        </div>


                        <!-- ACTION BUTTONS -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 pt-4 border-t border-charcoal/10 w-full min-w-0">

                            <a href="<?= baseUrl('ngo/events/details.php?id=' . $event['id']) ?>"
                               class="w-full sm:flex-1 min-w-0 text-center bg-ivory text-forest font-bold py-2 px-3 rounded-xl hover:bg-forest/5 transition-colors border border-charcoal/10 text-sm">
                                View Details
                            </a>

                            <a href="<?= baseUrl('ngo/events/edit.php?id=' . $event['id']) ?>"
                               class="w-full sm:flex-1 min-w-0 text-center bg-white text-gold font-bold py-2 px-3 rounded-xl hover:bg-gold/5 transition-colors border border-gold/30 text-sm">
                                Edit
                            </a>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <!-- NO UPCOMING EVENTS -->
                <div class="col-span-full bg-white rounded-3xl p-8 text-center border border-gold/20">

                    <h3 class="text-lg font-bold text-forest mb-2">
                        No Upcoming Events
                    </h3>

                    <p class="text-charcoal-light text-sm">
                        You haven't created any upcoming events yet.
                    </p>

                </div>

            <?php } ?>

        </div>


        <!-- PAST EVENTS -->
        <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6 opacity-75">
            Past Events
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full min-w-0 opacity-75">

            <?php if (!empty($pastEvents)) { ?>

                <?php foreach ($pastEvents as $event) { ?>

                    <!-- PAST EVENT CARD -->
                    <div class="w-full min-w-0 overflow-hidden bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-charcoal/10 flex flex-col justify-between">

                        <div class="w-full min-w-0">

                            <div class="flex justify-between items-start mb-3">

                                <span class="inline-block px-2.5 py-1 bg-charcoal/10 text-charcoal text-xs font-bold uppercase tracking-wider rounded-md">
                                    <?= $event['status'] == 'completed' ? 'Completed' : 'Past Event' ?>
                                </span>

                            </div>

                            <!-- EVENT TITLE -->
                            <h2 class="w-full min-w-0 text-xl font-serif font-bold text-charcoal mb-2 break-words">
                                <?= $event['title'] ?>
                            </h2>

                            <!-- EVENT INFORMATION -->
                            <div class="space-y-2 mb-4 w-full min-w-0">

                                <!-- DATE -->
                                <div class="flex items-start min-w-0 text-sm text-charcoal">

                                    <svg class="w-4 h-4 mr-2 mt-0.5 shrink-0 text-charcoal-light"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>

                                    </svg>

                                    <span class="min-w-0 break-words">
                                        <?= $event['event_date'] ?>
                                    </span>

                                </div>

                                <!-- LOCATION -->
                                <div class="flex items-start min-w-0 text-sm text-charcoal">

                                    <svg class="w-4 h-4 mr-2 mt-0.5 shrink-0 text-charcoal-light"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 00-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>

                                    </svg>

                                    <span class="min-w-0 break-words">
                                        <?= $event['location'] ?>
                                    </span>

                                </div>

                            </div>

                            <!-- DESCRIPTION -->
                            <p class="w-full min-w-0 text-sm text-charcoal-light mb-6 line-clamp-2 break-words">
                                <?= $event['short_description'] ?>
                            </p>

                        </div>


                        <!-- ACTION BUTTON -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 pt-4 border-t border-charcoal/10 w-full min-w-0">

                            <a href="<?= baseUrl('ngo/events/details.php?id=' . $event['id']) ?>"
                               class="w-full sm:flex-1 min-w-0 text-center bg-ivory text-charcoal font-bold py-2 px-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/10 text-sm">
                                View Details
                            </a>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <!-- NO PAST EVENTS -->
                <div class="col-span-full bg-white rounded-3xl p-8 text-center border border-charcoal/10">

                    <h3 class="text-lg font-bold text-charcoal mb-2">
                        No Past Events
                    </h3>

                    <p class="text-charcoal-light text-sm">
                        Your completed events will appear here.
                    </p>

                </div>

            <?php } ?>

        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/ngo.php';
?>

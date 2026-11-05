<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

$event = null;
$errorMsg = '';

$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Check login
if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    // Get NGO ID
    $ngoQuery = "SELECT id FROM ngos WHERE user_id = $user_id";
    $ngoResult = mysqli_query($con, $ngoQuery);

    if ($ngoResult && mysqli_num_rows($ngoResult) > 0) {

        $ngoData = mysqli_fetch_assoc($ngoResult);
        $ngo_id = $ngoData['id'];

        // Get Event Details
        if ($eventId > 0) {

            $eventQuery = "SELECT * FROM events
                           WHERE id = $eventId
                           AND ngo_id = $ngo_id";

            $eventResult = mysqli_query($con, $eventQuery);

            if ($eventResult && mysqli_num_rows($eventResult) > 0) {
                $event = mysqli_fetch_assoc($eventResult);
            }
        }

    } else {
        $errorMsg = "NGO details not found!";
    }

} else {
    $errorMsg = "Please login to view event details!";
}


// Format Event Time
$eventTime = 'Not specified';

if ($event && !empty($event['start_time'])) {

    $eventTime = date('h:i A', strtotime($event['start_time']));

    if (!empty($event['end_time'])) {
        $eventTime .= ' - ' . date('h:i A', strtotime($event['end_time']));
    }
}


// Format Entry Fee
$entryFee = 'Free';

if ($event && $event['entry_fee'] > 0) {
    $entryFee = '₹' . number_format($event['entry_fee'], 2);
}


// Event Status
$status = $event ? $event['status'] : '';

?>

<div class="min-h-screen bg-ivory py-8 sm:py-10 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

        <!-- TOP BACK LINK -->
        <div class="mb-6">
            <a href="<?= baseUrl('ngo/events/index.php') ?>"
               class="inline-flex items-center text-sm font-bold text-gold hover:text-forest transition-colors">

                <svg class="w-4 h-4 mr-1"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>

                </svg>

                Back to Events
            </a>
        </div>


        <?php if (!$event): ?>

            <!-- EVENT NOT FOUND -->
            <div class="bg-white rounded-3xl shadow-sm border border-gold/20 p-12 text-center">

                <h1 class="text-3xl font-serif font-bold text-forest mb-4">
                    Event Not Found
                </h1>

                <p class="text-charcoal-light mb-8 text-lg">
                    <?= $errorMsg != '' ? $errorMsg : 'The requested event could not be found.' ?>
                </p>

                <a href="<?= baseUrl('ngo/events/index.php') ?>"
                   class="inline-block bg-forest text-white hover:bg-forest-dark transition-colors rounded-xl font-bold py-3 px-8">

                    Back to Events
                </a>

            </div>

        <?php else: ?>

            <!-- EVENT HEADER -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-3 mb-3">

                            <?php if ($status === 'upcoming'): ?>

                                <span class="px-2 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md">
                                    Upcoming
                                </span>

                            <?php elseif ($status === 'completed'): ?>

                                <span class="px-2 py-1 bg-charcoal/10 text-charcoal text-xs font-bold uppercase tracking-wider rounded-md">
                                    Completed
                                </span>

                            <?php else: ?>

                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider rounded-md">
                                    Cancelled
                                </span>

                            <?php endif; ?>

                            <span class="text-sm font-bold uppercase tracking-wider text-charcoal-light">
                                <?= $event['category'] ?>
                            </span>

                        </div>

                        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-forest break-words [overflow-wrap:anywhere]">
                            <?= $event['title'] ?>
                        </h1>

                    </div>

                    <?php if ($status === 'upcoming'): ?>

                        <div class="shrink-0">
                            <a href="<?= baseUrl('ngo/events/edit.php?id=' . $eventId) ?>"
                               class="inline-block bg-forest text-white border border-gold hover:bg-gold hover:text-forest transition-colors rounded-full font-bold py-2.5 px-6">

                                Edit Event
                            </a>
                        </div>

                    <?php endif; ?>

                </div>
            </div>


            <!-- MAIN TWO-COLUMN SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">

                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 min-w-0 space-y-6 sm:space-y-8">

                    <!-- EVENT DETAILS CARD -->
                    <div class="min-w-0 bg-white rounded-3xl border border-gold/20 shadow-sm p-6 sm:p-8">

                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-forest mb-6">
                            Event Details
                        </h2>

                        <!-- SHORT DESCRIPTION -->
                        <div class="mb-6">

                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-2">
                                Short Description
                            </h3>

                            <p class="text-charcoal break-words [overflow-wrap:anywhere]">
                                <?= $event['short_description'] ?>
                            </p>

                        </div>

                        <!-- DETAILED DESCRIPTION -->
                        <div class="mb-8">

                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-2">
                                Detailed Description
                            </h3>

                            <p class="text-charcoal leading-relaxed break-words [overflow-wrap:anywhere]">
                                <?php if (!empty($event['detailed_description'])): ?>

                                    <?= nl2br($event['detailed_description']) ?>

                                <?php else: ?>

                                    No detailed description available.

                                <?php endif; ?>
                            </p>

                        </div>

                        <div class="border-t border-charcoal/10 my-6 sm:my-8"></div>

                        <!-- EVENT INFORMATION -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <!-- EVENT DATE -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Event Date
                                </h3>

                                <p class="text-charcoal font-medium">
                                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                                </p>
                            </div>

                            <!-- EVENT TIME -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Event Time
                                </h3>

                                <p class="text-charcoal font-medium">
                                    <?= $eventTime ?>
                                </p>
                            </div>

                            <!-- LOCATION -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Location
                                </h3>

                                <p class="text-charcoal font-medium break-words [overflow-wrap:anywhere]">
                                    <?= $event['location'] ?>
                                </p>
                            </div>

                            <!-- ENTRY FEE -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Entry Fee
                                </h3>

                                <p class="text-charcoal font-medium">
                                    <?= $entryFee ?>
                                </p>
                            </div>

                        </div>

                    </div>


                    <!-- REGISTRATION INFORMATION -->
                    <div class="bg-white rounded-3xl border border-gold/20 shadow-sm p-6 sm:p-8">

                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-forest mb-4">
                            Registration Information
                        </h2>

                        <p class="text-charcoal-light leading-relaxed">
                            Registration details will be displayed here once the event registration functionality is connected to the database.
                        </p>

                    </div>

                </div>


                <!-- RIGHT COLUMN -->
                <div class="lg:col-span-1 min-w-0 space-y-6 sm:space-y-8">

                    <!-- EVENT SUMMARY CARD -->
                    <div class="min-w-0 bg-white rounded-2xl border border-gold/20 shadow-sm p-6">

                        <h2 class="text-lg sm:text-xl font-serif font-bold text-forest mb-5">
                            Event Summary
                        </h2>

                        <div class="space-y-4 mb-6">

                            <!-- EVENT DATE -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Event Date
                                </h3>

                                <p class="text-sm text-charcoal font-medium">
                                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                                </p>
                            </div>

                            <!-- EVENT TIME -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Event Time
                                </h3>

                                <p class="text-sm text-charcoal font-medium">
                                    <?= $eventTime ?>
                                </p>
                            </div>

                            <!-- LOCATION -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Location
                                </h3>

                                <p class="text-sm text-charcoal font-medium break-words [overflow-wrap:anywhere]">
                                    <?= $event['location'] ?>
                                </p>
                            </div>

                            <!-- STATUS -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Status
                                </h3>

                                <p class="text-sm font-medium <?= $status === 'upcoming' ? 'text-forest' : ($status === 'completed' ? 'text-charcoal' : 'text-red-600') ?>">
                                    <?= ucfirst($status) ?>
                                </p>
                            </div>

                            <!-- CATEGORY -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Category
                                </h3>

                                <p class="text-sm text-charcoal font-medium">
                                    <?= $event['category'] ?>
                                </p>
                            </div>

                            <!-- ENTRY FEE -->
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-1">
                                    Entry Fee
                                </h3>

                                <p class="text-sm text-charcoal font-medium">
                                    <?= $entryFee ?>
                                </p>
                            </div>

                        </div>


                        <!-- MAXIMUM PARTICIPANTS -->
                        <div class="border-t border-charcoal/10 pt-5 mt-5">

                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal-light mb-3">
                                Maximum Participants
                            </h3>

                            <?php if (!empty($event['max_participants'])): ?>

                                <p class="text-2xl font-bold text-forest">
                                    <?= (int) $event['max_participants'] ?>
                                </p>

                                <p class="text-xs text-charcoal-light mt-1">
                                    Maximum registration capacity
                                </p>

                            <?php else: ?>

                                <p class="text-sm font-bold text-forest">
                                    Unlimited
                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/ngo.php';
?>
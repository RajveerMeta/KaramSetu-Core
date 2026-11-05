
<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$successMsg = '';
$errorMsg = '';

$event = null;

$title = '';
$category = '';
$location = '';
$event_date = '';
$start_time = '';
$end_time = '';
$max_participants = '';
$short_desc = '';
$long_desc = '';
$entry_fee = '0';

$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    // Get NGO ID
    $ngoQuery = "SELECT id FROM ngos WHERE user_id = $user_id";
    $ngoResult = mysqli_query($con, $ngoQuery);

    if ($ngoResult && mysqli_num_rows($ngoResult) > 0) {

        $ngoData = mysqli_fetch_assoc($ngoResult);
        $ngo_id = $ngoData['id'];

        // Update Event
        if (isset($_POST['update_event'])) {

            $eventId = (int) $_POST['event_id'];

            $title = $_POST['title'];
            $category = $_POST['category'];
            $location = $_POST['location'];
            $event_date = $_POST['event_date'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $max_participants = $_POST['max_participants'];
            $short_desc = $_POST['short_desc'];
            $long_desc = $_POST['long_desc'];
            $entry_fee = $_POST['entry_fee'];

            // Handle empty values
            if ($start_time == '') {
                $start_time = NULL;
            }

            if ($end_time == '') {
                $end_time = NULL;
            }

            if ($max_participants == '') {
                $max_participants = NULL;
            }

            if ($long_desc == '') {
                $long_desc = NULL;
            }

            if ($entry_fee == '') {
                $entry_fee = 0;
            }

            // Check Event Ownership
            $checkQuery = "SELECT id FROM events
                           WHERE id = $eventId
                           AND ngo_id = $ngo_id";

            $checkResult = mysqli_query($con, $checkQuery);

            if ($checkResult && mysqli_num_rows($checkResult) > 0) {

                // Update Query
                $updateQuery = "UPDATE events SET
                                title = '$title',
                                category = '$category',
                                location = '$location',
                                event_date = '$event_date',
                                start_time = " . ($start_time === NULL ? "NULL" : "'$start_time'") . ",
                                end_time = " . ($end_time === NULL ? "NULL" : "'$end_time'") . ",
                                entry_fee = '$entry_fee',
                                max_participants = " . ($max_participants === NULL ? "NULL" : "'$max_participants'") . ",
                                short_description = '$short_desc',
                                detailed_description = " . ($long_desc === NULL ? "NULL" : "'$long_desc'") . "
                                WHERE id = $eventId
                                AND ngo_id = $ngo_id";

                if (mysqli_query($con, $updateQuery)) {

    $_SESSION['successMsg'] = "Event updated successfully!";

    header("Location: " . baseUrl('ngo/events/index.php'));
    exit();

} else {

    $errorMsg = "Event update failed!";

}

            } else {

                $errorMsg = "Event not found!";

            }
        }

        // Get Event Details
        if ($eventId > 0) {

            $eventQuery = "SELECT * FROM events
                           WHERE id = $eventId
                           AND ngo_id = $ngo_id";

            $eventResult = mysqli_query($con, $eventQuery);

            if ($eventResult && mysqli_num_rows($eventResult) > 0) {

                $event = mysqli_fetch_assoc($eventResult);

                // Set Form Values
                $title = $event['title'];
                $category = $event['category'];
                $location = $event['location'];
                $event_date = $event['event_date'];
                $start_time = $event['start_time'] ?? '';
                $end_time = $event['end_time'] ?? '';
                $max_participants = $event['max_participants'] ?? '';
                $short_desc = $event['short_description'];
                $long_desc = $event['detailed_description'] ?? '';
                $entry_fee = $event['entry_fee'];

            } else {

                $errorMsg = "Event not found!";

            }

        } else {

            $errorMsg = "Invalid event ID!";

        }

    } else {

        $errorMsg = "NGO details not found!";

    }

} else {

    $errorMsg = "Please login to edit your event!";

}
$successMsg = '';

if (isset($_SESSION['successMsg'])) {
    $successMsg = $_SESSION['successMsg'];
    unset($_SESSION['successMsg']);
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <!-- BACK LINK -->
        <div class="mb-8">
            <a href="<?= baseUrl('ngo/events/index.php') ?>"
               class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">

                <svg class="w-4 h-4"
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


        <?php if ($event): ?>

            <!-- EDIT EVENT CARD -->
            <div class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gold/20">

                <h1 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">
                    Edit Event
                </h1>

                <p class="text-charcoal-light mb-8">
                    Update the details of your scheduled event.
                </p>


                <!-- SUCCESS MESSAGE -->
                <?php if ($successMsg != ''): ?>

                    <div class="mb-6 p-4 rounded-xl bg-forest/10 border border-forest text-forest font-bold text-sm">
                        <?= $successMsg ?>
                   </div>

                <?php endif; ?>


                <!-- ERROR MESSAGE -->
                <?php if ($errorMsg != '') { ?>

                    <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-700 font-bold text-sm mb-6">
                        <?= $errorMsg ?>
                    </div>

                <?php } ?>


                <form id="eventForm"
                      method="POST"
                      class="space-y-6 validation-form"
                      novalidate>

                    <input type="hidden" name="event_id" value="<?= $eventId ?>">

                    <div class="space-y-4">

                        <!-- EVENT TITLE -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Event Title <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?= $title ?>"
                                required
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="e.g. Annual Charity Gala"
                                data-validate="required"
                            >
                        </div>


                        <!-- CATEGORY -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Category <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="category"
                                name="category"
                                required
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white appearance-none"
                                data-validate="required"
                            >

                                <option value="">Select a category</option>

                                <option value="fundraiser" <?= $category == 'fundraiser' ? 'selected' : '' ?>>
                                    Fundraiser
                                </option>

                                <option value="workshop" <?= $category == 'workshop' ? 'selected' : '' ?>>
                                    Workshop / Skill Building
                                </option>

                                <option value="health" <?= $category == 'health' ? 'selected' : '' ?>>
                                    Health Camp
                                </option>

                                <option value="community" <?= $category == 'community' ? 'selected' : '' ?>>
                                    Community Gathering
                                </option>

                            </select>
                        </div>


                        <!-- LOCATION -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Location <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="<?= $location ?>"
                                required
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="e.g. Rajkot, Gujarat"
                                data-validate="required"
                            >
                        </div>


                        <!-- DATE AND TIME -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <!-- EVENT DATE -->
                            <div>
                                <label class="block text-sm font-bold text-charcoal mb-1">
                                    Event Date <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="event_date"
                                    name="event_date"
                                    value="<?= $event_date ?>"
                                    required
                                    class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                    data-validate="required"
                                >
                            </div>


                            <!-- START TIME -->
                            <div>
                                <label class="block text-sm font-bold text-charcoal mb-1">
                                    Start Time
                                </label>

                                <input
                                    type="time"
                                    id="start_time"
                                    name="start_time"
                                    value="<?= $start_time ?>"
                                    class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                >
                            </div>


                            <!-- END TIME -->
                            <div>
                                <label class="block text-sm font-bold text-charcoal mb-1">
                                    End Time
                                </label>

                                <input
                                    type="time"
                                    id="end_time"
                                    name="end_time"
                                    value="<?= $end_time ?>"
                                    class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                >
                            </div>

                        </div>


                        <!-- MAXIMUM PARTICIPANTS -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Maximum Participants
                            </label>

                            <input
                                type="number"
                                id="max_participants"
                                name="max_participants"
                                value="<?= $max_participants ?>"
                                min="1"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="Leave empty for unlimited"
                                data-validate="numeric"
                            >
                        </div>


                        <!-- SHORT DESCRIPTION -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Short Description <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                id="short_desc"
                                name="short_desc"
                                required
                                rows="2"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="A brief summary of this event..."
                                data-validate="required"
                            ><?= $short_desc ?></textarea>
                        </div>


                        <!-- DETAILED DESCRIPTION -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Detailed Description
                            </label>

                            <textarea
                                id="long_desc"
                                name="long_desc"
                                rows="5"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="Provide full details about what attendees can expect..."
                            ><?= $long_desc ?></textarea>
                        </div>


                        <!-- ENTRY FEE -->
                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Entry Fee (₹)
                            </label>

                            <input
                                type="number"
                                id="entry_fee"
                                name="entry_fee"
                                min="0"
                                step="0.01"
                                value="<?= $entry_fee ?>"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                placeholder="Enter 0 for a free event"
                                data-validate="numeric"
                            >

                            <p class="text-xs text-charcoal-light mt-1">
                                Enter 0 if the event is free.
                            </p>
                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="flex flex-col md:flex-row flex-wrap gap-4 pt-4 border-t border-charcoal/10 mt-8">

                        <a href="<?= baseUrl('ngo/events/index.php') ?>"
                           class="flex-1 text-center bg-ivory text-charcoal font-bold py-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/20">

                            Cancel
                        </a>

                        <button
                            type="submit"
                            name="update_event"
                            value="1"
                            class="flex-1 text-center bg-forest text-white font-bold py-3 rounded-xl hover:bg-forest-dark transition-colors shadow-md"
                        >
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        <?php else: ?>

            <!-- EVENT NOT FOUND -->
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gold/20 text-center">

                <h1 class="text-2xl md:text-3xl font-serif font-bold text-forest mb-4">
                    Event Not Found
                </h1>

                <p class="text-charcoal-light mb-6">
                    <?= $errorMsg != '' ? $errorMsg : 'The requested event could not be found.' ?>
                </p>

                <a href="<?= baseUrl('ngo/events/index.php') ?>"
                   class="inline-block bg-forest text-white font-bold py-3 px-8 rounded-xl hover:bg-forest-dark transition-colors">

                    Back to Events
                </a>

            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/ngo.php';
?>

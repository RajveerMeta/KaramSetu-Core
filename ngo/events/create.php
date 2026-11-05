
<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$successMsg = '';
$errorMsg = '';

// Form variables
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

if (isset($_POST["create_event"])) {

    // Get form values
    $title = $_POST["title"];
    $category = $_POST["category"];
    $location = $_POST["location"];
    $event_date = $_POST["event_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $max_participants = $_POST["max_participants"];
    $short_desc = $_POST["short_desc"];
    $long_desc = $_POST["long_desc"];
    $entry_fee = $_POST["entry_fee"];

    if (isset($_SESSION['user_id'])) {

        $user_id = $_SESSION['user_id'];

        // Get NGO ID
        $ngoQuery = "SELECT id FROM ngos WHERE user_id = $user_id";
        $ngoResult = mysqli_query($con, $ngoQuery);

        if ($ngoResult && mysqli_num_rows($ngoResult) > 0) {

            $ngoData = mysqli_fetch_assoc($ngoResult);
            $ngo_id = $ngoData['id'];

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

            // Insert event
            $sql = "INSERT INTO events
                    (
                        ngo_id,
                        title,
                        category,
                        location,
                        event_date,
                        start_time,
                        end_time,
                        entry_fee,
                        max_participants,
                        status,
                        short_description,
                        detailed_description
                    )
                    VALUES
                    (
                        '$ngo_id',
                        '$title',
                        '$category',
                        '$location',
                        '$event_date',
                        " . ($start_time === NULL ? "NULL" : "'$start_time'") . ",
                        " . ($end_time === NULL ? "NULL" : "'$end_time'") . ",
                        '$entry_fee',
                        " . ($max_participants === NULL ? "NULL" : "'$max_participants'") . ",
                        'upcoming',
                        '$short_desc',
                        " . ($long_desc === NULL ? "NULL" : "'$long_desc'") . "
                    )";

            if (mysqli_query($con, $sql)) {
                $successMsg = "Event created successfully!";
            } else {
                $errorMsg = "Event creation failed!";
            }

        } else {
            $errorMsg = "NGO details not found!";
        }

    } else {
        $errorMsg = "Please login to create an event!";
    }
}

ob_start();
?>


<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <a href="<?= baseUrl('ngo/events/index.php') ?>"
               class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>

                Back to Events
            </a>
        </div>

        <div class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gold/20">

            <h1 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">
                Create New Event
            </h1>

            <p class="text-charcoal-light mb-8">
                Plan and publish a new event for your organization.
            </p>

            <!-- Success Message -->
            <?php if ($successMsg != '') { ?>
                <div class="p-4 rounded-xl bg-forest/10 border border-forest text-forest font-bold text-sm mb-6">
                    <?= $successMsg ?>
                </div>
            <?php } ?>

            <!-- Error Message -->
            <?php if ($errorMsg != '') { ?>
                <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-700 font-bold text-sm mb-6">
                    <?= $errorMsg ?>
                </div>
            <?php } ?>

            <form id="eventForm" method="POST" class="space-y-6 validation-form" novalidate>

                <div class="space-y-4">

                    <!-- Event Title -->
                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Event Title <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?= 	$title ?>"
                            required
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                            placeholder="e.g. Annual Charity Gala"
                            data-validate="required"
                        >
                    </div>

                    <!-- Category -->
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

                            <option value="fundraiser" <?= 	$category == 'fundraiser' ? 'selected' : '' ?>>
                                Fundraiser
                            </option>

                            <option value="workshop" <?= 	$category == 'workshop' ? 'selected' : '' ?>>
                                Workshop / Skill Building
                            </option>

                            <option value="health" <?= 	$category == 'health' ? 'selected' : '' ?>>
                                Health Camp
                            </option>

                            <option value="community" <?= 	$category == 'community' ? 'selected' : '' ?>>
                                Community Gathering
                            </option>
                        </select>
                    </div>

                    <!-- Location -->
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

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

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

                    <!-- Maximum Participants -->
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

                    <!-- Short Description -->
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

                    <!-- Detailed Description -->
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

                    <!-- Entry Fee -->
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

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row flex-wrap gap-4 pt-4 border-t border-charcoal/10 mt-8">

                    <a href="<?= baseUrl('ngo/events/index.php') ?>"
                       class="flex-1 text-center bg-ivory text-charcoal font-bold py-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/20">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="create_event"
                        value="1"
                        class="flex-1 text-center bg-forest text-white font-bold py-3 rounded-xl hover:bg-forest-dark transition-colors shadow-md"
                    >
                        Create Event
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/ngo.php';
?>

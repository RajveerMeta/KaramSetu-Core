
<?php
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../Database/db_config.php';

/* GET CAUSE ID */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: " . baseUrl('ngo/causes.php'));
    exit;
}

/* CHECK LOGIN */

if (!isset($_SESSION['user_id'])) {
    header("Location: " . baseUrl('auth/login.php'));
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* GET NGO ID */

$ngoQuery = "SELECT id FROM ngos WHERE user_id = $user_id";
$ngoResult = mysqli_query($con, $ngoQuery);
$ngo = mysqli_fetch_assoc($ngoResult);

if (!$ngo) {
    header("Location: " . baseUrl('ngo/causes.php'));
    exit;
}

$ngo_id = $ngo['id'];

/* GET CAUSE DETAILS */

$query = "SELECT * FROM causes
          WHERE id = $id AND ngo_id = $ngo_id";

$result = mysqli_query($con, $query);
$users = mysqli_fetch_assoc($result);

if (!$users) {
    header("Location: " . baseUrl('ngo/causes.php'));
    exit;
}

/* MESSAGES */

$successMsg = '';
$errorMsg = '';

/* UPDATE CAUSE */

if (isset($_POST['update_cause'])) {

    $title =  $_POST['title'];
    $category = $_POST['category'];
    $target = $_POST['target'];
    $short_desc = $_POST['short_desc'];
    $long_desc = $_POST['long_desc'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (
        empty($title) ||
        empty($category) ||
        empty($target) ||
        empty($short_desc)
    ) {
        $errorMsg = "Please fill all required fields.";
    } elseif ($target <= 0) {
        $errorMsg = "Target amount must be greater than zero.";
    } elseif (
        !empty($start_date) &&
        !empty($end_date) &&
        $end_date < $start_date
    ) {
        $errorMsg = "End date cannot be before start date.";
    } else {

        if (empty($start_date)) {
            $start_date = "NULL";
        } else {
            $start_date = "'" . mysqli_real_escape_string($con, $start_date) . "'";
        }

        if (empty($end_date)) {
            $end_date = "NULL";
        } else {
            $end_date = "'" . mysqli_real_escape_string($con, $end_date) . "'";
        }

        $updateQuery = "UPDATE causes SET
                        title = '$title',
                        category = '$category',
                        target_amount = $target,
                        short_description = '$short_desc',
                        detailed_description = '$long_desc',
                        start_date = $start_date,
                        end_date = $end_date
                        WHERE id = $id
                        AND ngo_id = $ngo_id";

        if (mysqli_query($con, $updateQuery)) {

            $successMsg = "Cause updated successfully!";

            /* REFRESH CAUSE DETAILS */

            $query = "SELECT * FROM causes
                      WHERE id = $id AND ngo_id = $ngo_id";

            $result = mysqli_query($con, $query);
            $users = mysqli_fetch_assoc($result);

        } else {
            $errorMsg = "Unable to update cause. Please try again.";
        }
    }
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <!-- BACK LINK -->

        <div class="mb-8">
            <a href="<?= baseUrl('ngo/causes.php') ?>"
               class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">

                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>

                Back to Causes
            </a>
        </div>

        <!-- EDIT FORM -->

        <div class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gold/20">

            <h1 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">
                Edit Cause
            </h1>

            <p class="text-charcoal-light mb-8">
                Update the details of your fundraising campaign.
            </p>

            <form id="causeForm" method="POST" class="space-y-6 validation-form">

                <!-- SUCCESS MESSAGE -->

                <?php if ($successMsg != ''): ?>

                    <div class="p-4 rounded-xl bg-forest/10 border border-forest text-forest font-bold text-sm mb-6">
                        <?= $successMsg ?>
                    </div>

                <?php endif; ?>

                <!-- ERROR MESSAGE -->

                <?php if ($errorMsg != ''): ?>

                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-bold text-sm mb-6">
                        <?= $errorMsg ?>
                    </div>

                <?php endif; ?>

                <div class="space-y-4">

                    <!-- CAUSE TITLE -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Cause Title <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="<?= $users['title'] ?>"
                            required
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                            data-validate="required">
                    </div>

                    <!-- CATEGORY -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="category"
                            id="category"
                            required
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white appearance-none">

                            <option value="community" <?= $users['category'] == 'community' ? 'selected' : '' ?>>
                                Community Development
                            </option>

                            <option value="education" <?= $users['category'] == 'education' ? 'selected' : '' ?>>
                                Education
                            </option>

                            <option value="health" <?= $users['category'] == 'health' ? 'selected' : '' ?>>
                                Health & Nutrition
                            </option>

                            <option value="environment" <?= $users['category'] == 'environment' ? 'selected' : '' ?>>
                                Environment
                            </option>

                        </select>
                    </div>

                    <!-- TARGET AMOUNT -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Target Amount (₹) <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            name="target"
                            id="target"
                            value="<?= $users['target_amount'] ?>"
                            required
                            min="1"
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                            data-validate="required numeric">
                    </div>

                    <!-- SHORT DESCRIPTION -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Short Description <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            name="short_desc"
                            id="short_desc"
                            required
                            rows="2"
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                            data-validate="required"><?= $users['short_description'] ?></textarea>
                    </div>

                    <!-- DETAILED DESCRIPTION -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-1">
                            Detailed Description
                        </label>

                        <textarea
                            name="long_desc"
                            id="long_desc"
                            rows="5"
                            class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"><?= $users['detailed_description'] ?></textarea>
                    </div>

                    <!-- DATES -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                Start Date
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                value="<?= $users['start_date'] ?>"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                End Date
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                value="<?= $users['end_date'] ?>"
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>

                    </div>

                </div>

                <!-- BUTTONS -->

                <div class="flex flex-col md:flex-row flex-wrap gap-4 pt-4 border-t border-charcoal/10 mt-8">

                    <a href="<?= baseUrl('ngo/causes.php') ?>"
                       class="flex-1 text-center bg-ivory text-charcoal font-bold py-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/20">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="update_cause"
                        class="flex-1 text-center bg-forest text-white font-bold py-3 rounded-xl hover:bg-forest-dark transition-colors shadow-md">
                        Update Cause
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>

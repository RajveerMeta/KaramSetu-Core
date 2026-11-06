
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

/* GET ACCEPTED CONTRIBUTION TYPES */

$accepted_types = [];
$typesQuery = mysqli_query($con, "SELECT contribution_type FROM cause_accepted_types WHERE cause_id = $id");
if ($typesQuery) {
    while ($tRow = mysqli_fetch_assoc($typesQuery)) {
        $accepted_types[] = $tRow['contribution_type'];
    }
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
    $accepted_types = isset($_POST['accepted_types']) && is_array($_POST['accepted_types']) ? $_POST['accepted_types'] : [];

    if (
        empty($title) ||
        empty($category) ||
        empty($target) ||
        empty($short_desc) ||
        empty($start_date) ||
        empty($end_date)
    ) {
        $errorMsg = "Please fill all required fields.";
    } elseif (empty($accepted_types)) {
        $errorMsg = "Please select at least one accepted contribution type.";
    } elseif ($target <= 0) {
        $errorMsg = "Target amount must be greater than zero.";
    } elseif (
        !empty($start_date) &&
        !empty($end_date) &&
        $end_date < $start_date
    ) {
        $errorMsg = "End date cannot be before start date.";
    } else {

        $title_esc = mysqli_real_escape_string($con, trim($title));
        $category_esc = mysqli_real_escape_string($con, trim($category));
        $target_val = (float) $target;
        $short_desc_esc = mysqli_real_escape_string($con, trim($short_desc));
        $long_desc_esc = mysqli_real_escape_string($con, trim($long_desc));
        $start_date_esc = mysqli_real_escape_string($con, trim($start_date));
        $end_date_esc = mysqli_real_escape_string($con, trim($end_date));

        $updateQuery = "UPDATE causes SET
                        title = '$title_esc',
                        category = '$category_esc',
                        target_amount = $target_val,
                        short_description = '$short_desc_esc',
                        detailed_description = '$long_desc_esc',
                        start_date = '$start_date_esc',
                        end_date = '$end_date_esc'
                        WHERE id = $id
                        AND ngo_id = $ngo_id";

        if (mysqli_query($con, $updateQuery)) {

            /* UPDATE ACCEPTED CONTRIBUTION TYPES */
            mysqli_query($con, "DELETE FROM cause_accepted_types WHERE cause_id = $id");
            foreach ($accepted_types as $type) {
                $type = trim($type);
                if (in_array($type, ['money', 'food', 'clothes', 'items'])) {
                    mysqli_query($con, "INSERT INTO cause_accepted_types (cause_id, contribution_type) VALUES ($id, '$type')");
                }
            }

            $successMsg = "Cause updated successfully!";

            /* REFRESH CAUSE DETAILS */
            $accepted_types = [];
            $typesQuery = mysqli_query($con, "SELECT contribution_type FROM cause_accepted_types WHERE cause_id = $id");
            if ($typesQuery) {
                while ($tRow = mysqli_fetch_assoc($typesQuery)) {
                    $accepted_types[] = $tRow['contribution_type'];
                }
            }

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

                    <!-- ACCEPTED CONTRIBUTION TYPES -->

                    <div>
                        <label class="block text-sm font-bold text-charcoal mb-2">
                            Accepted Contribution Types <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="flex items-center gap-2 p-3 border border-charcoal/20 rounded-xl bg-ivory/30 hover:bg-white cursor-pointer transition-colors">
                                <input type="checkbox" name="accepted_types[]" value="money" <?= in_array('money', $accepted_types) ? 'checked' : '' ?> class="h-4 w-4 text-forest rounded border-charcoal/30 accent-forest">
                                <span class="text-sm font-medium text-charcoal">Money</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-charcoal/20 rounded-xl bg-ivory/30 hover:bg-white cursor-pointer transition-colors">
                                <input type="checkbox" name="accepted_types[]" value="food" <?= in_array('food', $accepted_types) ? 'checked' : '' ?> class="h-4 w-4 text-forest rounded border-charcoal/30 accent-forest">
                                <span class="text-sm font-medium text-charcoal">Food</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-charcoal/20 rounded-xl bg-ivory/30 hover:bg-white cursor-pointer transition-colors">
                                <input type="checkbox" name="accepted_types[]" value="clothes" <?= in_array('clothes', $accepted_types) ? 'checked' : '' ?> class="h-4 w-4 text-forest rounded border-charcoal/30 accent-forest">
                                <span class="text-sm font-medium text-charcoal">Clothes</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-charcoal/20 rounded-xl bg-ivory/30 hover:bg-white cursor-pointer transition-colors">
                                <input type="checkbox" name="accepted_types[]" value="items" <?= in_array('items', $accepted_types) ? 'checked' : '' ?> class="h-4 w-4 text-forest rounded border-charcoal/30 accent-forest">
                                <span class="text-sm font-medium text-charcoal">Useful Items</span>
                            </label>
                        </div>
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
                                Start Date <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                value="<?= $users['start_date'] ?>" required
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-charcoal mb-1">
                                End Date <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                value="<?= $users['end_date'] ?>" required
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

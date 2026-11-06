<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$successMsg = "";
$errorMsg = "";

$title = "";
$category = "";
$target = "";
$short_desc = "";
$long_desc = "";
$start_date = "";
$end_date = "";
$accepted_types = [];

/* Get logged-in NGO */
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if ($user_id > 0) {
    $ngoQuery = mysqli_query(
        $con,
        "SELECT id FROM ngos WHERE user_id = $user_id LIMIT 1"
    );

    $ngo = $ngoQuery ? mysqli_fetch_assoc($ngoQuery) : null;
    $ngo_id = $ngo ? (int) $ngo['id'] : 0;
} else {
    $ngo_id = 0;
}

if (isset($_POST['create_cause'])) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $target = trim($_POST['target'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $long_desc = trim($_POST['long_desc'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $accepted_types = isset($_POST['accepted_types']) && is_array($_POST['accepted_types']) ? $_POST['accepted_types'] : [];

    if ($ngo_id <= 0) {
        $errorMsg = "NGO account not found. Please log in again.";
    } elseif (
        $title === '' ||
        $category === '' ||
        $target === '' ||
        $short_desc === '' ||
        $start_date === '' ||
        $end_date === ''
    ) {
        $errorMsg = "Please fill in all required fields.";
    } elseif (empty($accepted_types)) {
        $errorMsg = "Please select at least one accepted contribution type.";
    } elseif (!is_numeric($target) || (float) $target <= 0) {
        $errorMsg = "Please enter a valid target amount.";
    } elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date) ||
        !checkdate((int) substr($start_date, 5, 2), (int) substr($start_date, 8, 2), (int) substr($start_date, 0, 4)) ||
        !checkdate((int) substr($end_date, 5, 2), (int) substr($end_date, 8, 2), (int) substr($end_date, 0, 4))
    ) {
        $errorMsg = "Please enter valid dates.";
    } elseif ($end_date < $start_date) {
        $errorMsg = "End date cannot be earlier than start date.";
    } else {
        $target_amount = (float) $target;

        $stmt = mysqli_prepare(
            $con,
            "INSERT INTO causes
            (ngo_id, title, category, target_amount, short_description,
             detailed_description, start_date, end_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issdssss",
                $ngo_id,
                $title,
                $category,
                $target_amount,
                $short_desc,
                $long_desc,
                $start_date,
                $end_date
            );

            if (mysqli_stmt_execute($stmt)) {
                $new_cause_id = mysqli_insert_id($con);
                foreach ($accepted_types as $type) {
                    $type = trim($type);
                    if (in_array($type, ['money', 'food', 'clothes', 'items'])) {
                        mysqli_query($con, "INSERT INTO cause_accepted_types (cause_id, contribution_type) VALUES ($new_cause_id, '$type')");
                    }
                }
                $successMsg = "Cause created successfully!";

                $title = "";
                $category = "";
                $target = "";
                $short_desc = "";
                $long_desc = "";
                $start_date = "";
                $end_date = "";
$accepted_types = [];
            } else {
                $errorMsg = "Unable to create the cause. Please try again.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $errorMsg = "Unable to prepare the database query.";
        }
    }
}

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <a href="<?= baseUrl('ngo/causes.php') ?>"
               class="text-sm font-bold text-gold hover:text-forest transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Causes
            </a>
        </div>

        <div class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gold/20">

            <h1 class="text-xl md:text-3xl font-serif font-bold text-forest mb-2">
                Create New Cause
            </h1>

            <p class="text-charcoal-light mb-8">
                Start a new fundraising campaign for your organization.
            </p>

            <?php if ($successMsg !== ''): ?>
                <div class="p-4 mb-6 rounded-xl bg-forest/10 border border-forest text-forest font-bold text-sm">
                    <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg !== ''): ?>
                <div class="p-4 mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 font-bold text-sm">
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form id="causeForm" method="POST" class="space-y-6 validation-form" novalidate>

                <div class="space-y-4">

                    <div>
                        <label for="title" class="block text-sm font-bold text-charcoal mb-1">
                            Cause Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title"
                               value="<?= htmlspecialchars($title) ?>"
                               maxlength="200" required
                               class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                               placeholder="e.g. Clean Water Initiative">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-bold text-charcoal mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white appearance-none">
                            <option value="">Select a category</option>
                            <option value="education" <?= $category === 'education' ? 'selected' : '' ?>>Education</option>
                            <option value="health" <?= $category === 'health' ? 'selected' : '' ?>>Health &amp; Nutrition</option>
                            <option value="environment" <?= $category === 'environment' ? 'selected' : '' ?>>Environment</option>
                            <option value="community" <?= $category === 'community' ? 'selected' : '' ?>>Community Development</option>
                        </select>
                    </div>

                    <div>
                        <label for="target" class="block text-sm font-bold text-charcoal mb-1">
                            Target Amount (₹) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="target" name="target"
                               value="<?= htmlspecialchars($target) ?>"
                               min="1" step="0.01" required
                               class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                               placeholder="500000">
                    </div>

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

                    <div>
                        <label for="short_desc" class="block text-sm font-bold text-charcoal mb-1">
                            Short Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="short_desc" name="short_desc" required rows="2"
                                  class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                  placeholder="A brief summary of this cause..."><?= htmlspecialchars($short_desc) ?></textarea>
                    </div>

                    <div>
                        <label for="long_desc" class="block text-sm font-bold text-charcoal mb-1">
                            Detailed Description
                        </label>
                        <textarea id="long_desc" name="long_desc" rows="5"
                                  class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white"
                                  placeholder="Provide full details about how the funds will be used..."><?= htmlspecialchars($long_desc) ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-bold text-charcoal mb-1">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date"
                                   value="<?= htmlspecialchars($start_date) ?>" required
                                   class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-bold text-charcoal mb-1">
                                End Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date"
                                   value="<?= htmlspecialchars($end_date) ?>" required
                                   class="w-full px-4 py-3 border border-charcoal/20 rounded-xl text-charcoal focus:outline-none focus:ring-2 focus:ring-forest focus:border-transparent transition-all bg-ivory/30 focus:bg-white">
                        </div>
                    </div>

                </div>

                <div class="flex flex-col md:flex-row flex-wrap gap-4 pt-4 border-t border-charcoal/10 mt-8">
                    <a href="<?= baseUrl('ngo/causes.php') ?>"
                       class="flex-1 text-center bg-ivory text-charcoal font-bold py-3 rounded-xl hover:bg-charcoal/5 transition-colors border border-charcoal/20">
                        Cancel
                    </a>

                    <button type="submit" name="create_cause"
                            class="flex-1 text-center bg-forest text-white font-bold py-3 rounded-xl hover:bg-forest-dark transition-colors shadow-md">
                        Create Cause
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
    
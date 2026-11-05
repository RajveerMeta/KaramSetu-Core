
<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../DataBase/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$causeId = (int) $_GET['id'];

if ($causeId <= 0) {
    header('Location: ' . baseUrl('admin/causes/index.php'));
    exit;
}

/* Update cause status */
if (isset($_POST['update_status'])) {
    $newStatus = $_POST['new_status'];

    if (in_array($newStatus, ['active', 'suspended'], true)) {
        $statusSql = mysqli_real_escape_string($con, $newStatus);

        $updateSql = "UPDATE causes
                      SET status = '$statusSql'
                      WHERE id = $causeId
                      AND status IN ('active', 'suspended')";

        mysqli_query($con, $updateSql);
    }

    header('Location: ' . baseUrl('admin/causes/view.php?id=' . $causeId));
    exit;
}

/* Get cause and NGO details */
$sql = "SELECT
            c.id,
            c.title,
            c.category,
            c.target_amount,
            c.short_description,
            c.detailed_description,
            c.start_date,
            c.end_date,
            c.status,
            c.created_at,
            n.id AS ngo_id,
            n.name AS ngo_name,
            n.registration_number,
            n.official_email,
            n.city,
            n.state,
            n.verification_status
        FROM causes c
        INNER JOIN ngos n ON n.id = c.ngo_id
        WHERE c.id = $causeId
        LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: ' . baseUrl('admin/causes/index.php'));
    exit;
}

$cause = mysqli_fetch_assoc($result);

$description = $cause['detailed_description'];

$ngoName = $cause['ngo_name'];
$ngoCity = $cause['city'];
$ngoState = $cause['state'];
$location = $ngoCity . ', ' . $ngoState;
$ngoStatus = ucfirst($cause['verification_status']);

/* Fundraising and contribution statistics */
$statsSql = "SELECT
                COUNT(*) AS total_contributions,
                COUNT(DISTINCT user_id) AS registered_contributors,
                COALESCE(SUM(
                    CASE
                        WHEN contribution_type = 'money'
                         AND payment_status = 'completed'
                        THEN monetary_amount
                        ELSE 0
                    END
                ), 0) AS money_raised,
                SUM(
                    CASE
                        WHEN contribution_type IN ('food', 'clothes', 'items')
                         AND fulfillment_status IN ('received', 'completed')
                        THEN 1
                        ELSE 0
                    END
                ) AS resource_contributions
             FROM contributions
             WHERE cause_id = $causeId";

$statsResult = mysqli_query($con, $statsSql);
$stats = mysqli_fetch_assoc($statsResult);

$raised = (float) $stats['money_raised'];
$target = (float) $cause['target_amount'];
$remaining = max(0, $target - $raised);

$progress = $target > 0
    ? min(100, round(($raised / $target) * 100))
    : 0;

/* Recent contributions */
$contributionsSql = "SELECT
                        donor_name,
                        is_anonymous,
                        contribution_type,
                        monetary_amount,
                        payment_status,
                        fulfillment_status,
                        created_at
                     FROM contributions
                     WHERE cause_id = $causeId
                     ORDER BY created_at DESC
                     LIMIT 10";

$contributionsResult = mysqli_query($con, $contributionsSql);

$contributions = [];

while ($row = mysqli_fetch_assoc($contributionsResult)) {
    $typeLabels = [
        'money' => 'Money',
        'food' => 'Food',
        'clothes' => 'Clothes',
        'items' => 'Items'
    ];

    $contributionStatus = $row['contribution_type'] === 'money'
        ? ucfirst($row['payment_status'])
        : ucfirst($row['fulfillment_status']);

    if ((int) $row['is_anonymous'] === 1) {
        $contributor = 'Anonymous';
    } else {
        $contributor = $row['donor_name'];
    }

    if ($row['contribution_type'] === 'money') {
        $amount = '₹' . number_format((float) $row['monetary_amount'], 2);
    } else {
        $amount = '—';
    }

    $contributions[] = [
        'contributor' => $contributor,
        'type' => $typeLabels[$row['contribution_type']],
        'amount' => $amount,
        'date' => date('d M Y', strtotime($row['created_at'])),
        'status' => $contributionStatus
    ];
}

/* Values used by the existing page */
$cause['name'] = $cause['title'];
$cause['ngo'] = $ngoName;
$cause['created_date'] = date('d M Y', strtotime($cause['created_at']));
$cause['start_date'] = date('d M Y', strtotime($cause['start_date']));
$cause['end_date'] = date('d M Y', strtotime($cause['end_date']));
$cause['location'] = $location;
$cause['target'] = $target;
$cause['raised'] = $raised;
$cause['ngo_reg'] = $cause['registration_number'];
$cause['ngo_email'] = $cause['official_email'];
$cause['ngo_status'] = $ngoStatus;

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="mb-6">
        <a href="<?= e(baseUrl('admin/causes/index.php')) ?>"
           class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Causes
        </a>

        <h1 class="text-3xl font-serif font-bold text-forest mb-2">
            Cause Details
        </h1>

        <p class="text-charcoal-light">
            Review cause information, fundraising progress, and contribution activity.
        </p>
    </div>

    <!-- Cause Header -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm mb-8 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4 mb-2">
                    <h2 class="text-2xl font-serif font-bold text-charcoal">
                        <?= e($cause['name']) ?>
                    </h2>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        <?= $cause['status'] === 'active'
                            ? 'bg-green-100 text-green-800 border border-green-200'
                            : ($cause['status'] === 'completed'
                                ? 'bg-blue-100 text-blue-800 border border-blue-200'
                                : 'bg-red-100 text-red-800 border border-red-200') ?>">
                        <?= e(ucfirst($cause['status'])) ?>
                    </span>
                </div>

                <p class="text-charcoal-light font-medium text-sm mb-1">
                    <?= e($cause['ngo']) ?>
                </p>

                <p class="text-sm text-forest font-medium">
                    <?= e($cause['category']) ?>
                </p>
            </div>

            <div class="text-left md:text-right">
                <p class="text-sm text-charcoal-light">Cause ID</p>
                <p class="text-lg font-bold text-forest">
                    #<?= (int) $cause['id'] ?>
                </p>
            </div>

        </div>
    </div>

    <!-- Cause Information and Managing NGO -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Cause Information -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Cause Information
                </h3>
            </div>

            <div class="p-6">
                <dl class="space-y-5">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Cause Name</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2">
                            <?= e($cause['name']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Category</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['category']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Created By</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['ngo']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Created Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['created_date']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Start Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['start_date']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">End Date</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['end_date']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Location</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= e($cause['location']) ?>
                        </dd>
                    </div>

                </dl>
            </div>
        </div>

        <!-- Managing NGO -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h3 class="text-lg font-serif font-bold text-forest">
                    Managing NGO
                </h3>
            </div>

            <div class="p-6 flex flex-col justify-between h-full">
                <div>
                    <h4 class="text-lg font-bold text-charcoal mb-1">
                        <?= e($cause['ngo']) ?>
                    </h4>

                    <p class="text-sm text-charcoal-light mb-5">
                        <?= e($location) ?>
                    </p>

                    <dl class="space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">
                                Registration Number
                            </dt>
                            <dd class="text-sm font-medium text-charcoal sm:col-span-2 break-words">
                                <?= e($cause['ngo_reg']) ?>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Email</dt>
                            <dd class="text-sm sm:col-span-2 break-words">
                                <a href="mailto:<?= e($cause['ngo_email']) ?>"
                                   class="text-forest hover:text-gold transition-colors">
                                    <?= e($cause['ngo_email']) ?>
                                </a>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Verification</dt>
                            <dd class="text-sm font-medium sm:col-span-2
                                <?= $cause['ngo_status'] === 'Approved' ? 'text-green-700' : 'text-red-700' ?>">
                                <?= e($cause['ngo_status']) ?>
                            </dd>
                        </div>

                    </dl>
                </div>

                <div class="mt-6 pt-4 border-t border-charcoal/5">
                    <a href="<?= e(baseUrl('admin/ngos/view.php?id=' . (int) $cause['ngo_id'])) ?>"
                       class="inline-flex justify-center items-center px-4 py-2 border border-forest text-forest hover:bg-forest hover:text-white text-sm font-medium rounded-md transition-colors">
                        View NGO
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- About This Cause -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">
                About This Cause
            </h3>
        </div>

        <div class="p-6">
            <div class="text-sm leading-7 text-charcoal">
                <?= nl2br(e($description)) ?>
            </div>
        </div>
    </div>

    <!-- Fundraising Overview -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">
                Fundraising Overview
            </h3>
        </div>

        <div class="p-6">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-5 gap-4">

                <div>
                    <p class="text-sm font-medium text-charcoal-light">Raised</p>
                    <p class="text-3xl font-bold text-forest mt-1">
                        ₹<?= number_format($raised, 2) ?>
                    </p>
                    <p class="text-sm text-charcoal-light mt-1">
                        Target: ₹<?= number_format($target, 2) ?>
                    </p>
                </div>

                <div class="md:text-right">
                    <p class="text-sm font-medium text-charcoal-light">Remaining</p>
                    <p class="text-xl font-bold text-charcoal mt-1">
                        ₹<?= number_format($remaining, 2) ?>
                    </p>
                </div>

            </div>

            <div>
                <div class="flex justify-between text-sm font-medium mb-2">
                    <span class="text-charcoal-light">Fundraising Progress</span>
                    <span class="text-forest font-bold"><?= $progress ?>%</span>
                </div>

                <div class="w-full bg-ivory rounded-full h-3 border border-charcoal/10 overflow-hidden">
                    <div class="bg-forest h-full rounded-full"
                         style="width: <?= $progress ?>%;"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Contribution Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Total Contributions</p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= (int) $stats['total_contributions'] ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Money Raised</p>
            <p class="text-2xl font-bold text-forest mt-1">
                ₹<?= number_format($raised, 2) ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Resource Contributions</p>
            <p class="text-2xl font-bold text-gold mt-1">
                <?= (int) $stats['resource_contributions'] ?>
            </p>
        </div>

        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-5">
            <p class="text-sm font-medium text-charcoal-light">Registered Contributors</p>
            <p class="text-2xl font-bold text-charcoal mt-1">
                <?= (int) $stats['registered_contributors'] ?>
            </p>
        </div>

    </div>

    <!-- Recent Contributions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h3 class="text-lg font-serif font-bold text-forest">
                Recent Contributions
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] table-fixed text-left border-collapse">

                <colgroup>
                    <col class="w-[25%]">
                    <col class="w-[17%]">
                    <col class="w-[20%]">
                    <col class="w-[18%]">
                    <col class="w-[20%]">
                </colgroup>

                <thead>
                    <tr class="bg-ivory/30 text-charcoal-light text-xs uppercase tracking-wide border-b border-charcoal/10">
                        <th class="px-6 py-4 font-semibold">Contributor</th>
                        <th class="px-6 py-4 font-semibold">Type</th>
                        <th class="px-6 py-4 font-semibold text-right">Amount</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">

                    <?php if (count($contributions) > 0): ?>

                        <?php foreach ($contributions as $contrib): ?>
                            <tr class="hover:bg-ivory/30 transition-colors">

                                <td class="px-6 py-4 text-sm font-medium text-charcoal break-words">
                                    <?= e($contrib['contributor']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-charcoal-light">
                                    <?= e($contrib['type']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap
                                    <?= $contrib['amount'] === '—' ? 'text-charcoal-light' : 'text-forest' ?>">
                                    <?= e($contrib['amount']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-charcoal-light whitespace-nowrap">
                                    <?= e($contrib['date']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        <?= in_array(strtolower($contrib['status']), ['completed', 'received'], true)
                                            ? 'bg-green-50 text-green-700 border border-green-200'
                                            : (strtolower($contrib['status']) === 'failed'
                                                || strtolower($contrib['status']) === 'cancelled'
                                                ? 'bg-red-50 text-red-700 border border-red-200'
                                                : 'bg-amber-50 text-amber-700 border border-amber-200') ?>">
                                        <?= e($contrib['status']) ?>
                                    </span>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="text-sm font-semibold text-charcoal">
                                    No contributions found
                                </p>
                                <p class="text-sm text-charcoal-light mt-1">
                                    Contributions for this cause will appear here.
                                </p>
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Cause Status and Admin Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">

        <div class="max-w-2xl">
            <h3 class="text-lg font-serif font-bold text-forest mb-2">
                Cause Status
            </h3>

            <p class="text-sm text-charcoal leading-relaxed">
                <?php if ($cause['status'] === 'active'): ?>
                    This cause is currently active and accepting contributions.
                <?php elseif ($cause['status'] === 'suspended'): ?>
                    This cause is currently suspended.
                <?php else: ?>
                    This cause has been completed.
                <?php endif; ?>
            </p>
        </div>

        <?php if (in_array($cause['status'], ['active', 'suspended'], true)): ?>

            <form method="POST"
                  action="<?= e(baseUrl('admin/causes/view.php?id=' . (int) $cause['id'])) ?>">

                <input type="hidden"
                       name="new_status"
                       value="<?= $cause['status'] === 'active' ? 'suspended' : 'active' ?>">

                <button type="submit"
                        name="update_status"
                        value="1"
                        class="px-6 py-2.5 bg-white border <?= $cause['status'] === 'active'
                            ? 'border-red-200 text-red-600 hover:bg-red-50'
                            : 'border-green-200 text-green-700 hover:bg-green-50' ?> font-medium rounded-md transition-colors shadow-sm text-sm">
                    <?= $cause['status'] === 'active' ? 'Suspend Cause' : 'Activate Cause' ?>
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>

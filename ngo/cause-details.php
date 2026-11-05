
<?php
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../Database/db_config.php';

ob_start();

$ngoId = 0;
$cause = null;
$recentContributions = [];

$totalRaised = 0;
$totalDonors = 0;
$fundingGoal = 0;
$progress = 0;
$daysRemaining = 0;

$pageError = '';

/* GET LOGGED-IN NGO */

if (!isset($_SESSION['user_id'])) {
    $pageError = 'Please log in to view this cause.';
} else {

    $userId = (int) $_SESSION['user_id'];

    $ngoQuery = "SELECT id FROM ngos WHERE user_id = $userId LIMIT 1";

    $ngoResult = mysqli_query($con, $ngoQuery);

    $ngo = mysqli_fetch_assoc($ngoResult);

    if ($ngo) {
        $ngoId = $ngo['id'];
    } else {
        $pageError = 'NGO account not found.';
    }
}

/* GET CAUSE ID */

$causeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($pageError == '' && $causeId <= 0) {
    $pageError = 'Invalid cause ID.';
}

/* GET CAUSE DETAILS */

if ($pageError == '' && $ngoId > 0) {

    $query = "SELECT * FROM causes
              WHERE id = $causeId
              AND ngo_id = $ngoId";

    $result = mysqli_query($con, $query);

    $cause = mysqli_fetch_assoc($result);

    if (!$cause) {
        $pageError = 'Cause not found or you do not have permission to view it.';
    }
}

/* GET FUNDING DETAILS */

if ($cause && $pageError == '') {

    $fundingGoal = (float) $cause['target_amount'];

    $fundingQuery = "SELECT
                        COALESCE(SUM(
                            CASE
                                WHEN LOWER(payment_status) IN
                                ('completed', 'success', 'successful', 'paid')
                                THEN monetary_amount
                                ELSE 0
                            END
                        ), 0) AS total_raised,

                        COUNT(DISTINCT
                            CASE
                                WHEN LOWER(payment_status) IN
                                ('completed', 'success', 'successful', 'paid')
                                THEN donor_name
                                ELSE NULL
                            END
                        ) AS total_donors

                    FROM contributions
                    WHERE ngo_id = $ngoId
                    AND cause_id = $causeId";

    $fundingResult = mysqli_query($con, $fundingQuery);

    $fundingData = mysqli_fetch_assoc($fundingResult);

    if ($fundingData) {
        $totalRaised = $fundingData['total_raised'];
        $totalDonors = $fundingData['total_donors'];
    }

    /* CALCULATE PROGRESS */

    if ($fundingGoal > 0) {
        $progress = min(100, round(($totalRaised / $fundingGoal) * 100));
    }

    /* CALCULATE REMAINING DAYS */

    if (!empty($cause['end_date'])) {

        $today = new DateTime(date('Y-m-d'));
        $endDate = new DateTime($cause['end_date']);

        if ($endDate >= $today) {
            $daysRemaining = $today->diff($endDate)->days;
        }
    }

    /* GET RECENT CONTRIBUTIONS */

    $contributionQuery = "SELECT donor_name, contribution_type,
                                 monetary_amount, created_at
                          FROM contributions
                          WHERE ngo_id = $ngoId
                          AND cause_id = $causeId
                          AND LOWER(payment_status) IN
                          ('completed', 'success', 'successful', 'paid')
                          ORDER BY created_at DESC
                          LIMIT 5";

    $contributionResult = mysqli_query($con, $contributionQuery);

    while ($row = mysqli_fetch_assoc($contributionResult)) {
        $recentContributions[] = $row;
    }
}
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <?php if ($pageError != ''): ?>

            <div class="bg-white rounded-3xl p-8 border border-gold/20 shadow-sm">

                <h1 class="text-2xl font-serif font-bold text-forest mb-3">
                    Unable to Open Cause
                </h1>

                <p class="text-charcoal mb-6">
                    <?= $pageError ?>
                </p>

                <a href="<?= baseUrl('ngo/causes.php') ?>"
                   class="inline-block bg-forest text-white font-bold py-3 px-6 rounded-full hover:bg-forest/90">
                    Back to Causes
                </a>

            </div>

        <?php elseif ($cause): ?>

            <!-- BACK LINK -->

            <div class="mb-8">

                <a href="<?= baseUrl('ngo/causes.php') ?>"
                   class="text-sm font-bold text-gold hover:text-forest transition-colors inline-flex items-center gap-1">

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

            <!-- HEADER -->

            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">

                <div>

                    <div class="flex flex-wrap items-center gap-3 mb-3">

                        <?php
                        $status = strtolower($cause['status']);

                        $statusClass = 'bg-gray-100 text-gray-700';

                        if ($status == 'active') {
                            $statusClass = 'bg-forest/10 text-forest';
                        } elseif ($status == 'completed') {
                            $statusClass = 'bg-blue-100 text-blue-800';
                        } elseif ($status == 'suspended') {
                            $statusClass = 'bg-red-100 text-red-800';
                        }
                        ?>

                        <span class="inline-block px-3 py-1 <?= $statusClass ?> text-xs font-bold uppercase tracking-wider rounded-md">
                            <?= ucfirst($status) ?>
                        </span>

                        <span class="text-sm font-bold text-charcoal-light uppercase tracking-wide">
                            <?= $cause['category'] ?>
                        </span>

                    </div>

                    <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest">
                        <?= $cause['title'] ?>
                    </h1>

                </div>

                <div>

                    <a href="<?= baseUrl('ngo/edit-cause.php?id=' . $cause['id']) ?>"
                       class="inline-block bg-white text-gold border border-gold font-bold py-2.5 px-6 rounded-full hover:bg-gold/5 transition-colors shadow-sm">
                        Edit Cause
                    </a>

                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- MAIN CONTENT -->

                <div class="lg:col-span-2 space-y-8">

                    <!-- CAUSE DETAILS -->

                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">

                        <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6 border-b border-charcoal/10 pb-4">
                            Cause Details
                        </h2>

                        <div class="space-y-6">

                            <!-- SHORT DESCRIPTION -->

                            <div>

                                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">
                                    Short Description
                                </h3>

                                <p class="text-lg text-charcoal font-medium">
                                    <?= $cause['short_description'] ?>
                                </p>

                            </div>

                            <!-- DETAILED DESCRIPTION -->

                            <div>

                                <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">
                                    Detailed Description
                                </h3>

                                <p class="text-base text-charcoal leading-relaxed">
                                    <?= $cause['detailed_description'] ?>
                                </p>

                            </div>

                            <!-- DATES -->

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-charcoal/10">

                                <div>

                                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">
                                        Start Date
                                    </h3>

                                    <p class="text-base font-bold text-charcoal">
                                        <?= !empty($cause['start_date']) ? date('d M Y', strtotime($cause['start_date'])) : 'Not set' ?>
                                    </p>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-charcoal-light uppercase tracking-wide mb-2">
                                        End Date
                                    </h3>

                                    <p class="text-base font-bold text-charcoal">
                                        <?= !empty($cause['end_date']) ? date('d M Y', strtotime($cause['end_date'])) : 'Not set' ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- RECENT CONTRIBUTIONS -->

                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">

                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6 border-b border-charcoal/10 pb-4">

                            <h2 class="text-xl font-serif font-bold text-forest">
                                Recent Contributions
                            </h2>

                            <a href="<?= baseUrl('ngo/contributions.php') ?>"
                               class="text-sm font-bold text-gold hover:text-forest transition-colors">
                                View All
                            </a>

                        </div>

                        <?php if (!empty($recentContributions)): ?>

                            <div class="space-y-4">

                                <?php foreach ($recentContributions as $contribution): ?>

                                    <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-ivory/30 rounded-xl border border-charcoal/5">

                                        <div>

                                            <p class="text-sm font-bold text-charcoal">
                                                <?= $contribution['donor_name'] ?: 'Anonymous Donor' ?>
                                            </p>

                                            <p class="text-xs text-charcoal/70 mt-1">

                                                <?= ucwords(str_replace('_', ' ', $contribution['contribution_type'])) ?>

                                                &bull;

                                                <?= date('d M Y', strtotime($contribution['created_at'])) ?>

                                            </p>

                                        </div>

                                        <div>

                                            <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-sm font-bold rounded-lg">
                                                ₹<?= number_format($contribution['monetary_amount'], 2) ?>
                                            </span>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <div class="text-center py-10">

                                <p class="text-charcoal-light">
                                    No completed contributions have been recorded for this cause yet.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <!-- FUNDING SIDEBAR -->

                <div class="space-y-6 lg:space-y-8">

                    <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">

                        <h3 class="text-lg font-serif font-bold text-forest mb-6">
                            Funding Progress
                        </h3>

                        <div class="mb-6">

                            <div class="flex flex-wrap items-end justify-between gap-4 mb-3">

                                <div>

                                    <p class="text-xs font-bold text-charcoal-light uppercase tracking-wide">
                                        Raised
                                    </p>

                                    <p class="text-xl md:text-3xl font-bold text-forest">
                                        ₹<?= number_format($totalRaised, 2) ?>
                                    </p>

                                </div>

                                <div class="text-right">

                                    <p class="text-xs font-bold text-charcoal-light uppercase tracking-wide">
                                        Goal
                                    </p>

                                    <p class="text-lg font-bold text-charcoal">

                                        <?php if ($fundingGoal > 0): ?>

                                            ₹<?= number_format($fundingGoal, 2) ?>

                                        <?php else: ?>

                                            Not set

                                        <?php endif; ?>

                                    </p>

                                </div>

                            </div>

                            <!-- PROGRESS BAR -->

                            <div class="w-full bg-ivory rounded-full h-3 mb-2 overflow-hidden border border-charcoal/5">

                                <div class="bg-forest h-3 rounded-full"
                                     style="width: <?= $progress ?>%">
                                </div>

                            </div>

                            <p class="text-sm font-bold text-forest text-right">

                                <?php if ($fundingGoal > 0): ?>

                                    <?= $progress ?>% Achieved

                                <?php else: ?>

                                    Funding goal not set

                                <?php endif; ?>

                            </p>

                        </div>

                        <!-- FUNDING INFORMATION -->

                        <div class="space-y-4 pt-6 border-t border-charcoal/10">

                            <div class="flex items-center justify-between gap-3 text-sm">

                                <span class="text-charcoal-light font-medium">
                                    Total Donors
                                </span>

                                <span class="font-bold text-charcoal">
                                    <?= number_format($totalDonors) ?>
                                </span>

                            </div>

                            <div class="flex items-center justify-between gap-3 text-sm">

                                <span class="text-charcoal-light font-medium">
                                    Days Remaining
                                </span>

                                <span class="font-bold text-charcoal">

                                    <?php if (!empty($cause['end_date'])): ?>

                                        <?= $daysRemaining ?>

                                    <?php else: ?>

                                        Not set

                                    <?php endif; ?>

                                </span>

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
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>

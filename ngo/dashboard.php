<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Database/db_config.php';

/* =========================
   GET LOGGED-IN NGO
========================= */

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$ngo = [];
$ngo_id = 0;

if ($user_id > 0) {
    $sql = "SELECT * FROM ngos WHERE user_id = $user_id LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $ngo = mysqli_fetch_assoc($result);
        $ngo_id = (int) $ngo['id'];
    }
}

/* =========================
   DEFAULT DASHBOARD DATA
========================= */

$dashboardData = [
    'total_funds_raised' => '₹0',
    'active_causes' => 0,
    'upcoming_events_count' => 0,
    'total_contributions' => 0,
    'fundraising_raised' => 0,
    'fundraising_goal' => 0,
    'fundraising_progress' => 0,
    'recent_contributions' => [],
    'active_causes_list' => []
];

$upcoming_events = [];

/* =========================
   FETCH DASHBOARD DATA
========================= */

if ($ngo_id > 0) {

    /* Total Funds Raised */

    $sql = "SELECT COALESCE(SUM(monetary_amount), 0) AS total
            FROM contributions
            WHERE ngo_id = $ngo_id
            AND contribution_type = 'money'
            AND payment_status = 'completed'";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        $dashboardData['fundraising_raised'] = (float) $row['total'];

        $dashboardData['total_funds_raised'] =
            '₹' . number_format($dashboardData['fundraising_raised']);
    }

    /* Active Causes Count */

    $sql = "SELECT COUNT(*) AS total
            FROM causes
            WHERE ngo_id = $ngo_id
            AND status = 'active'";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $dashboardData['active_causes'] = (int) $row['total'];
    }

    /* Total Contributions */

    $sql = "SELECT COUNT(*) AS total
            FROM contributions
            WHERE ngo_id = $ngo_id";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $dashboardData['total_contributions'] = (int) $row['total'];
    }

    /* Total Fundraising Goal */

    $sql = "SELECT COALESCE(SUM(target_amount), 0) AS total
            FROM causes
            WHERE ngo_id = $ngo_id
            AND status = 'active'";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $dashboardData['fundraising_goal'] = (float) $row['total'];
    }

    /* Upcoming Events Count */

    $sql = "SELECT COUNT(*) AS total
            FROM events
            WHERE ngo_id = $ngo_id
            AND event_date >= CURDATE()";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $dashboardData['upcoming_events_count'] = (int) $row['total'];
    }

    /* Upcoming Events List */

    $sql = "SELECT *
            FROM events
            WHERE ngo_id = $ngo_id
            AND event_date >= CURDATE()
            ORDER BY event_date ASC
            LIMIT 2";

    $result = mysqli_query($con, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $upcoming_events[] = $row;
        }
    }

    /* Recent Contributions */

    $sql = "SELECT
                c.id,
                c.donor_name,
                c.is_anonymous,
                c.contribution_type,
                c.monetary_amount,
                c.created_at,
                COALESCE(items.total_quantity, 0) AS total_quantity,
                items.unit
            FROM contributions c
            LEFT JOIN (
                SELECT
                    contribution_id,
                    SUM(quantity) AS total_quantity,
                    MAX(unit) AS unit
                FROM contribution_items
                GROUP BY contribution_id
            ) items ON items.contribution_id = c.id
            WHERE c.ngo_id = $ngo_id
            ORDER BY c.created_at DESC
            LIMIT 5";

    $result = mysqli_query($con, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {

            $donor = $row['is_anonymous']
                ? 'Anonymous'
                : $row['donor_name'];

            $type = ucfirst($row['contribution_type']);

            if ($row['contribution_type'] === 'money') {
                $amount = '₹' . number_format(
                    (float) $row['monetary_amount']
                );
            } else {
                $amount = number_format(
                    (float) $row['total_quantity']
                );

                $amount .= !empty($row['unit'])
                    ? ' ' . $row['unit']
                    : ' items';
            }

            $date = date(
                'd M Y, h:i A',
                strtotime($row['created_at'])
            );

            $dashboardData['recent_contributions'][] = [
                'donor' => $donor,
                'type' => $type,
                'amount' => $amount,
                'date' => $date
            ];
        }
    }

    /* Active Causes List */

    $sql = "SELECT
                c.id,
                c.title,
                c.target_amount,
                COALESCE(SUM(
                    CASE
                        WHEN con.contribution_type = 'money'
                        AND con.payment_status = 'completed'
                        THEN con.monetary_amount
                        ELSE 0
                    END
                ), 0) AS raised
            FROM causes c
            LEFT JOIN contributions con
                ON con.cause_id = c.id
                AND con.ngo_id = $ngo_id
            WHERE c.ngo_id = $ngo_id
            AND c.status = 'active'
            GROUP BY c.id, c.title, c.target_amount
            ORDER BY c.id DESC
            LIMIT 5";

    $result = mysqli_query($con, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {

            $raised = (float) $row['raised'];
            $goal = (float) $row['target_amount'];

            $progress = $goal > 0
                ? min(100, round(($raised / $goal) * 100))
                : 0;

            $dashboardData['active_causes_list'][] = [
                'id' => $row['id'],
                'name' => $row['title'],
                'raised' => '₹' . number_format($raised),
                'goal' => '₹' . number_format($goal),
                'progress' => $progress
            ];
        }
    }

    /* Overall Fundraising Progress */

    if ($dashboardData['fundraising_goal'] > 0) {
        $dashboardData['fundraising_progress'] = min(
            100,
            round(
                ($dashboardData['fundraising_raised'] /
                $dashboardData['fundraising_goal']) * 100
            )
        );
    }
}

/* =========================
   VERIFICATION STATUS
========================= */

$verification_status = $ngo['verification_status'] ?? 'pending';
$is_verified = ($verification_status === 'approved');

ob_start();
?>

<div class="min-h-screen bg-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-4xl font-serif font-bold text-forest mb-2">
                    Welcome back, <?= $ngo['name'] ?? 'NGO' ?>
                </h1>

                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="bg-forest/10 text-forest font-bold px-3 py-1 rounded-full">
                        <?= $is_verified ? 'Verified NGO' : ucfirst($verification_status) ?>
                    </span>

                    <span class="text-charcoal-light">|</span>
                    <span class="text-charcoal-light">Dashboard Overview</span>
                </div>
            </div>

            <a href="<?= baseUrl('ngo/profile.php') ?>"
               class="inline-block bg-white text-forest border border-forest font-bold py-2.5 px-6 rounded-full hover:bg-forest/5 transition-colors shadow-sm">
                View Public Profile
            </a>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 w-full">

            <!-- MAIN DASHBOARD CONTENT -->
            <div class="lg:col-span-2 space-y-8">

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">
                            Total Funds
                        </p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest">
                            <?= $dashboardData['total_funds_raised'] ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">
                            Active Causes
                        </p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest">
                            <?= $dashboardData['active_causes'] ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">
                            Upcoming Events
                        </p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest">
                            <?= $dashboardData['upcoming_events_count'] ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gold/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-charcoal-light mb-1">
                            Contributions
                        </p>
                        <p class="text-xl md:text-2xl font-serif font-bold text-forest">
                            <?= $dashboardData['total_contributions'] ?>
                        </p>
                    </div>

                </div>

                <!-- FUNDRAISING OVERVIEW -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">

                    <h2 class="text-xl md:text-2xl font-serif font-bold text-forest mb-6">
                        Fundraising Progress
                    </h2>

                    <div class="flex flex-wrap items-end justify-between gap-4 mb-2">
                        <div>
                            <p class="text-sm font-bold text-charcoal-light">Total Raised</p>
                            <p class="text-xl md:text-3xl font-bold text-forest">
                                <?= $dashboardData['total_funds_raised'] ?>
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm font-bold text-charcoal-light">Overall Goal</p>
                            <p class="text-xl font-bold text-charcoal">
                                ₹<?= number_format($dashboardData['fundraising_goal']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="w-full bg-ivory rounded-full h-4 mb-4 overflow-hidden border border-charcoal/10">
                        <div class="bg-forest h-4 rounded-full"
                             style="width: <?= $dashboardData['fundraising_progress'] ?>%">
                        </div>
                    </div>

                    <p class="text-sm font-medium text-forest text-right">
                        <?= $dashboardData['fundraising_progress'] ?>% to goal
                    </p>
                </div>

                <!-- RECENT CONTRIBUTIONS AND ACTIVE CAUSES -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- RECENT CONTRIBUTIONS -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h2 class="text-xl font-serif font-bold text-forest">
                                Recent Contributions
                            </h2>

                            <a href="<?= baseUrl('ngo/contributions.php') ?>"
                               class="text-sm font-bold text-gold hover:text-forest transition-colors">
                                View All
                            </a>
                        </div>

                        <div class="space-y-4">

                            <?php if (!empty($dashboardData['recent_contributions'])): ?>

                                <?php foreach ($dashboardData['recent_contributions'] as $contribution): ?>

                                    <div class="flex flex-wrap items-center justify-between gap-4 p-3 bg-ivory/30 rounded-xl border border-charcoal/5">
                                        <div>
                                            <p class="text-sm font-bold text-charcoal">
                                                <?= $contribution['donor'] ?>
                                            </p>

                                            <p class="text-xs text-charcoal/70">
                                                <?= $contribution['type'] ?>
                                                &bull;
                                                <?= $contribution['date'] ?>
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <span class="inline-block px-3 py-1 bg-forest/10 text-forest text-sm font-bold rounded-lg">
                                                <?= $contribution['amount'] ?>
                                            </span>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <p class="text-sm text-charcoal-light text-center py-8">
                                    No contributions received yet.
                                </p>

                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- ACTIVE CAUSES -->
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full">

                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h2 class="text-xl font-serif font-bold text-forest">
                                Active Causes
                            </h2>

                            <a href="<?= baseUrl('ngo/causes.php') ?>"
                               class="text-sm font-bold text-gold hover:text-forest transition-colors">
                                View All
                            </a>
                        </div>

                        <div class="space-y-6">

                            <?php if (!empty($dashboardData['active_causes_list'])): ?>

                                <?php foreach ($dashboardData['active_causes_list'] as $cause): ?>

                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-4 mb-1">
                                            <h3 class="text-sm font-bold text-charcoal">
                                                <?= $cause['name'] ?>
                                            </h3>

                                            <span class="text-xs font-bold text-forest">
                                                <?= $cause['progress'] ?>%
                                            </span>
                                        </div>

                                        <p class="text-xs text-charcoal-light mb-2">
                                            <?= $cause['raised'] ?> / <?= $cause['goal'] ?>
                                        </p>

                                        <div class="w-full bg-ivory rounded-full h-2 overflow-hidden border border-charcoal/5">
                                            <div class="bg-gold h-2 rounded-full"
                                                 style="width: <?= $cause['progress'] ?>%">
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <p class="text-sm text-charcoal-light text-center py-8">
                                    No active causes available.
                                </p>

                            <?php endif; ?>

                        </div>

                        <div class="mt-8">
                            <a href="<?= baseUrl('ngo/create-cause.php') ?>"
                               class="block w-full py-2.5 px-4 text-center border border-dashed border-gold text-gold font-bold text-sm rounded-xl hover:bg-gold/5 transition-colors">
                                + Create New Cause
                            </a>
                        </div>

                    </div>

                </div>

                <!-- UPCOMING EVENTS -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20 w-full">

                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-serif font-bold text-forest">
                            Upcoming Events
                        </h2>

                        <a href="<?= baseUrl('ngo/events/index.php') ?>"
                           class="text-sm font-bold text-gold hover:text-forest transition-colors">
                            View All Events
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <?php if (!empty($upcoming_events)): ?>

                            <?php foreach ($upcoming_events as $event): ?>

                                <div class="bg-ivory/30 border border-charcoal/10 rounded-2xl p-4 flex flex-col justify-between">

                                    <div>
                                        <span class="inline-block px-2.5 py-1 bg-forest/10 text-forest text-xs font-bold uppercase tracking-wider rounded-md mb-3">
                                            <?= $event['type'] ?? 'Event' ?>
                                        </span>

                                        <h3 class="text-lg font-serif font-bold text-forest mb-2 leading-snug">
                                            <?= $event['title'] ?>
                                        </h3>

                                        <div class="flex items-center text-xs text-charcoal-light mb-4">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>

                                            <?= date('d M Y', strtotime($event['event_date'])) ?>
                                        </div>
                                    </div>

                                    <a href="<?= baseUrl('ngo/events/details.php?id=' . $event['id']) ?>"
                                       class="text-sm font-bold text-gold hover:text-forest transition-colors mt-2 block">
                                        View Details &rarr;
                                    </a>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-sm text-charcoal-light text-center py-8 col-span-full">
                                No upcoming events available.
                            </p>

                        <?php endif; ?>

                    </div>
                </div>

            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="space-y-6 lg:space-y-8">

                <!-- VERIFICATION STATUS -->
                <div class="bg-white rounded-3xl p-4 md:p-8 shadow-sm border border-gold/20">

                    <h3 class="text-xl font-serif font-bold text-forest mb-4">
                        NGO Verification
                    </h3>

                    <div class="flex items-start gap-4 mb-4">

                        <div class="w-12 h-12 rounded-full bg-forest/10 text-forest flex items-center justify-center flex-shrink-0">
                            <?php if ($is_verified): ?>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            <?php else: ?>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z">
                                    </path>
                                </svg>
                            <?php endif; ?>
                        </div>

                        <div>
                            <h4 class="text-base font-bold text-charcoal">
                                <?= $is_verified ? 'Verified' : ucfirst($verification_status) ?>
                            </h4>

                            <p class="text-sm text-charcoal-light leading-relaxed">
                                <?php if ($is_verified): ?>
                                    Your organization has been verified by KarmaSetu.
                                <?php else: ?>
                                    Your organization verification is not yet approved.
                                <?php endif; ?>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- PROFILE COMPLETION -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">

                    <h3 class="text-lg font-serif font-bold text-forest mb-4">
                        Profile Completion
                    </h3>

                    <div class="flex items-end justify-between mb-2">
                        <span class="text-sm font-bold text-charcoal">
                            Overall Status
                        </span>

                        <span class="text-xl font-bold text-forest">85%</span>
                    </div>

                    <div class="w-full bg-ivory rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-forest h-2.5 rounded-full" style="width: 85%"></div>
                    </div>

                    <div class="space-y-3">

                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-charcoal-light">Organization information</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-charcoal-light">Contact information</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 text-forest mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-charcoal-light">Address & Documents</span>
                        </div>

                        <div class="flex items-center text-sm opacity-60">
                            <div class="w-5 h-5 rounded-full border-2 border-charcoal/30 mr-2 flex-shrink-0"></div>
                            <span class="text-charcoal-light">Impact information</span>
                        </div>

                    </div>

                    <a href="<?= baseUrl('ngo/profile.php') ?>"
                       class="block mt-6 text-center text-sm font-bold text-gold hover:text-forest transition-colors">
                        Complete Profile &rarr;
                    </a>

                </div>

                <!-- QUICK ACTIONS -->
                <div class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-gold/20">

                    <h3 class="text-lg font-serif font-bold text-forest mb-4">
                        Quick Actions
                    </h3>

                    <div class="space-y-3">

                        <a href="<?= baseUrl('ngo/profile.php') ?>"
                           class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">

                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-gold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                            </div>

                            <span class="text-sm font-bold text-charcoal">Manage Profile</span>
                        </a>

                        <a href="<?= baseUrl('ngo/create-cause.php') ?>"
                           class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">

                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-gold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v12m6-6H6">
                                    </path>
                                </svg>
                            </div>

                            <span class="text-sm font-bold text-charcoal">Create Cause</span>
                        </a>

                        <a href="<?= baseUrl('ngo/events/create.php') ?>"
                           class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">

                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-gold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>

                            <span class="text-sm font-bold text-charcoal">Create Event</span>
                        </a>

                        <a href="<?= baseUrl('ngo/contributions.php') ?>"
                           class="flex items-center p-3 rounded-xl hover:bg-ivory transition-colors group">

                            <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center mr-3 group-hover:bg-forest group-hover:text-gold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>

                            <span class="text-sm font-bold text-charcoal">View Contributions</span>
                        </a>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layouts/ngo.php';
?>
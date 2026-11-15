<?php
require_once __DIR__ . '/../../includes/functions.php';
requireNgo();
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

if (!$event) {
    header("Location: " . baseUrl('ngo/events/index.php'));
    exit;
}

// ACTION FLASH MESSAGE
$actionMsg = '';
$actionMsgType = '';

if (isset($_SESSION['vol_action_message'])) {
    $actionMsg = $_SESSION['vol_action_message'];
    $actionMsgType = $_SESSION['vol_action_type'];

    unset($_SESSION['vol_action_message']);
    unset($_SESSION['vol_action_type']);
}

// COMPLETE VOLUNTEER ACTIVITY
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['complete_volunteer_id'])) {
    $volId = (int)$_POST['complete_volunteer_id'];
    $hoursInput = isset($_POST['hours_completed']) ? (float)$_POST['hours_completed'] : 4.0;
    if ($hoursInput <= 0) {
        $hoursInput = 4.0;
    }

    // Verify activity belongs to this event and this NGO
    $chkStmt = mysqli_prepare($con, "SELECT va.*, u.name AS volunteer_name FROM volunteer_activities va INNER JOIN users u ON u.id = va.user_id WHERE va.id = ? AND va.ngo_id = ? AND va.event_id = ? AND va.status = 'registered' LIMIT 1");
    mysqli_stmt_bind_param($chkStmt, "iii", $volId, $ngo_id, $eventId);
    mysqli_stmt_execute($chkStmt);
    $chkRes = mysqli_stmt_get_result($chkStmt);

    if ($chkRes && mysqli_num_rows($chkRes) > 0) {
        $volData = mysqli_fetch_assoc($chkRes);
        $volUserId = (int)$volData['user_id'];
        $karmaPoints = 120; // 120 Karma points for volunteer activity

        mysqli_begin_transaction($con);

        // 1. Update activity status
        $upStmt = mysqli_prepare($con, "UPDATE volunteer_activities SET status = 'completed', hours_completed = ?, karma_points_awarded = ?, updated_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($upStmt, "dii", $hoursInput, $karmaPoints, $volId);
        $upOk = mysqli_stmt_execute($upStmt);

        // 2. Prevent duplicate in karma_transactions
        $dupStmt = mysqli_prepare($con, "SELECT id FROM karma_transactions WHERE user_id = ? AND source_type = 'volunteering' AND source_id = ? LIMIT 1");
        mysqli_stmt_bind_param($dupStmt, "ii", $volUserId, $volId);
        mysqli_stmt_execute($dupStmt);
        $dupRes = mysqli_stmt_get_result($dupStmt);

        if ($dupRes && mysqli_num_rows($dupRes) === 0) {
            $desc = "Karma points for volunteering: " . $volData['title'];
            $insKarma = mysqli_prepare($con, "INSERT INTO karma_transactions (user_id, points, source_type, source_id, description, created_at) VALUES (?, ?, 'volunteering', ?, ?, NOW())");
            mysqli_stmt_bind_param($insKarma, "iiis", $volUserId, $karmaPoints, $volId, $desc);
            mysqli_stmt_execute($insKarma);

            // 3. Update user_profiles karma_points
            $upProf = mysqli_prepare($con, "INSERT INTO user_profiles (user_id, karma_points, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE karma_points = karma_points + ?, updated_at = NOW()");
            mysqli_stmt_bind_param($upProf, "iii", $volUserId, $karmaPoints, $karmaPoints);
            mysqli_stmt_execute($upProf);
        }

        if ($upOk) {
            mysqli_commit($con);
            $_SESSION['vol_action_message'] = "Volunteer activity marked as completed! $karmaPoints Karma points awarded to " . $volData['volunteer_name'] . ".";
            $_SESSION['vol_action_type'] = 'success';
        } else {
            mysqli_rollback($con);
            $_SESSION['vol_action_message'] = "Failed to update volunteer activity. Please try again.";
            $_SESSION['vol_action_type'] = 'error';
        }
    } else {
        $_SESSION['vol_action_message'] = "Volunteer activity record not found or already completed.";
        $_SESSION['vol_action_type'] = 'error';
    }

    header("Location: " . baseUrl('ngo/events/details.php?id=' . $eventId));
    exit;
}

// VERIFY ATTENDANCE FOR EVENT BOOKING
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['verify_attendance_booking_id'])) {
    $bookingId = (int)$_POST['verify_attendance_booking_id'];

    // Verify booking belongs to this event
    $chkStmt = mysqli_prepare($con, "SELECT eb.*, u.name AS attendee_name FROM event_bookings eb INNER JOIN users u ON u.id = eb.user_id WHERE eb.id = ? AND eb.event_id = ? AND eb.booking_status = 'confirmed' LIMIT 1");
    if ($chkStmt) {
        mysqli_stmt_bind_param($chkStmt, "ii", $bookingId, $eventId);
        mysqli_stmt_execute($chkStmt);
        $chkRes = mysqli_stmt_get_result($chkStmt);

        if ($chkRes && mysqli_num_rows($chkRes) > 0) {
            $bData = mysqli_fetch_assoc($chkRes);
            $attendeeUserId = (int)$bData['user_id'];
            $eventTitle = $event['title'] ?? 'Event';

            mysqli_begin_transaction($con);

            // Update booking_status to 'attended'
            $upStmt = mysqli_prepare($con, "UPDATE event_bookings SET booking_status = 'attended', updated_at = NOW() WHERE id = ?");
            mysqli_stmt_bind_param($upStmt, "i", $bookingId);
            $upOk = mysqli_stmt_execute($upStmt);
            mysqli_stmt_close($upStmt);

            // Award 50 Karma points
            $pts = 50;
            $tx = awardKarmaPoints($attendeeUserId, $pts, 'event_attendance', $bookingId, "Karma points for attending event: " . $eventTitle);

            if ($upOk && $tx) {
                mysqli_commit($con);
                $_SESSION['vol_action_message'] = "Attendance verified for " . $bData['attendee_name'] . "! Awarded 50 Karma points.";
                $_SESSION['vol_action_type'] = 'success';
            } else {
                mysqli_rollback($con);
                $_SESSION['vol_action_message'] = "Failed to verify attendance. Please try again.";
                $_SESSION['vol_action_type'] = 'error';
            }
        } else {
            $_SESSION['vol_action_message'] = "Booking record not found or already verified.";
            $_SESSION['vol_action_type'] = 'error';
        }
        mysqli_stmt_close($chkStmt);
    }

    header("Location: " . baseUrl('ngo/events/details.php?id=' . $eventId));
    exit;
}

// FETCH EVENT VOLUNTEERS
$volunteers = [];
$vListStmt = mysqli_prepare($con, "SELECT va.*, u.name AS volunteer_name, u.email AS volunteer_email, u.phone AS volunteer_phone FROM volunteer_activities va INNER JOIN users u ON u.id = va.user_id WHERE va.event_id = ? AND va.ngo_id = ? ORDER BY va.created_at DESC");
mysqli_stmt_bind_param($vListStmt, "ii", $eventId, $ngo_id);
mysqli_stmt_execute($vListStmt);
$vListRes = mysqli_stmt_get_result($vListStmt);
if ($vListRes) {
    while ($vRow = mysqli_fetch_assoc($vListRes)) {
        $volunteers[] = $vRow;
    }
}

// FETCH TICKET BOOKINGS
$bookings = [];
$bkStmt = mysqli_prepare($con, "SELECT eb.*, u.name AS attendee_name, u.email AS attendee_email FROM event_bookings eb INNER JOIN users u ON u.id = eb.user_id WHERE eb.event_id = ? ORDER BY eb.created_at DESC LIMIT 10");
mysqli_stmt_bind_param($bkStmt, "i", $eventId);
mysqli_stmt_execute($bkStmt);
$bkRes = mysqli_stmt_get_result($bkStmt);
if ($bkRes) {
    while ($bRow = mysqli_fetch_assoc($bkRes)) {
        $bookings[] = $bRow;
    }
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


                    <!-- VOLUNTEER REGISTRATIONS & MANAGEMENT -->
                    <div class="bg-white rounded-3xl border border-gold/20 shadow-sm p-6 sm:p-8">

                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-serif font-bold text-forest">
                                    Registered Volunteers
                                </h2>
                                <p class="text-xs text-charcoal-light mt-1">
                                    Manage volunteer participation and award Karma points upon activity completion.
                                </p>
                            </div>

                            <span class="px-3 py-1 bg-forest/10 text-forest text-xs font-bold rounded-full">
                                <?= count($volunteers) ?> <?= count($volunteers) === 1 ? 'Volunteer' : 'Volunteers' ?>
                            </span>
                        </div>

                        <?php if ($actionMsg !== ''): ?>
                            <div class="mb-6 p-4 rounded-xl <?= $actionMsgType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                                <p class="text-sm font-medium"><?= e($actionMsg) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($volunteers)): ?>
                            <div class="space-y-4">
                                <?php foreach ($volunteers as $v): ?>
                                    <div class="border border-charcoal/10 rounded-2xl p-4 sm:p-5 bg-ivory/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="font-bold text-charcoal text-base truncate">
                                                    <?= e($v['volunteer_name']) ?>
                                                </h4>
                                                <?php if ($v['status'] === 'registered'): ?>
                                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase rounded-md">
                                                        Registered
                                                    </span>
                                                <?php elseif ($v['status'] === 'completed'): ?>
                                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 text-[10px] font-bold uppercase rounded-md">
                                                        Completed (+<?= (int)$v['karma_points_awarded'] ?> Karma)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 text-[10px] font-bold uppercase rounded-md">
                                                        Cancelled
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-xs text-charcoal-light space-y-0.5">
                                                <p><span class="font-medium text-charcoal">Role:</span> <?= e($v['volunteer_role']) ?></p>
                                                <p><span class="font-medium text-charcoal">Email:</span> <?= e($v['volunteer_email']) ?> <?= !empty($v['volunteer_phone']) ? ' | ' . e($v['volunteer_phone']) : '' ?></p>
                                                <?php if ($v['status'] === 'completed'): ?>
                                                    <p><span class="font-medium text-forest">Hours Completed:</span> <?= (float)$v['hours_completed'] ?>h</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if ($v['status'] === 'registered'): ?>
                                            <form method="POST" class="flex flex-wrap items-center gap-2 shrink-0">
                                                <input type="hidden" name="complete_volunteer_id" value="<?= (int)$v['id'] ?>">
                                                <div class="flex items-center gap-1">
                                                    <input type="number" step="0.5" min="0.5" max="24" name="hours_completed" value="4.0" class="w-16 px-2 py-1.5 text-xs bg-white border border-charcoal/20 rounded-lg text-charcoal font-medium" title="Hours Completed">
                                                    <span class="text-xs text-charcoal-light font-medium">hrs</span>
                                                </div>
                                                <button type="submit" class="px-3.5 py-1.5 bg-forest text-white hover:bg-forest-dark text-xs font-bold rounded-lg transition-colors shadow-sm whitespace-nowrap">
                                                    Mark Completed
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-6 bg-ivory/30 rounded-2xl border border-charcoal/5 text-center">
                                <p class="text-sm text-charcoal-light">No volunteers have registered for this event yet.</p>
                            </div>
                        <?php endif; ?>

                        <!-- ATTENDEE BOOKINGS OVERVIEW -->
                        <div class="border-t border-charcoal/10 pt-6 mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-base font-serif font-bold text-forest">
                                    Ticket Bookings (<?= count($bookings) ?>)
                                </h3>
                                <span class="text-xs text-charcoal-light">Recent confirmed bookings</span>
                            </div>

                            <?php if (!empty($bookings)): ?>
                                <div class="divide-y divide-charcoal/5">
                                    <?php foreach ($bookings as $b): ?>
                                        <div class="py-2.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                                            <div>
                                                <span class="font-bold text-charcoal"><?= e($b['attendee_name']) ?></span>
                                                <span class="text-charcoal-light ml-1">(<?= e($b['booking_reference']) ?>)</span>
                                                <?php if ($b['booking_status'] === 'attended'): ?>
                                                    <span class="ml-2 px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 text-[10px] font-bold uppercase rounded-md">Attended (+50)</span>
                                                <?php elseif ($b['booking_status'] === 'cancelled'): ?>
                                                    <span class="ml-2 px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 text-[10px] font-bold uppercase rounded-md">Cancelled</span>
                                                <?php else: ?>
                                                    <span class="ml-2 px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase rounded-md">Confirmed</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <span class="font-medium text-forest"><?= (int)$b['quantity'] ?> tickets</span>
                                                    <span class="text-charcoal-light ml-1 font-mono">₹<?= number_format((float)$b['total_amount'], 2) ?></span>
                                                </div>
                                                <?php if ($b['booking_status'] === 'confirmed'): ?>
                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="verify_attendance_booking_id" value="<?= (int)$b['id'] ?>">
                                                        <button type="submit" class="px-2.5 py-1 bg-forest text-white hover:bg-forest-dark text-[11px] font-bold rounded-md transition-colors shadow-sm whitespace-nowrap">
                                                            Mark Attended
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-charcoal-light">No ticket bookings recorded yet.</p>
                            <?php endif; ?>
                        </div>

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
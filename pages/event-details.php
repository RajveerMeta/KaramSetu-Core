
<?php
require_once __DIR__ . '/../Database/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$event = null;
$bookingMessage = '';
$bookingMessageType = '';

// GET EVENT DETAILS

if ($id > 0) {
    $sql = "SELECT events.*,
               ngos.name AS ngo_name,
               ngos.description AS ngo_description,
               ngos.primary_cause AS ngo_primary_cause
        FROM events
        INNER JOIN ngos ON events.ngo_id = ngos.id
        WHERE events.id = $id
        LIMIT 1";

    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
    }
}

if (!$event) {
    echo "<div class='py-20 text-center'><h2 class='text-2xl md:text-4xl font-bold'>Event not found</h2></div>";
    return;
}

// GET BOOKING MESSAGE

if (isset($_SESSION['booking_message'])) {
    $bookingMessage = $_SESSION['booking_message'];
    $bookingMessageType = $_SESSION['booking_message_type'];

    unset($_SESSION['booking_message']);
    unset($_SESSION['booking_message_type']);
}

// PROCESS TICKET BOOKING

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['book_tickets'])) {

    if (!isset($_SESSION['user_id'])) {

        $bookingMessage = "Please login before booking tickets.";
        $bookingMessageType = 'error';

    } else {

        $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity'])
            ? (int) $_POST['quantity']
            : 0;

        $userId = (int) $_SESSION['user_id'];

        if ($quantity < 1 || $quantity > 255) {

            $bookingMessage = "Please select a valid ticket quantity.";
            $bookingMessageType = 'error';

        } else {

            // START TRANSACTION

            mysqli_begin_transaction($con);

            // LOCK EVENT ROW TO CHECK AVAILABLE TICKETS

            $lockQuery = "SELECT max_participants, entry_fee, status
                          FROM events
                          WHERE id = $id
                          FOR UPDATE";

            $lockResult = mysqli_query($con, $lockQuery);

            if ($lockResult && mysqli_num_rows($lockResult) > 0) {

                $lockedEvent = mysqli_fetch_assoc($lockResult);

                $capacity = (int) $lockedEvent['max_participants'];
                $ticketPrice = (float) $lockedEvent['entry_fee'];

                // CHECK EVENT STATUS

                if ($lockedEvent['status'] !== 'upcoming') {

                    mysqli_rollback($con);

                    $bookingMessage = "Tickets are not available for this event.";
                    $bookingMessageType = 'error';

                } else {

                    // GET CURRENT BOOKED TICKETS

                    $checkQuery = "SELECT COALESCE(SUM(quantity), 0) AS booked_tickets
                                   FROM event_bookings
                                   WHERE event_id = $id
                                   AND booking_status IN ('confirmed', 'attended')";

                    $checkResult = mysqli_query($con, $checkQuery);

                    if ($checkResult) {

                        $checkData = mysqli_fetch_assoc($checkResult);
                        $currentBooked = (int) $checkData['booked_tickets'];

                        // CHECK TICKET CAPACITY
                        // Capacity 0 means unlimited.

                        if ($capacity > 0 && ($currentBooked + $quantity) > $capacity) {

                            mysqli_rollback($con);

                            $remaining = max(0, $capacity - $currentBooked);

                            $bookingMessage = "Only $remaining tickets are currently available.";
                            $bookingMessageType = 'error';

                        } else {

                            // CALCULATE BOOKING AMOUNT

                            $totalAmount = $ticketPrice * $quantity;

                            $ticketPriceDB = number_format($ticketPrice, 2, '.', '');
                            $totalAmountDB = number_format($totalAmount, 2, '.', '');

                            // GENERATE BOOKING REFERENCE

                            $bookingReference = 'KS-' . $id . '-' . strtoupper(str_replace('.', '', uniqid('', true)));

                            // SAVE BOOKING

                            $insertQuery = "INSERT INTO event_bookings
                                            (
                                                booking_reference,
                                                user_id,
                                                event_id,
                                                quantity,
                                                ticket_price,
                                                total_amount,
                                                optional_donation,
                                                booking_status,
                                                created_at,
                                                updated_at
                                            )
                                            VALUES
                                            (
                                                '$bookingReference',
                                                $userId,
                                                $id,
                                                $quantity,
                                                $ticketPriceDB,
                                                $totalAmountDB,
                                                0.00,
                                                'confirmed',
                                                NOW(),
                                                NOW()
                                            )";

                            $insertResult = mysqli_query($con, $insertQuery);

                            if ($insertResult) {

                                if (mysqli_commit($con)) {

                                    $_SESSION['booking_message'] =
                                        "Your booking is confirmed! Booking Reference: $bookingReference. This is a demo booking; no payment was collected.";

                                    $_SESSION['booking_message_type'] = 'success';

                                    // REDIRECT TO PREVENT DUPLICATE BOOKING

                                    header("Location: " . $_SERVER['REQUEST_URI']);
                                    exit;

                                } else {

                                    mysqli_rollback($con);

                                    $bookingMessage = "Unable to complete your booking. Please try again.";
                                    $bookingMessageType = 'error';
                                }

                            } else {

                                mysqli_rollback($con);

                                $bookingMessage = "Unable to save your booking. Please try again.";
                                $bookingMessageType = 'error';
                            }
                        }

                    } else {

                        mysqli_rollback($con);

                        $bookingMessage = "Unable to check ticket availability.";
                        $bookingMessageType = 'error';
                    }
                }

            } else {

                mysqli_rollback($con);

                $bookingMessage = "Event details could not be verified.";
                $bookingMessageType = 'error';
            }
        }
    }
}

// GET VOLUNTEER MESSAGE

$volunteerMessage = '';
$volunteerMessageType = '';

if (isset($_SESSION['volunteer_message'])) {
    $volunteerMessage = $_SESSION['volunteer_message'];
    $volunteerMessageType = $_SESSION['volunteer_message_type'];

    unset($_SESSION['volunteer_message']);
    unset($_SESSION['volunteer_message_type']);
}

// PROCESS VOLUNTEER REGISTRATION

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['volunteer_register'])) {

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['volunteer_message'] = "Please login before registering as a volunteer.";
        $_SESSION['volunteer_message_type'] = 'error';
    } else {
        $userId = (int) $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? 'user';

        if ($userRole !== 'user') {
            $_SESSION['volunteer_message'] = "Only user accounts can register as volunteers.";
            $_SESSION['volunteer_message_type'] = 'error';
        } elseif ($event['status'] !== 'upcoming') {
            $_SESSION['volunteer_message'] = "Volunteering is only available for upcoming events.";
            $_SESSION['volunteer_message_type'] = 'error';
        } else {
            // Check duplicate registration
            $dupStmt = mysqli_prepare($con, "SELECT id FROM volunteer_activities WHERE user_id = ? AND event_id = ? AND status != 'cancelled' LIMIT 1");
            mysqli_stmt_bind_param($dupStmt, "ii", $userId, $id);
            mysqli_stmt_execute($dupStmt);
            $dupRes = mysqli_stmt_get_result($dupStmt);

            if ($dupRes && mysqli_num_rows($dupRes) > 0) {
                $_SESSION['volunteer_message'] = "You are already registered as a volunteer for this event.";
                $_SESSION['volunteer_message_type'] = 'error';
            } else {
                $role = isset($_POST['volunteer_role']) ? trim($_POST['volunteer_role']) : 'Event Volunteer';
                if ($role === '') {
                    $role = 'Event Volunteer';
                }

                $ngoId = (int) $event['ngo_id'];
                $eventTitle = $event['title'];
                $eventDateStr = $event['event_date'];
                $startTime = !empty($event['start_time']) ? $event['start_time'] : null;
                $endTime = !empty($event['end_time']) ? $event['end_time'] : null;
                $location = $event['location'];

                $insStmt = mysqli_prepare($con, "INSERT INTO volunteer_activities (user_id, ngo_id, event_id, title, volunteer_role, activity_date, start_time, end_time, location, hours_completed, status, karma_points_awarded, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0.0, 'registered', 0, NOW(), NOW())");
                mysqli_stmt_bind_param($insStmt, "iiissssss", $userId, $ngoId, $id, $eventTitle, $role, $eventDateStr, $startTime, $endTime, $location);

                if (mysqli_stmt_execute($insStmt)) {
                    $_SESSION['volunteer_message'] = "You have successfully registered as a volunteer for this event!";
                    $_SESSION['volunteer_message_type'] = 'success';
                } else {
                    $_SESSION['volunteer_message'] = "Unable to process your registration. Please try again.";
                    $_SESSION['volunteer_message_type'] = 'error';
                }
            }
        }
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// CHECK IF LOGGED-IN USER IS ALREADY REGISTERED AS VOLUNTEER

$userVolRecord = null;
if (isset($_SESSION['user_id'])) {
    $currentUserId = (int) $_SESSION['user_id'];
    $chkStmt = mysqli_prepare($con, "SELECT * FROM volunteer_activities WHERE user_id = ? AND event_id = ? AND status != 'cancelled' LIMIT 1");
    mysqli_stmt_bind_param($chkStmt, "ii", $currentUserId, $id);
    mysqli_stmt_execute($chkStmt);
    $chkRes = mysqli_stmt_get_result($chkStmt);
    if ($chkRes && mysqli_num_rows($chkRes) > 0) {
        $userVolRecord = mysqli_fetch_assoc($chkRes);
    }
}

// GET BOOKED TICKETS

$bookingQuery = "SELECT
                    COALESCE(SUM(quantity), 0) AS booked_tickets,
                    COUNT(*) AS total_bookings
                 FROM event_bookings
                 WHERE event_id = $id
                 AND booking_status IN ('confirmed', 'attended')";

$bookingResult = mysqli_query($con, $bookingQuery);

$bookingData = mysqli_fetch_assoc($bookingResult);

$bookedTickets = (int) ($bookingData['booked_tickets'] ?? 0);
$totalBookings = (int) ($bookingData['total_bookings'] ?? 0);

// CALCULATE TICKET AVAILABILITY

$totalTickets = (int) $event['max_participants'];

$availableTickets = $totalTickets > 0
    ? max(0, $totalTickets - $bookedTickets)
    : 0;

$soldPercentage = $totalTickets > 0
    ? min(100, round(($bookedTickets / $totalTickets) * 100))
    : 0;

// EVENT DETAILS

$price = (float) $event['entry_fee'];

$eventStatus = $event['status'] === 'upcoming' ? 'Open' : ucfirst($event['status']);

$eventDate = date('l, d F Y', strtotime($event['event_date']));
$eventTime = date('h:i A', strtotime($event['start_time']));
$eventEndTime = date('h:i A', strtotime($event['end_time']));
?>


<section class="bg-ivory pb-12 pt-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="relative w-full aspect-[16/9] md:aspect-[16/7] bg-forest/5 rounded-3xl overflow-hidden shadow-sm mb-10 group">
            <img src="<?= e($event['event_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>

            <div class="absolute bottom-6 left-6 md:bottom-8 md:left-8 z-10 flex flex-col md:flex-row flex-wrap gap-2">
                <span class="bg-gold text-forest font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md">
                    <?= e($event['category']) ?>
                </span>

                <span class="bg-white text-forest font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md border border-gold/20">
                    <?= $price == 0 ? 'FREE' : '₹' . number_format($price) ?>
                </span>

                <?php if ($eventStatus !== 'Open'): ?>
                    <span class="<?= $event['status'] === 'cancelled' ? 'bg-red-500' : 'bg-amber-500' ?> text-white font-bold tracking-widest uppercase text-xs px-4 py-2 rounded-full shadow-md">
                        <?= e($eventStatus) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="max-w-4xl mb-10">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-forest mb-6 leading-tight text-balance"><?= e($event['title']) ?></h1>

            <p class="text-xl md:text-2xl text-charcoal-light font-light leading-relaxed border-l-4 border-gold pl-6 py-1 break-words">
                <?= e($event['short_description']) ?>
            </p>
        </div>

        <div class="flex flex-col md:flex-row flex-wrap items-center gap-y-6 gap-x-12 pb-8 border-b border-gold/20">

            <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Organizer</span>
                    <span class="block text-base font-medium text-forest"><?= e($event['ngo_name']) ?></span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Date</span>
                    <span class="block text-base font-medium text-forest"><?= e($eventDate) ?></span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-start gap-4 min-w-[150px]">
                <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Time</span>
                    <span class="block text-base font-medium text-forest"><?= e($eventTime) ?></span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-start gap-4 min-w-[200px]">
                <div class="w-12 h-12 rounded-full bg-white border border-gold/30 flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm text-gold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-charcoal-light uppercase tracking-wider mb-1">Location</span>
                    <span class="block text-base font-medium text-forest break-words"><?= e($event['location']) ?></span>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="py-12 bg-ivory relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-12 items-start">

            <div class="w-full min-w-0 space-y-12">

                <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm overflow-hidden">
                    <h2 class="text-3xl md:text-4xl font-serif text-forest mb-6">About This Event</h2>

                    <div class="prose prose-lg text-charcoal-light font-light max-w-none space-y-6 leading-relaxed break-words [overflow-wrap:anywhere]">
                        <?= nl2br(e($event['detailed_description'])) ?>
                    </div>
                </div>

                <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm">
                    <h3 class="text-2xl md:text-4xl font-serif text-forest mb-8">Event Details</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-12">
                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Date & Time</h4>
                            <p class="text-base font-medium text-charcoal"><?= e($eventDate) ?></p>
                            <p class="text-sm text-charcoal-light mt-1"><?= e($eventTime) ?> - <?= e($eventEndTime) ?></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Venue</h4>
                            <p class="text-base font-medium text-charcoal">Main Grounds</p>
                            <p class="text-sm text-charcoal-light mt-1"><?= e($event['location']) ?></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Organizer</h4>
                            <p class="text-base font-medium text-charcoal"><?= e($event['ngo_name']) ?></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Audience</h4>
                            <p class="text-base font-medium text-charcoal">Open for All</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Category</h4>
                            <p class="text-base font-medium text-charcoal"><?= e($event['category']) ?></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider mb-2">Event Type</h4>
                            <p class="text-base font-medium text-charcoal"><?= $price == 0 ? 'Free Entry' : 'Paid Entry' ?></p>
                        </div>
                    </div>
                </div>

                <!-- TICKET SALES PROGRESS -->

                <div class="bg-white p-8 md:p-10 rounded-3xl border border-gold/20 shadow-sm">
                    <h3 class="text-2xl md:text-4xl font-serif text-forest mb-2">Ticket Sales Progress</h3>
                    <p class="text-sm text-charcoal-light mb-8">See how many tickets have been booked for this event.</p>

                    <div class="flex flex-col md:flex-row justify-between items-end mb-4">
                        <div>
                            <span class="text-4xl font-serif text-forest block"><?= number_format($bookedTickets) ?></span>
                            <span class="text-sm font-bold text-gold uppercase tracking-wider">Tickets Sold</span>
                        </div>

                        <div class="text-center">
                            <span class="text-3xl md:text-4xl font-serif text-forest block"><?= $soldPercentage ?>%</span>
                            <span class="text-sm font-bold text-gold uppercase tracking-wider">Sold</span>
                        </div>

                        <div class="text-right">
                            <span class="text-2xl md:text-4xl font-serif text-charcoal-light block">
                                <?= $totalTickets > 0 ? number_format($totalTickets) : 'Unlimited' ?>
                            </span>
                            <span class="text-sm font-bold text-charcoal-light uppercase tracking-wider">Total Tickets</span>
                        </div>
                    </div>

                    <div class="w-full bg-ivory rounded-full h-5 mb-5 border border-gold/20 overflow-hidden shadow-inner">
                        <div class="bg-forest h-full rounded-full relative" style="width: <?= $soldPercentage ?>%">
                            <div class="absolute inset-0 bg-white/20 w-full h-full" style="background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent); background-size: 1rem 1rem;"></div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between text-sm font-medium text-charcoal-light">
                        <span>
                            <?= $totalTickets > 0 ? number_format($availableTickets) . ' Tickets Remaining' : 'No Ticket Limit' ?>
                        </span>
                        <span><?= number_format($totalBookings) ?> Bookings</span>
                    </div>
                </div>
            </div>

            <div class="w-full min-w-0 space-y-8">

                
<div id="ticketBookingCard" data-ticket-price="<?= e($price) ?>" class="bg-white p-6 md:p-8 rounded-3xl border border-gold/30 shadow-md">

    <h3 class="text-2xl md:text-4xl font-serif text-forest mb-6">Book Tickets</h3>

    <?php if ($bookingMessage !== ''): ?>
        <div class="mb-6 p-4 rounded-xl <?= $bookingMessageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
            <p class="text-sm font-medium"><?= e($bookingMessage) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" id="ticketBookingForm">

        <input type="hidden" name="quantity" id="bookingQuantity" value="1">
        <input type="hidden" name="book_tickets" value="1">

        <div class="space-y-6">

            <div class="flex flex-col md:flex-row justify-between items-center pb-6 border-b border-gold/10">
                <span class="text-sm font-bold text-charcoal uppercase tracking-wider">Ticket Price</span>

                <div class="text-right">
                    <span class="text-2xl md:text-4xl font-serif text-forest">
                        <?= $price == 0 ? 'Free' : '₹' . number_format($price) ?>
                    </span>
                    <span class="text-xs text-charcoal-light block">/ person</span>
                </div>
            </div>

            <?php if ($price > 0): ?>

                <div class="pb-6 border-b border-gold/10">

                    <label class="block text-sm font-bold text-charcoal uppercase tracking-wider mb-4">Quantity</label>

                    <div class="flex flex-col md:flex-row items-center justify-between border border-gold/20 rounded-xl p-2 bg-ivory">

                        <button type="button" id="donationMinusBtn" class="w-12 h-12 flex flex-col md:flex-row items-center justify-center rounded-lg bg-white border border-gold/20 text-forest hover:bg-forest hover:text-ivory transition-colors shadow-sm">

                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>

                        </button>

                        <span id="donationCountDisplay" class="text-2xl md:text-4xl font-serif text-forest w-16 text-center">1</span>

                        <button type="button" id="donationPlusBtn" class="w-12 h-12 flex flex-col md:flex-row items-center justify-center rounded-lg bg-white border border-gold/20 text-forest hover:bg-forest hover:text-ivory transition-colors shadow-sm">

                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>

                        </button>

                    </div>

                </div>

                <div class="flex flex-col md:flex-row justify-between items-center pb-6">

                    <span class="text-sm font-bold text-charcoal uppercase tracking-wider">Total Amount</span>

                    <span id="totalAmountDisplay" class="text-3xl md:text-4xl font-serif text-forest">
                        ₹<?= number_format($price) ?>
                    </span>

                </div>

            <?php endif; ?>

            <button type="submit" name="book_tickets" value="1"
                <?= $event['status'] !== 'upcoming' || ($totalTickets > 0 && $availableTickets <= 0) ? 'disabled' : '' ?>
                class="w-full bg-forest text-ivory hover:bg-forest/90 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-4 rounded-xl font-bold transition-all shadow-md text-lg flex flex-col md:flex-row items-center justify-center gap-2 group">

                <?= $event['status'] !== 'upcoming' ? 'Booking Closed' : (($totalTickets > 0 && $availableTickets <= 0) ? 'Sold Out' : ($price > 0 ? 'Book Now' : 'Register for Free')) ?>

                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>

            </button>

            <div class="flex flex-col md:flex-row items-start gap-2 mt-4 text-charcoal-light">

                <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>

                <p class="text-xs leading-relaxed">
                    Demo booking mode: your booking is confirmed immediately. No actual payment is collected.
                </p>

            </div>

        </div>

    </form>

</div>

<!-- VOLUNTEER CARD -->
<div id="volunteerCard" class="bg-white p-6 md:p-8 rounded-3xl border border-gold/30 shadow-md">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-2xl font-serif text-forest">Volunteer for Event</h3>
        <span class="inline-block bg-gold/20 text-forest text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            +120 Karma
        </span>
    </div>

    <p class="text-sm text-charcoal-light mb-6">Contribute your time and help make this event a success. Earn 120 Karma points upon completed participation.</p>

    <?php if ($volunteerMessage !== ''): ?>
        <div class="mb-6 p-4 rounded-xl <?= $volunteerMessageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
            <p class="text-sm font-medium"><?= e($volunteerMessage) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="p-4 bg-ivory rounded-2xl border border-gold/20 text-center">
            <p class="text-sm text-charcoal-light mb-4">Sign in with your user account to volunteer for this event.</p>
            <a href="<?= baseUrl('auth/login.php') ?>" class="inline-block w-full bg-forest text-ivory hover:bg-forest/90 font-bold py-3 px-6 rounded-xl transition-all shadow-md text-sm text-center">
                Login to Volunteer
            </a>
        </div>
    <?php elseif (($_SESSION['user_role'] ?? 'user') !== 'user'): ?>
        <div class="p-4 bg-ivory rounded-2xl border border-gold/20 text-center">
            <p class="text-xs text-charcoal-light">You are logged in as <?= e($_SESSION['user_role'] ?? '') ?>. Only donor/volunteer accounts can sign up for volunteer activities.</p>
        </div>
    <?php elseif ($userVolRecord !== null): ?>
        <div class="p-5 bg-green-50/80 rounded-2xl border border-green-200">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="font-bold text-forest text-sm">You are Registered!</h4>
            </div>
            <p class="text-xs text-charcoal-light mb-4">You are registered as a volunteer for this event.</p>
            <div class="text-xs space-y-1.5 mb-4 text-charcoal">
                <div class="flex justify-between"><span class="text-charcoal-light">Role:</span> <span class="font-bold"><?= e($userVolRecord['volunteer_role']) ?></span></div>
                <div class="flex justify-between"><span class="text-charcoal-light">Status:</span> <span class="font-bold uppercase tracking-wider text-forest"><?= e(ucfirst($userVolRecord['status'])) ?></span></div>
                <?php if ($userVolRecord['status'] === 'completed'): ?>
                    <div class="flex justify-between"><span class="text-charcoal-light">Karma Awarded:</span> <span class="font-bold text-forest">+<?= (int)$userVolRecord['karma_points_awarded'] ?> Points</span></div>
                <?php endif; ?>
            </div>
            <a href="<?= baseUrl('user/volunteer-activities.php') ?>" class="block text-center w-full py-2.5 px-4 bg-white border border-forest/20 text-forest hover:bg-forest hover:text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                View Your Activities
            </a>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="volunteer_register" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Volunteer Role</label>
                    <select name="volunteer_role" class="w-full px-4 py-3 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-charcoal text-sm font-medium">
                        <option value="Event Volunteer">Event Volunteer (General Support)</option>
                        <option value="Logistics & Setup">Logistics & Setup</option>
                        <option value="Registration Desk">Registration Desk</option>
                        <option value="Guest Assistance">Guest Assistance</option>
                        <option value="Refreshments & Distribution">Refreshments & Distribution</option>
                    </select>
                </div>

                <div class="text-xs text-charcoal-light space-y-1 p-3 bg-ivory/50 rounded-xl border border-gold/10">
                    <div class="flex items-center gap-1.5">
                        <span>📍</span>
                        <span class="truncate"><?= e($event['location']) ?></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span>📅</span>
                        <span><?= e($eventDate) ?></span>
                    </div>
                </div>

                <button type="submit" name="volunteer_register" value="1"
                    <?= $event['status'] !== 'upcoming' ? 'disabled' : '' ?>
                    class="w-full bg-forest text-ivory hover:bg-forest/90 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3.5 rounded-xl font-bold transition-all shadow-md text-sm flex items-center justify-center gap-2 group">
                    <span><?= $event['status'] !== 'upcoming' ? 'Volunteering Closed' : 'Register as Volunteer' ?></span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
                <p class="text-[11px] text-charcoal-light text-center">⭐ +120 Karma points awarded upon activity completion</p>
            </div>
        </form>
    <?php endif; ?>
</div>


                <div id="donationWrapper" class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-sm">
                    <h3 class="text-xl font-serif text-forest mb-2">Want to Support More?</h3>
                    <p class="text-sm text-charcoal-light mb-6">Add a donation to help <?= e($event['ngo_name']) ?> achieve their mission faster.</p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                        <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="500">₹500</button>
                        <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="1000">₹1,000</button>
                        <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="2000">₹2,000</button>
                        <button class="donation-amount-btn py-2.5 border rounded-xl text-sm font-bold transition-colors" data-amount="5000">₹5,000</button>
                        <button class="donation-custom-btn py-2.5 border rounded-xl text-sm font-bold transition-colors col-span-2 sm:col-span-2">Other</button>
                    </div>

                    <div id="donationCustomInput" style="display:none;" class="mb-4">
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-2">Custom Amount</label>

                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-charcoal-light font-medium">₹</span>
                            <input type="number" min="1" placeholder="Enter amount" class="w-full pl-8 pr-4 py-3 bg-ivory border border-gold/30 rounded-xl focus:ring-2 focus:ring-forest focus:border-transparent outline-none text-forest font-medium transition-all" data-validate="numeric">
                        </div>
                    </div>

                    <a href="<?= baseUrl('?page=contribute&ngo_id=' . (int)$event['ngo_id']) ?>" class="w-full bg-white border-2 border-forest text-forest hover:bg-forest hover:text-ivory px-4 py-3.5 rounded-xl font-bold transition-colors text-sm flex flex-col md:flex-row items-center justify-center gap-2">
                        Donate Now
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>

                <div class="bg-white p-6 md:p-8 rounded-3xl border border-gold/20 shadow-sm">
                    <h3 class="text-xl font-serif text-forest mb-6">About the Organizer</h3>

                    <div class="flex flex-col md:flex-row items-start gap-4 mb-5">
                        <div class="w-16 h-16 bg-ivory border border-gold/30 rounded-2xl flex flex-col md:flex-row items-center justify-center shrink-0 shadow-sm">
                            <span class="text-3xl md:text-4xl font-serif text-forest"><?= e(substr($event['ngo_name'], 0, 1)) ?></span>
                        </div>

                        <div class="min-w-0">
                            <h4 class="font-serif text-forest text-lg mb-1 break-words"><?= e($event['ngo_name']) ?></h4>
                            <span class="inline-block bg-forest/5 text-forest text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-forest/10"><?= e($event['ngo_primary_cause']) ?></span>
                        </div>
                    </div>

                    <p class="text-sm text-charcoal-light mb-6 line-clamp-3 leading-relaxed">
                    <?= e($event['ngo_description']) ?>
                    </p>
                    <a href="<?= baseUrl('?page=ngo-profile&id=' . $event['ngo_id']) ?>" class="w-full flex flex-col md:flex-row items-center justify-center gap-2 bg-white text-forest border border-forest/20 hover:bg-forest hover:text-ivory hover:border-forest px-4 py-3 rounded-xl font-bold transition-all text-sm group">
                        View NGO Profile
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                

            </div>
        </div>
    </div>
</section>

<section class="py-16 md:py-20 bg-white border-t border-gold/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-gold/10 pb-6">
            <div>
                <h2 class="text-3xl md:text-4xl font-serif text-forest mb-2">You May Also Like</h2>
                <p class="text-charcoal-light font-medium text-base">Discover more events similar to this one.</p>
            </div>

            <a href="<?= baseUrl('?page=events') ?>" class="text-sm font-bold text-forest hover:text-gold transition-colors inline-flex items-center group bg-ivory px-5 py-2.5 rounded-full border border-gold/20">
                Browse All Events
                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch">
            <?php
            $eventId = (int) $event['id'];

            $sql = "SELECT events.*, ngos.name AS ngo_name
                    FROM events
                    INNER JOIN ngos ON events.ngo_id = ngos.id
                    WHERE events.status = 'upcoming'
                    AND events.id != $eventId
                    ORDER BY events.created_at DESC
                    LIMIT 3";

            $result = mysqli_query($con, $sql);

            $relatedEvents = [];

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $relatedEvents[] = $row;
                }
            }
            ?>

            <?php foreach ($relatedEvents as $relatedEvent): ?>
                <?php
                $id = $relatedEvent['id'];
                $title = $relatedEvent['title'];
                $category = $relatedEvent['category'];
                $date = $relatedEvent['event_date'];
                $ngoName = $relatedEvent['ngo_name'];
                $location = $relatedEvent['location'];
                $description = $relatedEvent['short_description'];
                $entryFee = (float) $relatedEvent['entry_fee'] == 0 ? 'Free' : '₹' . number_format($relatedEvent['entry_fee']);
                $status = 'Open';
                $image = $relatedEvent['event_image'];
                $featured = false;
                ?>

                <div class="flex">
                    <?php include __DIR__ . "/../includes/components/events/event-card.php"; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

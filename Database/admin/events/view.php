
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$eventId = 0;

if (isset($_GET['id'])) {
    $eventId = (int) $_GET['id'];
}

if ($eventId <= 0) {
    header('Location: ' . baseUrl('admin/events/index.php'));
    exit;
}

/* Flash messages */
$message = '';
$messageType = '';

if (isset($_SESSION['event_message'])) {
    $message = $_SESSION['event_message'];
}

if (isset($_SESSION['event_message_type'])) {
    $messageType = $_SESSION['event_message_type'];
}

unset($_SESSION['event_message'], $_SESSION['event_message_type']);

/* Fetch event and NGO details */
$sql = "SELECT e.*,
               n.id AS ngo_id,
               n.name AS ngo_name,
               n.ngo_type,
               n.city AS ngo_city,
               n.state AS ngo_state,
               n.verification_status
        FROM events e
        INNER JOIN ngos n ON e.ngo_id = n.id
        WHERE e.id = ?
        LIMIT 1";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Unable to load event details.');
}

mysqli_stmt_bind_param($stmt, 'i', $eventId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$event) {
    header('Location: ' . baseUrl('admin/events/index.php'));
    exit;
}

/* Update event status */
if (isset($_POST['update_status'])) {
    $action = '';

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }

    $newStatus = '';

    if ($action === 'cancel') {
        $newStatus = 'cancelled';
    } elseif ($action === 'activate') {
        $newStatus = 'upcoming';
    }

    if ($newStatus === '') {
        $_SESSION['event_message'] = 'Invalid status action.';
        $_SESSION['event_message_type'] = 'error';
    } else {
        $sql = "UPDATE events
                SET status = ?
                WHERE id = ? AND status = ?";

        $expectedStatus = '';

        if ($action === 'cancel') {
            $expectedStatus = 'upcoming';
        } else {
            $expectedStatus = 'cancelled';
        }

        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            $_SESSION['event_message'] = 'Unable to prepare event status update.';
            $_SESSION['event_message_type'] = 'error';
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                'sis',
                $newStatus,
                $eventId,
                $expectedStatus
            );

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) === 1) {
                    if ($action === 'cancel') {
                        $_SESSION['event_message'] = 'Event cancelled successfully.';
                    } else {
                        $_SESSION['event_message'] = 'Event activated successfully.';
                    }

                    $_SESSION['event_message_type'] = 'success';
                } else {
                    $_SESSION['event_message'] = 'Unable to update event status. Please check its current status.';
                    $_SESSION['event_message_type'] = 'error';
                }
            } else {
                $_SESSION['event_message'] = 'Unable to update event status.';
                $_SESSION['event_message_type'] = 'error';
            }

            mysqli_stmt_close($stmt);
        }
    }

    header('Location: ' . baseUrl('admin/events/view.php?id=' . $eventId));
    exit;
}

/* Count registered participants */
$sql = "SELECT COALESCE(SUM(quantity), 0) AS registered
        FROM event_bookings
        WHERE event_id = ?
        AND booking_status IN ('confirmed', 'attended')";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Unable to count event participants.');
}

mysqli_stmt_bind_param($stmt, 'i', $eventId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$registrationData = mysqli_fetch_assoc($result);

$registered = (int) $registrationData['registered'];

mysqli_stmt_close($stmt);

/* Participation capacity */
$capacity = 0;

if ($event['max_participants'] !== null) {
    $capacity = (int) $event['max_participants'];
}

$progress = 0;

if ($capacity > 0) {
    $progress = round(($registered / $capacity) * 100);
}

if ($progress > 100) {
    $progress = 100;
}

/* Fetch recent participants */
$sql = "SELECT eb.id,
               eb.booking_reference,
               eb.quantity,
               eb.booking_status,
               eb.created_at,
               u.name AS participant_name,
               u.email AS participant_email
        FROM event_bookings eb
        INNER JOIN users u ON eb.user_id = u.id
        WHERE eb.event_id = ?
        ORDER BY eb.created_at DESC
        LIMIT 10";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Unable to load event participants.');
}

mysqli_stmt_bind_param($stmt, 'i', $eventId);
mysqli_stmt_execute($stmt);

$participantsResult = mysqli_stmt_get_result($stmt);

/* Store participants so both desktop and mobile layouts can use them */
$participants = [];

while ($participant = mysqli_fetch_assoc($participantsResult)) {
    $participants[] = $participant;
}

mysqli_stmt_close($stmt);

/* Format event status */
$status = ucfirst($event['status']);

$statusClass = 'bg-green-100 text-green-800 border-green-200';

if ($event['status'] === 'cancelled') {
    $statusClass = 'bg-red-100 text-red-800 border-red-200';
} elseif ($event['status'] === 'completed') {
    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
}

$statusDescription = 'This event is scheduled and currently accepting participants.';

if ($event['status'] === 'cancelled') {
    $statusDescription = 'This event has been cancelled and should not accept new participants.';
} elseif ($event['status'] === 'completed') {
    $statusDescription = 'This event has been marked as completed.';
}

/* Format event time */
$eventTime = 'Not specified';

if (!empty($event['start_time']) && !empty($event['end_time'])) {
    $eventTime = date('h:i A', strtotime($event['start_time']))
               . ' - '
               . date('h:i A', strtotime($event['end_time']));
} elseif (!empty($event['start_time'])) {
    $eventTime = date('h:i A', strtotime($event['start_time']));
} elseif (!empty($event['end_time'])) {
    $eventTime = date('h:i A', strtotime($event['end_time']));
}

/* Format NGO location */
$ngoLocation = $event['ngo_city'];

if (!empty($event['ngo_state'])) {
    if (!empty($ngoLocation)) {
        $ngoLocation .= ', ';
    }

    $ngoLocation .= $event['ngo_state'];
}

if (empty($ngoLocation)) {
    $ngoLocation = 'Not specified';
}
?>

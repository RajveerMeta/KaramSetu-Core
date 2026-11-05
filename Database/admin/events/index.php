
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

$success = '';
$error = '';

/* Success message */
if (isset($_GET['message']) && $_GET['message'] === 'updated') {
    $success = 'Event status updated successfully.';
}

/* Update event status */
if (isset($_POST['update_status'])) {
    $eventId = 0;
    $newStatus = '';

    if (isset($_POST['event_id'])) {
        $eventId = (int) $_POST['event_id'];
    }

    if (isset($_POST['new_status'])) {
        $newStatus = $_POST['new_status'];
    }

    if ($eventId > 0 && ($newStatus === 'cancelled' || $newStatus === 'upcoming')) {
        $sql = "UPDATE events
                SET status = ?
                WHERE id = ?
                AND status IN ('upcoming', 'cancelled')";

        $stmt = mysqli_prepare($con, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $newStatus, $eventId);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    mysqli_stmt_close($stmt);

                    header('Location: ' . baseUrl('admin/events/index.php?message=updated'));
                    exit;
                } else {
                    $error = 'Event status was not changed. Check the current status.';
                }
            } else {
                $error = 'Unable to update event status.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = 'Unable to prepare event status update.';
        }
    } else {
        $error = 'Invalid event or status.';
    }
}

/* Search and filter values */
$search = '';
$status = '';
$category = '';
$dateFilter = '';

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

if (isset($_GET['category'])) {
    $category = $_GET['category'];
}

if (isset($_GET['date'])) {
    $dateFilter = $_GET['date'];
}

/* Validate status filter */
if (
    $status !== '' &&
    $status !== 'upcoming' &&
    $status !== 'completed' &&
    $status !== 'cancelled'
) {
    $status = '';
}

/* Validate date filter */
if (
    $dateFilter !== 'week' &&
    $dateFilter !== 'month' &&
    $dateFilter !== 'next_month'
) {
    $dateFilter = '';
}

/* Build filters */
$where = " WHERE 1=1 ";

if ($search !== '') {
    $searchValue = mysqli_real_escape_string($con, $search);

    $where .= " AND (
                    events.title LIKE '%$searchValue%'
                    OR ngos.name LIKE '%$searchValue%'
                    OR events.location LIKE '%$searchValue%'
                )";
}

if ($status !== '') {
    $where .= " AND events.status = '$status'";
}

if ($category !== '') {
    $categoryValue = mysqli_real_escape_string($con, $category);
    $where .= " AND events.category = '$categoryValue'";
}

if ($dateFilter === 'week') {
    $where .= " AND events.event_date BETWEEN CURDATE()
                AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($dateFilter === 'month') {
    $where .= " AND MONTH(events.event_date) = MONTH(CURDATE())
                AND YEAR(events.event_date) = YEAR(CURDATE())";
} elseif ($dateFilter === 'next_month') {
    $where .= " AND MONTH(events.event_date) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
                AND YEAR(events.event_date) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))";
}

/* Total event count */
$countSql = "SELECT COUNT(*) AS total
             FROM events
             INNER JOIN ngos ON events.ngo_id = ngos.id";

$countResult = mysqli_query($con, $countSql);
$totalEvents = 0;

if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $totalEvents = (int) $countRow['total'];
}

/* Upcoming event count */
$upcomingCount = 0;
$upcomingResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM events WHERE status = 'upcoming'"
);

if ($upcomingResult) {
    $upcomingRow = mysqli_fetch_assoc($upcomingResult);
    $upcomingCount = (int) $upcomingRow['total'];
}

/* Completed event count */
$completedCount = 0;
$completedResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM events WHERE status = 'completed'"
);

if ($completedResult) {
    $completedRow = mysqli_fetch_assoc($completedResult);
    $completedCount = (int) $completedRow['total'];
}

/* Cancelled event count */
$suspendedCount = 0;
$suspendedResult = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM events WHERE status = 'cancelled'"
);

if ($suspendedResult) {
    $suspendedRow = mysqli_fetch_assoc($suspendedResult);
    $suspendedCount = (int) $suspendedRow['total'];
}

$activeEvents = $upcomingCount;

/* Event categories */
$categoryResult = mysqli_query(
    $con,
    "SELECT DISTINCT category
     FROM events
     WHERE category IS NOT NULL AND category != ''
     ORDER BY category"
);

/* Pagination */
$perPage = 10;
$page = 1;

if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}

if ($page < 1) {
    $page = 1;
}

/* Count filtered events */
$totalSql = "SELECT COUNT(*) AS total
             FROM events
             INNER JOIN ngos ON events.ngo_id = ngos.id
             $where";

$totalResult = mysqli_query($con, $totalSql);
$filteredTotal = 0;

if ($totalResult) {
    $totalRow = mysqli_fetch_assoc($totalResult);
    $filteredTotal = (int) $totalRow['total'];
}

$totalPages = (int) ceil($filteredTotal / $perPage);

if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

/* Fetch events */
$sql = "SELECT events.*,
               ngos.name AS ngo_name,
               (
                   SELECT COUNT(*)
                   FROM event_bookings
                   WHERE event_bookings.event_id = events.id
                   AND event_bookings.booking_status != 'cancelled'
               ) AS registered
        FROM events
        INNER JOIN ngos ON events.ngo_id = ngos.id
        $where
        ORDER BY events.event_date DESC
        LIMIT $perPage OFFSET $offset";

$result = mysqli_query($con, $sql);
?>

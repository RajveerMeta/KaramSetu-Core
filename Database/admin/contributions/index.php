
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

$search = '';
$type = '';
$status = '';
$dateFilter = '';
$page = 1;
$limit = 10;

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

if (isset($_GET['date'])) {
    $dateFilter = $_GET['date'];
}

if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}

if ($page < 1) {
    $page = 1;
}

$searchSql = mysqli_real_escape_string($con, $search);

$where = " WHERE 1=1 ";

if ($search != '') {
    $where .= " AND (
        c.donor_name LIKE '%$searchSql%'
        OR c.donor_email LIKE '%$searchSql%'
        OR n.name LIKE '%$searchSql%'
        OR ca.title LIKE '%$searchSql%'
        OR c.contribution_reference LIKE '%$searchSql%'
    )";
}

$allowedTypes = ['money', 'food', 'clothes', 'items'];

if (in_array($type, $allowedTypes)) {
    $typeSql = mysqli_real_escape_string($con, $type);
    $where .= " AND c.contribution_type = '$typeSql'";
}

if ($status == 'completed') {
    $where .= " AND c.payment_status = 'completed'
                AND c.fulfillment_status IN ('received', 'completed')";
} elseif ($status == 'pending') {
    $where .= " AND (
        c.payment_status = 'pending'
        OR c.fulfillment_status = 'pending'
    )";
} elseif ($status == 'cancelled') {
    $where .= " AND (
        c.payment_status = 'failed'
        OR c.fulfillment_status = 'cancelled'
    )";
}

if ($dateFilter == 'today') {
    $today = date('Y-m-d');
    $where .= " AND DATE(c.created_at) = '$today'";
} elseif ($dateFilter == 'week') {
    $where .= " AND YEARWEEK(c.created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($dateFilter == 'month') {
    $where .= " AND YEAR(c.created_at) = YEAR(CURDATE())
                AND MONTH(c.created_at) = MONTH(CURDATE())";
}

/* Summary cards */
$summarySql = "SELECT
    COUNT(*) AS total,
    COALESCE(SUM(CASE
        WHEN contribution_type = 'money'
        AND payment_status = 'completed'
        THEN monetary_amount ELSE 0
    END), 0) AS money_total,
    SUM(CASE
        WHEN contribution_type IN ('food', 'clothes', 'items')
        THEN 1 ELSE 0
    END) AS item_total
    FROM contributions";

$summaryResult = mysqli_query($con, $summarySql);
$summary = mysqli_fetch_assoc($summaryResult);

$totalContributions = (int) $summary['total'];
$moneyTotal = (float) $summary['money_total'];
$itemTotal = (int) $summary['item_total'];

/* Volunteer contributions placeholder */
$volunteerTotal = 0;

/* Count matching contributions */
$countSql = "SELECT COUNT(*) AS total
    FROM contributions c
    LEFT JOIN ngos n ON c.ngo_id = n.id
    LEFT JOIN causes ca ON c.cause_id = ca.id
    $where";

$countResult = mysqli_query($con, $countSql);
$countData = mysqli_fetch_assoc($countResult);
$filteredTotal = (int) $countData['total'];

$totalPages = ceil($filteredTotal / $limit);

if ($totalPages < 1) {
    $totalPages = 1;
}

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

/* Fetch contributions */
$sql = "SELECT
    c.id,
    c.contribution_reference,
    c.donor_name,
    c.donor_email,
    c.contribution_type,
    c.monetary_amount,
    c.payment_status,
    c.fulfillment_status,
    c.created_at,
    n.name AS ngo_name,
    ca.title AS cause_title
    FROM contributions c
    LEFT JOIN ngos n ON c.ngo_id = n.id
    LEFT JOIN causes ca ON c.cause_id = ca.id
    $where
    ORDER BY c.created_at DESC
    LIMIT $offset, $limit";

$result = mysqli_query($con, $sql);

/* Summary status counts */
$statusSql = "SELECT
    SUM(CASE
        WHEN payment_status = 'completed'
        AND fulfillment_status IN ('received', 'completed')
        THEN 1 ELSE 0
    END) AS completed_total,
    SUM(CASE
        WHEN payment_status = 'pending'
        OR fulfillment_status = 'pending'
        THEN 1 ELSE 0
    END) AS pending_total,
    SUM(CASE
        WHEN payment_status = 'failed'
        OR fulfillment_status = 'cancelled'
        THEN 1 ELSE 0
    END) AS cancelled_total
    FROM contributions";

$statusResult = mysqli_query($con, $statusSql);
$statusCounts = mysqli_fetch_assoc($statusResult);

function contributionStatus($paymentStatus, $fulfillmentStatus)
{
    if ($paymentStatus == 'failed' || $fulfillmentStatus == 'cancelled') {
        return 'Cancelled';
    }

    if ($paymentStatus == 'pending' || $fulfillmentStatus == 'pending') {
        return 'Pending';
    }

    if (
        $paymentStatus == 'completed' &&
        in_array($fulfillmentStatus, ['received', 'completed'])
    ) {
        return 'Completed';
    }

    return 'Pending';
}

function contributionTypeLabel($type)
{
    if ($type == 'money') {
        return 'Money';
    } elseif ($type == 'food') {
        return 'Food';
    } elseif ($type == 'clothes') {
        return 'Clothes';
    } elseif ($type == 'items') {
        return 'Items';
    }

    return ucfirst($type);
}

$queryParams = $_GET;
unset($queryParams['page']);
?>

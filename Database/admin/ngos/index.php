
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

// Search and filter values
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$status = '';
if (isset($_GET['status'])) {
    $status = strtolower(trim($_GET['status']));
}

$location = '';
if (isset($_GET['location'])) {
    $location = trim($_GET['location']);
}

$allowedStatuses = ['pending', 'approved', 'rejected', 'suspended'];

if (!in_array($status, $allowedStatuses, true)) {
    $status = '';
}

// Pagination settings
$limit = 8;
$page = 1;

if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}

if ($page < 1) {
    $page = 1;
}

// Build filter conditions
$where = " WHERE 1=1";

if ($search !== '') {
    $searchValue = mysqli_real_escape_string($con, $search);
    $where .= " AND n.name LIKE '%$searchValue%'";
}

if ($status !== '') {
    $statusValue = mysqli_real_escape_string($con, $status);
    $where .= " AND n.verification_status = '$statusValue'";
}

if ($location !== '') {
    $locationValue = mysqli_real_escape_string($con, $location);
    $where .= " AND n.city = '" . mysqli_real_escape_string($con, $location) . "'";
}

// Summary counts for all NGOs
$countSql = "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(verification_status = 'pending'), 0) AS pending,
                COALESCE(SUM(verification_status = 'approved'), 0) AS approved,
                COALESCE(SUM(verification_status = 'rejected'), 0) AS rejected
             FROM ngos";

$countResult = mysqli_query($con, $countSql);

$counts = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

if ($countResult) {
    $counts = mysqli_fetch_assoc($countResult);
}

// Count filtered NGOs
$totalSql = "SELECT COUNT(*) AS total
             FROM ngos n" . $where;

$totalResult = mysqli_query($con, $totalSql);
$totalRecords = 0;

if ($totalResult) {
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalRecords = (int) $totalRow['total'];
}

// Calculate pagination
$totalPages = max(1, (int) ceil($totalRecords / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

// Fetch NGOs
$sql = "SELECT
            n.id,
            n.name,
            n.city AS location,
            n.official_email AS email,
            n.created_at,
            n.verification_status AS status
        FROM ngos n
        $where
        ORDER BY n.created_at DESC
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($con, $sql);

$ngos = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $registered = '—';

        if (!empty($row['created_at'])) {
            $registered = date('d M Y', strtotime($row['created_at']));
        }

        $ngos[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'location' => $row['location'],
            'email' => $row['email'],
            'registered' => $registered,
            'status' => ucfirst($row['status'])
        ];
    }
}

// Pagination result range
$startRecord = $totalRecords > 0 ? $offset + 1 : 0;
$endRecord = min($offset + $limit, $totalRecords);

// Preserve filters in pagination links
$queryParams = [
    'search' => $search,
    'status' => $status,
    'location' => $location
];

$queryParams = array_filter($queryParams, function ($value) {
    return $value !== '';
});
?>

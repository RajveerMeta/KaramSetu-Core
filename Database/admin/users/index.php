
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

// Update user status
if (isset($_POST['update_status'])) {
    $userId = (int) $_POST['user_id'];
    $newStatus = $_POST['new_status'];

    if ($userId > 0 && in_array($newStatus, ['active', 'suspended'], true)) {
        $sql = "UPDATE users
                SET status = '$newStatus'
                WHERE id = $userId AND role = 'user'";

        if (mysqli_query($con, $sql)) {
            $_SESSION['user_message'] = 'User status updated successfully.';
            $_SESSION['user_message_type'] = 'success';
        } else {
            $_SESSION['user_message'] = 'Unable to update user status.';
            $_SESSION['user_message_type'] = 'error';
        }
    } else {
        $_SESSION['user_message'] = 'Invalid user or status.';
        $_SESSION['user_message_type'] = 'error';
    }

    header('Location: ' . baseUrl('admin/users/index.php'));
    exit;
}

// Search and filters
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$statusFilter = '';
if (isset($_GET['status'])) {
    $statusFilter = $_GET['status'];
}

$registrationFilter = '';
if (isset($_GET['registration'])) {
    $registrationFilter = $_GET['registration'];
}

$where = " WHERE role = 'user' ";

$searchValue = mysqli_real_escape_string($con, $search);

if ($search !== '') {
    $where .= " AND (name LIKE '%$searchValue%'
                 OR email LIKE '%$searchValue%')";
}

if (in_array($statusFilter, ['active', 'suspended'], true)) {
    $where .= " AND status = '$statusFilter'";
}

if ($registrationFilter === 'month') {
    $where .= " AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND created_at < DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')";
} elseif ($registrationFilter === 'three_months') {
    $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                AND created_at <= NOW()";
} elseif ($registrationFilter === 'year') {
    $where .= " AND YEAR(created_at) = YEAR(CURDATE())";
}

// Summary cards
$totalResult = mysqli_query(
    $con,
    "SELECT
        COUNT(*) AS total_users,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_users,
        SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) AS suspended_users,
        SUM(CASE
            WHEN created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
             AND created_at < DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')
            THEN 1 ELSE 0
        END) AS new_users
     FROM users
     WHERE role = 'user'"
);

$summary = mysqli_fetch_assoc($totalResult);

$totalUsers = (int) $summary['total_users'];
$activeUsers = (int) $summary['active_users'];
$suspendedUsers = (int) $summary['suspended_users'];
$newUsers = (int) $summary['new_users'];

// Fetch filtered users
$sql = "SELECT id, name, email, created_at, status
        FROM users
        $where
        ORDER BY id DESC";

$result = mysqli_query($con, $sql);

$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Flash message
$alertMessage = '';
if (isset($_SESSION['user_message'])) {
    $alertMessage = $_SESSION['user_message'];
}

$alertType = 'success';
if (isset($_SESSION['user_message_type'])) {
    $alertType = $_SESSION['user_message_type'];
}

unset($_SESSION['user_message'], $_SESSION['user_message_type']);
?>

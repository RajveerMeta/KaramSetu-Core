
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

// Get user ID
$userId = 0;

if (isset($_GET['id'])) {
    $userId = (int) $_GET['id'];
}

if ($userId <= 0) {
    header('Location: ' . baseUrl('admin/users/index.php'));
    exit;
}

// Update account status
if (isset($_POST['update_status'])) {
    $newStatus = '';

    if (isset($_POST['new_status'])) {
        $newStatus = $_POST['new_status'];
    }

    if (in_array($newStatus, ['active', 'suspended'], true)) {
        $sql = "UPDATE users
                SET status = '$newStatus'
                WHERE id = $userId AND role = 'user'";

        if (mysqli_query($con, $sql) && mysqli_affected_rows($con) >= 0) {
            header('Location: ' . baseUrl('admin/users/view.php?id=' . $userId . '&message=updated'));
            exit;
        }
    }

    header('Location: ' . baseUrl('admin/users/view.php?id=' . $userId . '&message=error'));
    exit;
}

// Fetch user details
$sql = "SELECT *
        FROM users
        WHERE id = $userId AND role = 'user'
        LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: ' . baseUrl('admin/users/index.php'));
    exit;
}

$user = mysqli_fetch_assoc($result);

// Prepare display values
$userName = $user['name'];
$userEmail = $user['email'];
$userPhone = $user['phone'];

$userLocation = '—';

if (isset($user['city']) && $user['city'] !== '') {
    $userLocation = $user['city'];
} elseif (isset($user['location']) && $user['location'] !== '') {
    $userLocation = $user['location'];
}

$userStatus = strtolower($user['status']);

if (!in_array($userStatus, ['active', 'suspended'], true)) {
    $userStatus = 'active';
}

$registered = '—';

if (!empty($user['created_at'])) {
    $registered = date('d M Y', strtotime($user['created_at']));
} elseif (!empty($user['registration_date'])) {
    $registered = date('d M Y', strtotime($user['registration_date']));
}

// Generate initials
$nameParts = explode(' ', trim($userName));
$initials = '';

foreach ($nameParts as $part) {
    if ($part !== '') {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    if (strlen($initials) >= 2) {
        break;
    }
}

$statusClass = $userStatus === 'active'
    ? 'bg-green-100 text-green-800 border-green-200'
    : 'bg-red-100 text-red-800 border-red-200';

$statusText = ucfirst($userStatus);

// Contribution and activity placeholders
$totalContributions = 0;
$totalContributed = 0;
$karmaPoints = 0;
$eventsJoined = 0;
$contributions = [];
$activities = [];

// Status message
$message = '';

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

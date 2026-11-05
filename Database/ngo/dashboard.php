
<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../db_config.php';

/* Get Logged-in NGO */
$userId = 0;

if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
}

$ngo = [];
$ngoId = 0;

$sql = "SELECT n.*
        FROM ngos n
        WHERE n.user_id = $userId
        LIMIT 1";

$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $ngo = mysqli_fetch_assoc($result);
    $ngoId = (int) $ngo['id'];
}

if (empty($ngo)) {
    $ngo['name'] = 'NGO';
    $ngo['verification_status'] = 'pending';
}

/* Overview Counts */
$dashboardData = [
    'total_funds_raised' => 0,
    'active_causes' => 0,
    'upcoming_events_count' => 0,
    'total_contributions' => 0,
    'fundraising_raised' => 0,
    'fundraising_goal' => 0,
    'fundraising_progress' => 0,
    'recent_contributions' => [],
    'active_causes_list' => []
];

/* Total Funds Raised */
$sql = "SELECT COALESCE(SUM(monetary_amount), 0) AS total
        FROM contributions
        WHERE ngo_id = $ngoId
        AND contribution_type = 'money'
        AND payment_status = 'completed'";

$result = mysqli_query($con, $sql);

if ($result) {
    $data = mysqli_fetch_assoc($result);
    $dashboardData['fundraising_raised'] = (float) $data['total'];
    $dashboardData['total_funds_raised'] = '₹' . number_format($data['total'], 0);
}

/* Active Causes */
$sql = "SELECT COUNT(*) AS total
        FROM causes
        WHERE ngo_id = $ngoId";

$result = mysqli_query($con, $sql);

if ($result) {
    $dashboardData['active_causes'] = (int) mysqli_fetch_assoc($result)['total'];
}

/* Upcoming Events */
$sql = "SELECT COUNT(*) AS total
        FROM events
        WHERE ngo_id = $ngoId
        AND status = 'upcoming'";

$result = mysqli_query($con, $sql);

if ($result) {
    $dashboardData['upcoming_events_count'] = (int) mysqli_fetch_assoc($result)['total'];
}

/* Total Contributions */
$sql = "SELECT COUNT(*) AS total
        FROM contributions
        WHERE ngo_id = $ngoId";

$result = mysqli_query($con, $sql);

if ($result) {
    $dashboardData['total_contributions'] = (int) mysqli_fetch_assoc($result)['total'];
}

/* Fundraising Goal */
$dashboardData['fundraising_goal'] = 500000;

if ($dashboardData['fundraising_goal'] > 0) {
    $dashboardData['fundraising_progress'] = round(
        ($dashboardData['fundraising_raised'] / $dashboardData['fundraising_goal']) * 100
    );
}

if ($dashboardData['fundraising_progress'] > 100) {
    $dashboardData['fundraising_progress'] = 100;
}

/* Recent Contributions */
$sql = "SELECT
            c.donor_name,
            c.contribution_type,
            c.monetary_amount,
            c.created_at
        FROM contributions c
        WHERE c.ngo_id = $ngoId
        ORDER BY c.created_at DESC
        LIMIT 4";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($contribution = mysqli_fetch_assoc($result)) {
        $donor = $contribution['donor_name'];

        if (empty($donor) || $donor == 'Anonymous') {
            $donor = 'Anonymous';
        }

        $type = ucfirst($contribution['contribution_type']);
        $amount = '';

        if ($contribution['contribution_type'] == 'money') {
            $amount = '₹' . number_format((float) $contribution['monetary_amount'], 2);
        } elseif ($contribution['contribution_type'] == 'food') {
            $amount = 'Food';
        } elseif ($contribution['contribution_type'] == 'clothes') {
            $amount = 'Clothing';
        } else {
            $amount = 'Items';
        }

        $date = date('d M Y', strtotime($contribution['created_at']));

        $dashboardData['recent_contributions'][] = [
            'donor' => $donor,
            'type' => $type,
            'amount' => $amount,
            'date' => $date
        ];
    }
}

/* Active Causes List */
$sql = "SELECT id, title, description
        FROM causes
        WHERE ngo_id = $ngoId
        LIMIT 2";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($cause = mysqli_fetch_assoc($result)) {
        $dashboardData['active_causes_list'][] = [
            'name' => $cause['title'],
            'raised' => '₹0',
            'goal' => 'Not set',
            'progress' => 0
        ];
    }
}

/* Upcoming Events */
$upcoming_events = [];

$sql = "SELECT *
        FROM events
        WHERE ngo_id = $ngoId
        AND status = 'upcoming'
        ORDER BY id DESC
        LIMIT 2";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($event = mysqli_fetch_assoc($result)) {
        $eventDate = '';

        if (isset($event['event_date']) && !empty($event['event_date'])) {
            $eventDate = date('d M Y', strtotime($event['event_date']));
        }

        $eventType = 'Event';

        if (isset($event['type']) && !empty($event['type'])) {
            $eventType = $event['type'];
        }

        $upcoming_events[] = [
            'title' => $event['title'],
            'date' => $eventDate,
            'type' => $eventType
        ];
    }
}
?>

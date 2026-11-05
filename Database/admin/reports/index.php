
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

/* Overview Counts */
$overview = [
    'users' => 0,
    'ngos' => 0,
    'causes' => 0,
    'events' => 0
];

$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
$result = mysqli_query($con, $sql);

if ($result) {
    $overview['users'] = mysqli_fetch_assoc($result)['total'];
}

$sql = "SELECT COUNT(*) AS total FROM ngos";
$result = mysqli_query($con, $sql);

if ($result) {
    $overview['ngos'] = mysqli_fetch_assoc($result)['total'];
}

$sql = "SELECT COUNT(*) AS total FROM causes";
$result = mysqli_query($con, $sql);

if ($result) {
    $overview['causes'] = mysqli_fetch_assoc($result)['total'];
}

$sql = "SELECT COUNT(*) AS total FROM events";
$result = mysqli_query($con, $sql);

if ($result) {
    $overview['events'] = mysqli_fetch_assoc($result)['total'];
}

/* Contribution Summary */
$contribution_summary = [
    'money' => 0,
    'food' => 0,
    'clothes' => 0,
    'items' => 0
];

$sql = "SELECT
            SUM(CASE
                WHEN contribution_type = 'money'
                AND payment_status = 'completed'
                THEN monetary_amount
                ELSE 0
            END) AS money_total,
            SUM(CASE WHEN contribution_type = 'food' THEN 1 ELSE 0 END) AS food_total,
            SUM(CASE WHEN contribution_type = 'clothes' THEN 1 ELSE 0 END) AS clothes_total,
            SUM(CASE WHEN contribution_type = 'items' THEN 1 ELSE 0 END) AS items_total
        FROM contributions";

$result = mysqli_query($con, $sql);

if ($result) {
    $summary = mysqli_fetch_assoc($result);

    $contribution_summary['money'] = $summary['money_total'];
    $contribution_summary['food'] = $summary['food_total'];
    $contribution_summary['clothes'] = $summary['clothes_total'];
    $contribution_summary['items'] = $summary['items_total'];
}

/* Weekly Money Contributions - Last Four Weeks */
$weekly_activity = [];
$max_weekly = 0;

for ($i = 3; $i >= 0; $i--) {
    $startDate = date('Y-m-d', strtotime('-' . ($i * 7 + 6) . ' days'));
    $endDate = date('Y-m-d', strtotime('-' . ($i * 7) . ' days'));

    $sql = "SELECT
                COALESCE(SUM(monetary_amount), 0) AS total
            FROM contributions
            WHERE contribution_type = 'money'
            AND payment_status = 'completed'
            AND created_at >= '$startDate 00:00:00'
            AND created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";

    $result = mysqli_query($con, $sql);
    $amount = 0;

    if ($result) {
        $data = mysqli_fetch_assoc($result);
        $amount = (float) $data['total'];
    }

    $weekly_activity[] = [
        'week' => date('d M', strtotime($startDate)) . ' - ' . date('d M', strtotime($endDate)),
        'amount' => $amount
    ];

    if ($amount > $max_weekly) {
        $max_weekly = $amount;
    }
}

/* Contribution Types */
$contribution_types = [];

$sql = "SELECT
            contribution_type,
            COUNT(*) AS total
        FROM contributions
        GROUP BY contribution_type";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($type = mysqli_fetch_assoc($result)) {
        $contribution_types[] = [
            'type' => ucfirst($type['contribution_type']),
            'count' => (int) $type['total']
        ];
    }
}

$total_contributions = 0;

foreach ($contribution_types as $type) {
    $total_contributions += $type['count'];
}

/* NGO Activity */
$ngo_activity = [
    'approved' => 0,
    'pending' => 0,
    'suspended' => 0,
    'total' => 0
];

$sql = "SELECT verification_status, COUNT(*) AS total
        FROM ngos
        GROUP BY verification_status";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($ngo = mysqli_fetch_assoc($result)) {
        if ($ngo['verification_status'] == 'approved') {
            $ngo_activity['approved'] = $ngo['total'];
        } elseif ($ngo['verification_status'] == 'pending') {
            $ngo_activity['pending'] = $ngo['total'];
        } elseif ($ngo['verification_status'] == 'suspended') {
            $ngo_activity['suspended'] = $ngo['total'];
        }
    }
}

$ngo_activity['total'] = $overview['ngos'];

/* Event Activity */
$event_activity = [
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'total' => 0
];

$sql = "SELECT status, COUNT(*) AS total
        FROM events
        GROUP BY status";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($event = mysqli_fetch_assoc($result)) {
        if ($event['status'] == 'upcoming') {
            $event_activity['upcoming'] = $event['total'];
        } elseif ($event['status'] == 'completed') {
            $event_activity['completed'] = $event['total'];
        } elseif ($event['status'] == 'cancelled') {
            $event_activity['cancelled'] = $event['total'];
        }
    }
}

$event_activity['total'] = $overview['events'];

/* Top Contributors */
$top_contributors = [];

$sql = "SELECT
            c.user_id,
            COALESCE(u.name, c.donor_name, 'Anonymous Contributor') AS contributor_name,
            COUNT(c.id) AS contribution_count,
            COALESCE(SUM(
                CASE
                    WHEN c.contribution_type = 'money'
                    AND c.payment_status = 'completed'
                    THEN c.monetary_amount
                    ELSE 0
                END
            ), 0) AS total_amount,
            COALESCE(SUM(c.karma_points_awarded), 0) AS total_points
        FROM contributions c
        LEFT JOIN users u ON c.user_id = u.id
        GROUP BY c.user_id, u.name, c.donor_name
        ORDER BY total_points DESC, contribution_count DESC
        LIMIT 5";

$result = mysqli_query($con, $sql);

if ($result) {
    $rank = 1;

    while ($user = mysqli_fetch_assoc($result)) {
        $top_contributors[] = [
            'rank' => $rank,
            'name' => $user['contributor_name'],
            'count' => $user['contribution_count'],
            'amount' => $user['total_amount'],
            'points' => $user['total_points']
        ];

        $rank++;
    }
}

/* Most Active NGOs */
$top_ngos = [];

$sql = "SELECT
            n.id,
            n.name,
            (SELECT COUNT(*) FROM events e WHERE e.ngo_id = n.id) AS event_count,
            (SELECT COUNT(*) FROM causes ca WHERE ca.ngo_id = n.id) AS cause_count,
            (SELECT COALESCE(SUM(c.monetary_amount), 0)
             FROM contributions c
             WHERE c.ngo_id = n.id
             AND c.contribution_type = 'money'
             AND c.payment_status = 'completed') AS received_amount
        FROM ngos n
        ORDER BY received_amount DESC, event_count DESC
        LIMIT 5";

$result = mysqli_query($con, $sql);

if ($result) {
    while ($ngo = mysqli_fetch_assoc($result)) {
        $top_ngos[] = [
            'name' => $ngo['name'],
            'events' => $ngo['event_count'],
            'causes' => $ngo['cause_count'],
            'received' => $ngo['received_amount']
        ];
    }
}
?>

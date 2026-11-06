
<?php
require_once __DIR__ . '/../db_config.php';
// APPROVED NGOs
$ngos = [];

$ngoQuery = mysqli_query($con, "
    SELECT
        n.id,
        n.name,
        n.primary_cause,
        n.city,
        n.state,

        (
            SELECT c.title
            FROM causes c
            WHERE c.ngo_id = n.id
            AND c.status = 'active'
            ORDER BY c.id DESC
            LIMIT 1
        ) AS active_cause,

        (
            SELECT GROUP_CONCAT(DISTINCT cat.contribution_type)
            FROM causes c
            INNER JOIN cause_accepted_types cat
                ON c.id = cat.cause_id
            WHERE c.ngo_id = n.id
            AND c.status = 'active'
        ) AS support_types

    FROM ngos n
    WHERE n.verification_status = 'approved'
    ORDER BY n.verified_at DESC
    LIMIT 3
");

while ($row = mysqli_fetch_assoc($ngoQuery)) {
    $ngos[] = $row;
}


// UPCOMING EVENTS
$events = [];

$eventQuery = mysqli_query($con, "
    SELECT
        id,
        title,
        category,
        location,
        event_date,
        start_time,
        entry_fee,
        short_description
    FROM events
    WHERE status = 'upcoming'
    AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 4
");

while ($row = mysqli_fetch_assoc($eventQuery)) {
    $events[] = $row;
}


// COMMUNITY LEADERBOARD
$leaders = [];

$leaderQuery = mysqli_query($con, "
    SELECT
        u.id,
        u.name,
        up.karma_points,
        COUNT(DISTINCT c.id) AS total_contributions

    FROM users u

    INNER JOIN user_profiles up
        ON u.id = up.user_id

    LEFT JOIN contributions c
        ON u.id = c.user_id
        AND c.payment_status = 'completed'

    WHERE u.role = 'user'
    AND u.status = 'active'

    GROUP BY u.id, u.name, up.karma_points

    ORDER BY up.karma_points DESC, u.id ASC
    LIMIT 3
");

while ($row = mysqli_fetch_assoc($leaderQuery)) {
    $leaders[] = $row;
}
?>

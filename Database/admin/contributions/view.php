
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . baseUrl('admin/contributions/index.php'));
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT
            c.*,
            u.name AS contributor_name,
            u.email AS contributor_email,
            u.phone AS contributor_phone,
            n.id AS receiving_ngo_id,
            n.name AS ngo_name,
            n.ngo_type,
            n.primary_cause,
            n.city AS ngo_city,
            n.state AS ngo_state,
            n.verification_status AS ngo_status,
            ca.title AS cause_name,
            ca.description AS cause_description
        FROM contributions c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN ngos n ON c.ngo_id = n.id
        LEFT JOIN causes ca ON c.cause_id = ca.id
        WHERE c.id = $id
        LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: ' . baseUrl('admin/contributions/index.php'));
    exit;
}

$row = mysqli_fetch_assoc($result);

$contributionId = $row['contribution_reference'];
$contributorName = $row['donor_name'];
$contributorEmail = $row['donor_email'];
$contributorPhone = $row['contributor_phone'];

if (empty($contributorName)) {
    $contributorName = $row['contributor_name'];
}

if (empty($contributorEmail)) {
    $contributorEmail = $row['contributor_email'];
}

if (empty($contributorPhone)) {
    $contributorPhone = 'Not provided';
}

if (empty($contributorName)) {
    $contributorName = 'Anonymous Contributor';
}

if (empty($contributorEmail)) {
    $contributorEmail = 'Not provided';
}

$contributionName = $row['cause_name'];

if (empty($contributionName)) {
    $contributionName = ucfirst($row['contribution_type']) . ' Contribution';
}

$contributionType = ucfirst($row['contribution_type']);

$amount = $row['monetary_amount'];

if ($amount !== null && $amount !== '') {
    $formattedAmount = '₹' . number_format((float) $amount, 2);
} else {
    $formattedAmount = 'N/A';
}

$date = date('d M Y', strtotime($row['created_at']));

if (
    $row['payment_status'] == 'failed' ||
    $row['fulfillment_status'] == 'cancelled'
) {
    $status = 'Cancelled';
} elseif (
    $row['payment_status'] == 'pending' ||
    $row['fulfillment_status'] == 'pending'
) {
    $status = 'Pending';
} elseif (
    $row['payment_status'] == 'completed' &&
    (
        $row['fulfillment_status'] == 'received' ||
        $row['fulfillment_status'] == 'completed'
    )
) {
    $status = 'Completed';
} else {
    $status = ucfirst($row['fulfillment_status']);
}

$ngoName = $row['ngo_name'];
$ngoType = $row['ngo_type'];
$ngoLocation = trim($row['ngo_city'] . ', ' . $row['ngo_state']);
$ngoStatus = ucfirst($row['ngo_status']);

$causeName = $row['cause_name'];
$causeCategory = $row['primary_cause'];
$causeDescription = $row['cause_description'];

if (empty($ngoName)) {
    $ngoName = 'NGO information unavailable';
}

if (empty($ngoType)) {
    $ngoType = 'Not available';
} else {
    $ngoType = ucfirst(str_replace('_', ' ', $ngoType));
}

if (empty($ngoLocation) || $ngoLocation == ',') {
    $ngoLocation = 'Not available';
}

if (empty($ngoStatus)) {
    $ngoStatus = 'Not available';
}

if (empty($causeName)) {
    $causeName = 'Cause information unavailable';
}

if (empty($causeCategory)) {
    $causeCategory = 'Not specified';
}

if (empty($causeDescription)) {
    $causeDescription = 'No description available.';
}

$statusClass = 'bg-forest/10 text-forest border-forest/20';

if ($status == 'Pending') {
    $statusClass = 'bg-amber-50 text-amber-800 border-amber-200';
} elseif ($status == 'Cancelled') {
    $statusClass = 'bg-red-50 text-red-700 border-red-200';
}
?>

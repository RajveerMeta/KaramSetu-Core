<?php
require_once __DIR__ . '/../../Database/db_config.php';

// Total users
$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total'];

// Total NGOs
$sql = "SELECT COUNT(*) AS total FROM ngos";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$totalNGOs = $row['total'];

// Pending NGO approvals
$sql = "SELECT COUNT(*) AS total FROM ngos
        WHERE verification_status = 'pending'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$pendingNGOs = $row['total'];

// Total completed contributions
$sql = "SELECT SUM(monetary_amount) AS total
        FROM contributions
        WHERE payment_status = 'completed'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$totalContributions = $row['total'] ?? 0;

// Get pending NGOs for dashboard table
$sql = "SELECT id, name, city, created_at, verification_status
        FROM ngos
        WHERE verification_status = 'pending'
        ORDER BY created_at DESC
        LIMIT 5";

$result = mysqli_query($con, $sql);

$pendingNGOList = [];

while ($row = mysqli_fetch_assoc($result)) {
    $pendingNGOList[] = $row;
}
// Recent Contributions
$sql = "SELECT id, monetary_amount, payment_status
        FROM contributions
        ORDER BY id DESC
        LIMIT 5";

$result = mysqli_query($con, $sql);

$recentContributions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $recentContributions[] = $row;
}

<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function escapeNgoValue($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$ngoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$ngoId || $ngoId < 1) {
    $_SESSION['ngo_message'] = 'Invalid NGO ID.';
    $_SESSION['ngo_message_type'] = 'error';

    header('Location: ' . baseUrl('admin/ngos/index.php'));
    exit;
}

/* Load NGO details */
$sql = "SELECT n.*,
               u.name AS account_name,
               u.email AS account_email,
               u.phone AS account_phone,
               u.status AS account_status
        FROM ngos n
        LEFT JOIN users u ON u.id = n.user_id
        WHERE n.id = ?
        LIMIT 1";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Unable to load NGO details.');
}

mysqli_stmt_bind_param($stmt, 'i', $ngoId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$ngo = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$ngo) {
    $_SESSION['ngo_message'] = 'NGO not found.';
    $_SESSION['ngo_message_type'] = 'error';

    header('Location: ' . baseUrl('admin/ngos/index.php'));
    exit;
}

/* Approve or reject NGO */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = '';
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }

    $reason = '';
    if (isset($_POST['rejection_reason'])) {
        $reason = trim($_POST['rejection_reason']);
    }

    if ($ngo['verification_status'] !== 'pending') {
        $_SESSION['ngo_message'] = 'This NGO has already been reviewed.';
        $_SESSION['ngo_message_type'] = 'error';
    } elseif ($action !== 'approve' && $action !== 'reject') {
        $_SESSION['ngo_message'] = 'Invalid action.';
        $_SESSION['ngo_message_type'] = 'error';
    } elseif ($action === 'reject' && $reason === '') {
        $_SESSION['ngo_message'] = 'Please provide a rejection reason.';
        $_SESSION['ngo_message_type'] = 'error';
    } else {
        mysqli_begin_transaction($con);

        try {
            if ($action === 'approve') {
                $sql = "UPDATE ngos
                        SET verification_status = 'approved',
                            rejection_reason = NULL,
                            verified_at = NOW()
                        WHERE id = ?
                        AND verification_status = 'pending'";

                $stmt = mysqli_prepare($con, $sql);

                if (!$stmt) {
                    throw new Exception('Unable to prepare NGO update.');
                }

                mysqli_stmt_bind_param($stmt, 'i', $ngoId);
                mysqli_stmt_execute($stmt);

                $updated = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($updated !== 1) {
                    throw new Exception('Unable to update NGO status.');
                }

                /* Activate the linked NGO account */
                if (!empty($ngo['user_id'])) {
                    $userId = (int) $ngo['user_id'];

                    $sql = "UPDATE users
                            SET status = 'active'
                            WHERE id = ? AND role = 'ngo'";

                    $stmt = mysqli_prepare($con, $sql);

                    if (!$stmt) {
                        throw new Exception('Unable to prepare account update.');
                    }

                    mysqli_stmt_bind_param($stmt, 'i', $userId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $_SESSION['ngo_message'] = 'NGO approved successfully.';
            } else {
                $sql = "UPDATE ngos
                        SET verification_status = 'rejected',
                            rejection_reason = ?,
                            verified_at = NULL
                        WHERE id = ?
                        AND verification_status = 'pending'";

                $stmt = mysqli_prepare($con, $sql);

                if (!$stmt) {
                    throw new Exception('Unable to prepare NGO update.');
                }

                mysqli_stmt_bind_param($stmt, 'si', $reason, $ngoId);
                mysqli_stmt_execute($stmt);

                $updated = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($updated !== 1) {
                    throw new Exception('Unable to update NGO status.');
                }

                $_SESSION['ngo_message'] = 'NGO rejected successfully.';
            }

            mysqli_commit($con);
            $_SESSION['ngo_message_type'] = 'success';
        } catch (Throwable $e) {
            mysqli_rollback($con);

            $_SESSION['ngo_message'] = 'Unable to save the decision. Please try again.';
            $_SESSION['ngo_message_type'] = 'error';
        }
    }

    header('Location: ' . baseUrl('admin/ngos/view.php?id=' . $ngoId));
    exit;
}

/* Load submitted documents */
$sql = "SELECT *
        FROM ngo_documents
        WHERE ngo_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Unable to load NGO documents.');
}

mysqli_stmt_bind_param($stmt, 'i', $ngoId);
mysqli_stmt_execute($stmt);

$documentsResult = mysqli_stmt_get_result($stmt);
$documents = [];

while ($document = mysqli_fetch_assoc($documentsResult)) {
    $documents[] = $document;
}

mysqli_stmt_close($stmt);

/* Status badge */
$statusClasses = [
    'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
    'approved' => 'bg-green-100 text-green-800 border-green-200',
    'rejected' => 'bg-red-100 text-red-800 border-red-200',
    'suspended' => 'bg-gray-100 text-gray-800 border-gray-200'
];

$status = $ngo['verification_status'];
$statusClass = $statusClasses[$status];

$statusLabel = ucfirst($status);

$ngoTypes = [
    'trust' => 'Trust',
    'society' => 'Society',
    'section_8' => 'Section 8 Company',
    'other' => 'Other'
];

$ngoType = 'Other';
if (isset($ngoTypes[$ngo['ngo_type']])) {
    $ngoType = $ngoTypes[$ngo['ngo_type']];
}

/* Flash messages */
$alertMessage = '';
if (isset($_SESSION['ngo_message'])) {
    $alertMessage = $_SESSION['ngo_message'];
}

$alertType = 'success';
if (isset($_SESSION['ngo_message_type'])) {
    $alertType = $_SESSION['ngo_message_type'];
}

unset($_SESSION['ngo_message'], $_SESSION['ngo_message_type']);
?>

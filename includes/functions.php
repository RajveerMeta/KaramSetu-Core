<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('asset')) {
    function asset($path) {
        return '/KaramSetu-Core/public/' . ltrim($path, '/');
    }
}

if (!function_exists('baseUrl')) {
    function baseUrl($path = '') {
        return '/KaramSetu-Core/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('getUserAccountStatus')) {
    function getUserAccountStatus($userId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        if ($userId <= 0 || !isset($con) || !$con) {
            return null;
        }
        $res = mysqli_query($con, "SELECT id, status, role FROM users WHERE id = $userId LIMIT 1");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row;
        }
        return null;
    }
}

if (!function_exists('getNgoRecordForUser')) {
    function getNgoRecordForUser($userId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        if ($userId <= 0 || !isset($con) || !$con) {
            return null;
        }
        $res = mysqli_query($con, "SELECT * FROM ngos WHERE user_id = $userId LIMIT 1");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row;
        }
        return null;
    }
}

if (!function_exists('redirectUserToHomeDashboard')) {
    function redirectUserToHomeDashboard($role = null) {
        if ($role === null) {
            $role = strtolower(trim($_SESSION['user_role'] ?? ''));
        }
        if ($role === 'admin') {
            header('Location: ' . baseUrl('admin/dashboard.php'));
        } elseif ($role === 'ngo') {
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $userStatus = getUserAccountStatus($userId);
            $ngo = getNgoRecordForUser($userId);
            $userStat = strtolower(trim($userStatus['status'] ?? ''));
            $ngoStat = strtolower(trim($ngo['verification_status'] ?? ''));

            if (!$userStatus || !$ngo || $userStat === 'suspended' || $ngoStat === 'suspended') {
                header('Location: ' . baseUrl('auth/logout.php'));
            } elseif ($userStat === 'active' && $ngoStat === 'approved') {
                header('Location: ' . baseUrl('ngo/dashboard.php'));
            } else {
                header('Location: ' . baseUrl('ngo/application-status.php'));
            }
        } elseif ($role === 'user') {
            header('Location: ' . baseUrl('user/dashboard.php'));
        } else {
            header('Location: ' . baseUrl('auth/login.php'));
        }
        exit;
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . baseUrl('auth/login.php'));
            exit;
        }
        $role = strtolower(trim($_SESSION['user_role'] ?? ''));
        if ($role !== 'admin') {
            redirectUserToHomeDashboard($role);
            exit;
        }
        $userStatus = getUserAccountStatus((int)$_SESSION['user_id']);
        if (!$userStatus || $userStatus['status'] !== 'active' || $userStatus['role'] !== 'admin') {
            header('Location: ' . baseUrl('auth/logout.php'));
            exit;
        }
    }
}

if (!function_exists('requireUser')) {
    function requireUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . baseUrl('auth/login.php'));
            exit;
        }
        $role = strtolower(trim($_SESSION['user_role'] ?? ''));
        if ($role !== 'user') {
            redirectUserToHomeDashboard($role);
            exit;
        }
        $userStatus = getUserAccountStatus((int)$_SESSION['user_id']);
        if (!$userStatus || $userStatus['status'] !== 'active' || $userStatus['role'] !== 'user') {
            header('Location: ' . baseUrl('auth/logout.php'));
            exit;
        }
    }
}

if (!function_exists('requireNgo')) {
    function requireNgo($allowPending = false) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . baseUrl('auth/login.php'));
            exit;
        }
        $role = strtolower(trim($_SESSION['user_role'] ?? ''));
        if ($role !== 'ngo') {
            redirectUserToHomeDashboard($role);
            exit;
        }
        $userId = (int)$_SESSION['user_id'];
        $userStatus = getUserAccountStatus($userId);
        $ngo = getNgoRecordForUser($userId);

        if (!$userStatus || !$ngo) {
            header('Location: ' . baseUrl('auth/logout.php'));
            exit;
        }

        $userStat = strtolower(trim($userStatus['status'] ?? ''));
        $ngoStat = strtolower(trim($ngo['verification_status'] ?? ''));

        if ($userStat === 'suspended' || $ngoStat === 'suspended') {
            header('Location: ' . baseUrl('auth/logout.php'));
            exit;
        }

        $isPending = ($userStat === 'pending' || $ngoStat === 'pending');
        if ($isPending) {
            if ($allowPending) {
                return $ngo;
            } else {
                header('Location: ' . baseUrl('ngo/application-status.php'));
                exit;
            }
        }

        if ($userStat !== 'active' || $ngoStat !== 'approved') {
            if ($allowPending) {
                return $ngo;
            }
            header('Location: ' . baseUrl('ngo/application-status.php'));
            exit;
        }

        return $ngo;
    }
}

if (!function_exists('calculateMoneyKarmaPoints')) {
    function calculateMoneyKarmaPoints($amount) {
        $amount = (float)$amount;
        if ($amount < 10) {
            return 0;
        }
        $blocks = (int)floor($amount / 10.0);
        return $blocks * 10;
    }
}

if (!function_exists('awardKarmaPoints')) {
    function awardKarmaPoints($userId, $points, $sourceType, $sourceId, $description = '') {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        $points = (int)$points;
        $sourceId = (int)$sourceId;
        if ($userId <= 0 || $points <= 0 || !isset($con) || !$con) {
            return false;
        }

        $validSources = ['donation_money', 'donation_resource', 'volunteering', 'event_attendance', 'admin_adjustment'];
        if (!in_array($sourceType, $validSources, true)) {
            return false;
        }

        // Prevent duplicate award for the same activity
        $chkStmt = mysqli_prepare($con, "SELECT id FROM karma_transactions WHERE user_id = ? AND source_type = ? AND source_id = ? AND points > 0 LIMIT 1");
        if (!$chkStmt) {
            return false;
        }
        mysqli_stmt_bind_param($chkStmt, "isi", $userId, $sourceType, $sourceId);
        mysqli_stmt_execute($chkStmt);
        $chkRes = mysqli_stmt_get_result($chkStmt);
        if ($chkRes && mysqli_num_rows($chkRes) > 0) {
            mysqli_stmt_close($chkStmt);
            return false;
        }
        mysqli_stmt_close($chkStmt);

        if (empty($description)) {
            $description = "Awarded " . $points . " Karma points for " . str_replace('_', ' ', $sourceType);
        }

        // Insert into karma_transactions
        $insStmt = mysqli_prepare($con, "INSERT INTO karma_transactions (user_id, points, source_type, source_id, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$insStmt) {
            return false;
        }
        mysqli_stmt_bind_param($insStmt, "iisis", $userId, $points, $sourceType, $sourceId, $description);
        $insOk = mysqli_stmt_execute($insStmt);
        $txId = mysqli_insert_id($con);
        mysqli_stmt_close($insStmt);

        if (!$insOk) {
            return false;
        }

        // Update user_profiles karma_points
        $upProf = mysqli_prepare($con, "INSERT INTO user_profiles (user_id, karma_points, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE karma_points = karma_points + ?, updated_at = NOW()");
        if ($upProf) {
            mysqli_stmt_bind_param($upProf, "iii", $userId, $points, $points);
            mysqli_stmt_execute($upProf);
            mysqli_stmt_close($upProf);
        }

        // Synchronize source table if applicable
        if ($sourceType === 'donation_money' || $sourceType === 'donation_resource') {
            $upCont = mysqli_prepare($con, "UPDATE contributions SET karma_points_awarded = ?, updated_at = NOW() WHERE id = ?");
            if ($upCont) {
                mysqli_stmt_bind_param($upCont, "ii", $points, $sourceId);
                mysqli_stmt_execute($upCont);
                mysqli_stmt_close($upCont);
            }
        } elseif ($sourceType === 'volunteering') {
            $upVol = mysqli_prepare($con, "UPDATE volunteer_activities SET karma_points_awarded = ?, updated_at = NOW() WHERE id = ?");
            if ($upVol) {
                mysqli_stmt_bind_param($upVol, "ii", $points, $sourceId);
                mysqli_stmt_execute($upVol);
                mysqli_stmt_close($upVol);
            }
        }

        return $txId;
    }
}

if (!function_exists('reverseKarmaPoints')) {
    function reverseKarmaPoints($userId, $sourceType, $sourceId, $reason = '') {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        $sourceId = (int)$sourceId;
        if ($userId <= 0 || $sourceId <= 0 || !isset($con) || !$con) {
            return false;
        }

        $validSources = ['donation_money', 'donation_resource', 'volunteering', 'event_attendance', 'admin_adjustment'];
        if (!in_array($sourceType, $validSources, true)) {
            return false;
        }

        // 1. Locate original positive earning transaction
        $chkStmt = mysqli_prepare($con, "SELECT id, points FROM karma_transactions WHERE user_id = ? AND source_type = ? AND source_id = ? AND points > 0 ORDER BY id DESC LIMIT 1");
        if (!$chkStmt) {
            return false;
        }
        mysqli_stmt_bind_param($chkStmt, "isi", $userId, $sourceType, $sourceId);
        mysqli_stmt_execute($chkStmt);
        $chkRes = mysqli_stmt_get_result($chkStmt);
        if (!$chkRes || mysqli_num_rows($chkRes) === 0) {
            mysqli_stmt_close($chkStmt);
            return false; // Never reverse unrewarded contributions
        }
        $origRow = mysqli_fetch_assoc($chkRes);
        $originalPoints = (int)$origRow['points'];
        mysqli_stmt_close($chkStmt);

        if ($originalPoints <= 0) {
            return false;
        }

        // 2. Prevent duplicate reversals
        $revChk = mysqli_prepare($con, "SELECT id FROM karma_transactions WHERE user_id = ? AND source_type = ? AND source_id = ? AND points < 0 LIMIT 1");
        if (!$revChk) {
            return false;
        }
        mysqli_stmt_bind_param($revChk, "isi", $userId, $sourceType, $sourceId);
        mysqli_stmt_execute($revChk);
        $revRes = mysqli_stmt_get_result($revChk);
        if ($revRes && mysqli_num_rows($revRes) > 0) {
            mysqli_stmt_close($revChk);
            return false; // Already reversed
        }
        mysqli_stmt_close($revChk);

        // 3. Record negative transaction preserving the original record
        $reversalPoints = -$originalPoints;
        if (empty($reason)) {
            $reason = "Reversal of " . $originalPoints . " Karma points for " . str_replace('_', ' ', $sourceType);
        }

        $insStmt = mysqli_prepare($con, "INSERT INTO karma_transactions (user_id, points, source_type, source_id, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$insStmt) {
            return false;
        }
        mysqli_stmt_bind_param($insStmt, "iisis", $userId, $reversalPoints, $sourceType, $sourceId, $reason);
        $insOk = mysqli_stmt_execute($insStmt);
        $txId = mysqli_insert_id($con);
        mysqli_stmt_close($insStmt);

        if (!$insOk) {
            return false;
        }

        // 4. Adjust user_profiles karma_points, never allowing negative balance
        $upProf = mysqli_prepare($con, "UPDATE user_profiles SET karma_points = GREATEST(0, CAST(karma_points AS SIGNED) - ?), updated_at = NOW() WHERE user_id = ?");
        if ($upProf) {
            mysqli_stmt_bind_param($upProf, "ii", $originalPoints, $userId);
            mysqli_stmt_execute($upProf);
            mysqli_stmt_close($upProf);
        }

        // 5. Update contributions if applicable
        if ($sourceType === 'donation_money' || $sourceType === 'donation_resource') {
            $upCont = mysqli_prepare($con, "UPDATE contributions SET karma_points_awarded = 0, updated_at = NOW() WHERE id = ?");
            if ($upCont) {
                mysqli_stmt_bind_param($upCont, "i", $sourceId);
                mysqli_stmt_execute($upCont);
                mysqli_stmt_close($upCont);
            }
        }

        return $txId;
    }
}

if (!function_exists('getUserKarmaSummary')) {
    function getUserKarmaSummary($userId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        $summary = [
            'balance' => 0,
            'total_earned' => 0,
            'total_reversed' => 0,
            'transaction_count' => 0,
            'profile_karma' => 0
        ];
        if ($userId <= 0 || !isset($con) || !$con) {
            return $summary;
        }

        $stmt = mysqli_prepare($con, "SELECT 
            COALESCE(SUM(points), 0) AS balance,
            COALESCE(SUM(CASE WHEN points > 0 THEN points ELSE 0 END), 0) AS total_earned,
            COALESCE(SUM(CASE WHEN points < 0 THEN ABS(points) ELSE 0 END), 0) AS total_reversed,
            COUNT(*) AS transaction_count
        FROM karma_transactions WHERE user_id = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $summary['balance'] = (int)$row['balance'];
                $summary['total_earned'] = (int)$row['total_earned'];
                $summary['total_reversed'] = (int)$row['total_reversed'];
                $summary['transaction_count'] = (int)$row['transaction_count'];
            }
            mysqli_stmt_close($stmt);
        }

        $pStmt = mysqli_prepare($con, "SELECT karma_points FROM user_profiles WHERE user_id = ? LIMIT 1");
        if ($pStmt) {
            mysqli_stmt_bind_param($pStmt, "i", $userId);
            mysqli_stmt_execute($pStmt);
            $pRes = mysqli_stmt_get_result($pStmt);
            if ($pRes && $pRow = mysqli_fetch_assoc($pRes)) {
                $summary['profile_karma'] = (int)$pRow['karma_points'];
            }
            mysqli_stmt_close($pStmt);
        }

        return $summary;
    }
}

if (!function_exists('getUserRecentKarmaTransactions')) {
    function getUserRecentKarmaTransactions($userId, $limit = 10) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 10;
        }
        $transactions = [];
        if ($userId <= 0 || !isset($con) || !$con) {
            return $transactions;
        }

        $stmt = mysqli_prepare($con, "SELECT id, points, source_type, source_id, description, created_at FROM karma_transactions WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $userId, $limit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $transactions[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $transactions;
    }
}

if (!function_exists('getCsrfToken')) {
    function getCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('getNgoServiceAreas')) {
    function getNgoServiceAreas($ngoId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $ngoId = (int)$ngoId;
        $areas = [];
        if ($ngoId <= 0 || !isset($con) || !$con) {
            return $areas;
        }
        $stmt = mysqli_prepare($con, "SELECT id, service_area, created_at FROM ngo_service_areas WHERE ngo_id = ? ORDER BY id ASC");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $ngoId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $areas[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $areas;
    }
}

if (!function_exists('getNgoDocuments')) {
    function getNgoDocuments($ngoId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $ngoId = (int)$ngoId;
        $documents = [];
        if ($ngoId <= 0 || !isset($con) || !$con) {
            return $documents;
        }
        $stmt = mysqli_prepare($con, "SELECT id, ngo_id, document_type, document_number, file_path, file_name, file_size, mime_type, created_at FROM ngo_documents WHERE ngo_id = ? ORDER BY created_at DESC, id DESC");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $ngoId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $documents[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $documents;
    }
}

if (!function_exists('syncUserKarmaBalance')) {
    function syncUserKarmaBalance($userId) {
        global $con;
        if (!isset($con) || !$con) {
            @require_once __DIR__ . '/../Database/db_config.php';
        }
        $userId = (int)$userId;
        if ($userId <= 0 || !isset($con) || !$con) {
            return 0;
        }

        $summary = getUserKarmaSummary($userId);
        $balance = max(0, (int)$summary['balance']);

        $stmt = mysqli_prepare($con, "INSERT INTO user_profiles (user_id, karma_points, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE karma_points = ?, updated_at = NOW()");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iii", $userId, $balance, $balance);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $balance;
    }
}


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
            if ($userStatus && $userStatus['status'] === 'pending') {
                header('Location: ' . baseUrl('ngo/application-status.php'));
            } else {
                header('Location: ' . baseUrl('ngo/dashboard.php'));
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

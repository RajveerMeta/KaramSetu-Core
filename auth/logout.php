<?php

require_once __DIR__ . '/../includes/functions.php';

// Remove current demo login data
if (isset($_COOKIE['PHPSESSID'])) {
    // No database/session authentication yet.
}

// Redirect to login page
header('Location: ' . baseUrl('auth/login.php'));
exit;

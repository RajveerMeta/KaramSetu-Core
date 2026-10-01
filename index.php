<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/functions.php';

$page = $_GET['page'] ?? 'home';

$pages = [
    'home' => 'pages/home.php',
    'about' => 'pages/about.php',
    'causes' => 'pages/causes.php',
    'ngos' => 'pages/ngos.php',
    'ngo-profile' => 'pages/ngo-profile.php',
    'events' => 'pages/events.php',
    'event-details' => 'pages/event-details.php',
    'contribute' => 'pages/contribute.php',
    'leaderboard' => 'pages/leaderboard.php'
];

if (!array_key_exists($page, $pages)) {
    $page = 'home'; 
}

$pageFile = __DIR__ . '/' . $pages[$page];

ob_start();

if (file_exists($pageFile)) {
    require $pageFile;
}

$content = ob_get_clean();

if (trim($content) === '') {
    $content = '<div class="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 class="text-3xl font-serif font-bold text-forest mb-4">Page is not available yet.</h2>
        <p class="text-charcoal-light">This is a temporary development fallback.</p>
    </div>';
}

require_once __DIR__ . '/includes/layouts/main.php';


<?php

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

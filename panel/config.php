<?php
/**
 * AAPanel Multi-User Manager
 * Configuration File
 */


$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

// === AAPANEL API CONFIG ===
// Ganti dengan URL dan API Key AAPanel Anda
define('AAPANEL_URL', getenv('AAPANEL_URL') ?: 'http://YOUR_SERVER_IP:8888');
define('AAPANEL_KEY', getenv('AAPANEL_KEY') ?: 'YOUR_AAPANEL_API_KEY');

// === DATABASE CONFIG (untuk user management) ===
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'aapanel_manager');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// === APP CONFIG ===
define('APP_NAME', 'AAPanel Manager');
define('APP_VERSION', '1.0.0');
define('SESSION_LIFETIME', 3600); // 1 jam
define('TIMEZONE', 'Asia/Jakarta');

// === SECURITY ===
define('CSRF_TOKEN_NAME', '_csrf');
define('SECRET_KEY', getenv('SECRET_KEY') ?: 'change-this-secret-key-in-production');

date_default_timezone_set(TIMEZONE);
session_start();

// Error reporting (matikan di production)
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

<?php
/**
 * Tambahkan endpoint csrf_token ke api.php
 * File ini di-include oleh api.php via routing
 */

// Endpoint tambahan: CSRF token
if ($action === 'csrf_token') {
    echo json_encode(['status' => true, 'token' => Auth::csrf()]);
    exit;
}

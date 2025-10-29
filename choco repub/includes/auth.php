<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function isAdminEmail($email) {
    return $email === 'admin@choco.com';
}

function isAdmin() {
    return isset($_SESSION['user_email']) && isAdminEmail($_SESSION['user_email']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /login.php');
        exit();
    }
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}
?>


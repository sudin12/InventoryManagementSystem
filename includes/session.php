<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login first";
        header("Location: login.php");
        exit();
    }
}

function checkAdmin() {
    checkLogin();
    if ($_SESSION['user_type'] !== 'admin') {
        $_SESSION['error'] = "Access denied. Admin privileges required.";
        header("Location: dashboard.php");
        exit();
    }
}

function checkAdminOrStoreKeeper() {
    checkLogin();
    if ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'store_keeper') {
        $_SESSION['error'] = "Access denied. Insufficient privileges.";
        header("Location: dashboard.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}
?>

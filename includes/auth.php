<?php
// Authentication functions
// Student: Vutivi & Karabo

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isVerifiedSeller() {
    return isset($_SESSION['is_seller_verified']) && $_SESSION['is_seller_verified'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php?page=home');
        exit();
    }
}
?>
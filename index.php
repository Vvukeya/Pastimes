<?php
// Pastimes Main Entry Point
// Student: Vutivi & Karabo
// Date: April 2026

session_start();
require_once 'config/database.php';

// Get the page parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Map of pages to their file paths
$page_files = [
    'home' => 'pages/home.php',
    'register' => 'pages/register.php',
    'login' => 'pages/login.php',
    'browse' => 'pages/browse.php',
    'product' => 'pages/product.php',
    'cart' => 'pages/cart.php',
    'checkout' => 'pages/checkout.php',
    'dashboard' => 'pages/dashboard.php',
    'sell' => 'pages/sell.php',
    'order-success' => 'pages/order-success.php'
];

// Pages that don't require login
$public_pages = ['home', 'register', 'login', 'browse', 'product'];

// Check if user needs to be logged in
if (!in_array($page, $public_pages) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Include header with navigation
include 'includes/header.php';

// Load the requested page
if (isset($page_files[$page]) && file_exists($page_files[$page])) {
    include $page_files[$page];
} else {
    include 'pages/home.php';
}

// Include footer
include 'includes/footer.php';
?>
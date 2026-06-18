<?php
session_start();
require_once '../config/database.php';
/** @var mysqli $conn */
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['redirect' => 'index.php?page=login&message=cart']);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

$cart = new ShoppingCart($conn, intval($_SESSION['user_id']));

if ($product_id > 0 && $quantity > 0) {
    if ($cart->AddItem($product_id, $quantity)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid product or quantity']);
}
?>
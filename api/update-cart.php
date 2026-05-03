<?php
session_start();
require_once '../config/database.php';
$conn = mysqli_connect('localhost', 'root', '', 'pastimes');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id > 0) {
    if ($quantity <= 0) {
        removeFromCart($conn, $_SESSION['user_id'], $product_id);
    } else {
        $sql = "UPDATE tblCart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['user_id'], $product_id);
        mysqli_stmt_execute($stmt);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
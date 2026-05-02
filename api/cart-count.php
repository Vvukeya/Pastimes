<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

$count = getCartCount($conn, $_SESSION['user_id']);
echo json_encode(['count' => $count]);
?>
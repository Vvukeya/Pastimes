<?php
// api/follow.php - Handle follow/unfollow AJAX requests
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$following_id = isset($_POST['following_id']) ? intval($_POST['following_id']) : 0;
$follower_id = $_SESSION['user_id'];

if ($following_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit();
}

if ($follower_id == $following_id) {
    echo json_encode(['success' => false, 'error' => 'You cannot follow yourself']);
    exit();
}

$success = false;
$is_following = false;

if ($action === 'follow') {
    $success = followUser($conn, $follower_id, $following_id);
    $is_following = true;
} elseif ($action === 'unfollow') {
    $success = unfollowUser($conn, $follower_id, $following_id);
    $is_following = false;
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit();
}

if ($success) {
    $follower_count = getFollowerCount($conn, $following_id);
    echo json_encode([
        'success' => true,
        'is_following' => $is_following,
        'follower_count' => $follower_count,
        'message' => $is_following ? 'You are now following this seller' : 'You have unfollowed this seller'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
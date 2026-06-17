<?php
// Helper functions
// Student: Vutivi & Karabo

function getCartCount($conn, $user_id) {
    $sql = "SELECT COALESCE(SUM(quantity), 0) as total FROM tblCart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getFeaturedProducts($conn, $limit = 8) {
    $sql = "SELECT * FROM tblClothes WHERE status = 'approved' ORDER BY created_at DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getProductById($conn, $product_id) {
    $sql = "SELECT p.*, u.name as seller_name, u.username as seller_username 
            FROM tblClothes p 
            JOIN tblUser u ON p.seller_id = u.user_id 
            WHERE p.product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function getAllProducts($conn, $filters = []) {
    $sql = "SELECT * FROM tblClothes WHERE status = 'approved'";
    $params = [];
    $types = "";
    
    if (!empty($filters['search'])) {
        $sql .= " AND (title LIKE ? OR brand LIKE ?)";
        $search = "%{$filters['search']}%";
        $params[] = $search;
        $params[] = $search;
        $types .= "ss";
    }
    
    if (!empty($filters['brand'])) {
        $sql .= " AND brand = ?";
        $params[] = $filters['brand'];
        $types .= "s";
    }
    
    if (!empty($filters['category'])) {
        $sql .= " AND category = ?";
        $params[] = $filters['category'];
        $types .= "s";
    }
    
    if (!empty($filters['condition'])) {
        $sql .= " AND `condition` = ?";
        $params[] = $filters['condition'];
        $types .= "s";
    }
    
    if (isset($filters['min_price']) && $filters['min_price'] > 0) {
        $sql .= " AND price >= ?";
        $params[] = $filters['min_price'];
        $types .= "d";
    }
    
    if (isset($filters['max_price']) && $filters['max_price'] > 0) {
        $sql .= " AND price <= ?";
        $params[] = $filters['max_price'];
        $types .= "d";
    }
    
    $order = isset($filters['sort']) ? $filters['sort'] : 'newest';
    switch ($order) {
        case 'price_low':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_high':
            $sql .= " ORDER BY price DESC";
            break;
        case 'oldest':
            $sql .= " ORDER BY created_at ASC";
            break;
        default:
            $sql .= " ORDER BY created_at DESC";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function addToCart($conn, $user_id, $product_id, $quantity = 1) {
    $sql = "SELECT cart_id, quantity FROM tblCart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $new_quantity = $row['quantity'] + $quantity;
        $sql = "UPDATE tblCart SET quantity = ? WHERE cart_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $new_quantity, $row['cart_id']);
    } else {
        $sql = "INSERT INTO tblCart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $quantity);
    }
    
    return mysqli_stmt_execute($stmt);
}

function removeFromCart($conn, $user_id, $product_id) {
    $sql = "DELETE FROM tblCart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    return mysqli_stmt_execute($stmt);
}

function getCartItems($conn, $user_id) {
    $sql = "SELECT c.*, p.title, p.price, p.image_url, p.brand 
            FROM tblCart c 
            JOIN tblClothes p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getCartTotal($conn, $user_id) {
    $sql = "SELECT COALESCE(SUM(c.quantity * p.price), 0) as total 
            FROM tblCart c 
            JOIN tblClothes p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function createOrder($conn, $user_id, $delivery_address, $payment_method) {
    $total = getCartTotal($conn, $user_id);
    $order_number = generateOrderNumber();
    
    mysqli_begin_transaction($conn);
    
    try {
        $sql = "INSERT INTO tblAorder (user_id, order_number, total_amount, delivery_address, payment_method, payment_status, status) 
                VALUES (?, ?, ?, ?, ?, 'pending', 'pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isdss", $user_id, $order_number, $total, $delivery_address, $payment_method);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
        
        $cart_items = getCartItems($conn, $user_id);
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $sql = "INSERT INTO tblOrderItems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt);
        }
        
        $sql = "DELETE FROM tblCart WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        return $order_number;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= 8;
}

function getUserOrders($conn, $user_id) {
    $sql = "SELECT * FROM tblAorder WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getUserListings($conn, $user_id) {
    $sql = "SELECT * FROM tblClothes WHERE seller_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function getUserMessages($conn, $user_id) {
    $sql = "SELECT m.*, u.username as sender_name, p.title as product_title 
            FROM tblMessages m 
            JOIN tblUser u ON m.sender_id = u.user_id 
            LEFT JOIN tblClothes p ON m.product_id = p.product_id 
            WHERE m.receiver_id = ? OR m.sender_id = ?
            ORDER BY m.created_at DESC LIMIT 20";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function sendMessage($conn, $sender_id, $receiver_id, $product_id, $message) {
    $sql = "INSERT INTO tblMessages (sender_id, receiver_id, product_id, message_text, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $sender_id, $receiver_id, $product_id, $message);
    return mysqli_stmt_execute($stmt);
}

// Get product image URL with fallback
function getProductImage($image_url) {
    if (!empty($image_url) && file_exists(__DIR__ . '/../' . $image_url)) {
        return $image_url;
    } elseif (!empty($image_url) && strpos($image_url, 'http') === 0) {
        return $image_url;
    } else {
        return 'images/placeholder.jpg';
    }
}

// Display product image with proper path
function displayProductImage($image_url, $alt = 'Product Image', $class = '') {
    $src = getProductImage($image_url);
    return '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '" onerror="this.src=\'images/placeholder.jpg\'">';
}

// ============ FOLLOW/UNFOLLOW FUNCTIONS ============

/**
 * Check if a user is following another user
 */
function isFollowing($conn, $follower_id, $following_id) {
    $sql = "SELECT follow_id FROM tblFollows WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

/**
 * Follow a user (seller)
 */
function followUser($conn, $follower_id, $following_id) {
    if ($follower_id == $following_id) {
        return false;
    }
    $sql = "INSERT INTO tblFollows (follower_id, following_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
    return mysqli_stmt_execute($stmt);
}

/**
 * Unfollow a user
 */
function unfollowUser($conn, $follower_id, $following_id) {
    $sql = "DELETE FROM tblFollows WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
    return mysqli_stmt_execute($stmt);
}

/**
 * Get follower count for a user
 */
function getFollowerCount($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM tblFollows WHERE following_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? 0;
}

/**
 * Get following count for a user
 */
function getFollowingCount($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM tblFollows WHERE follower_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? 0;
}

/**
 * Get all followers of a user
 */
function getUserFollowers($conn, $user_id) {
    $sql = "SELECT u.*, f.created_at as followed_since 
            FROM tblFollows f 
            JOIN tblUser u ON f.follower_id = u.user_id 
            WHERE f.following_id = ? 
            ORDER BY f.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Get all users that a user follows
 */
function getUserFollowing($conn, $user_id) {
    $sql = "SELECT u.*, f.created_at as followed_since 
            FROM tblFollows f 
            JOIN tblUser u ON f.following_id = u.user_id 
            WHERE f.follower_id = ? 
            ORDER BY f.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Get products from followed sellers for feed
 */
function getFollowedSellersProducts($conn, $user_id, $limit = 20) {
    $sql = "SELECT p.*, u.username as seller_name, u.name as seller_first_name, u.surname as seller_last_name
            FROM tblClothes p
            JOIN tblFollows f ON p.seller_id = f.following_id
            JOIN tblUser u ON p.seller_id = u.user_id
            WHERE f.follower_id = ? AND p.status = 'approved'
            ORDER BY p.created_at DESC
            LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// ============ CHAT FILE SHARING FUNCTIONS ============

/**
 * Send a message with file attachment (supports admin replies)
 */
function sendMessageWithFile($conn, $sender_id, $receiver_id, $product_id, $message, $file_name = null, $file_path = null, $file_type = null, $file_size = null, $is_admin_reply = 0) {
    $sql = "INSERT INTO tblMessages (sender_id, receiver_id, product_id, message_text, file_name, file_path, file_type, file_size, is_admin_reply, created_at, is_read) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiissssii", $sender_id, $receiver_id, $product_id, $message, $file_name, $file_path, $file_type, $file_size, $is_admin_reply);
    return mysqli_stmt_execute($stmt);
}

/**
 * Send admin reply - stores in tblAdminReplies AND creates a message in tblMessages
 */
function sendAdminReply($conn, $message_id, $admin_id, $reply_text, $file_name = null, $file_path = null, $file_type = null, $file_size = null) {
    // First, get the original message to know sender and receiver
    $sql = "SELECT sender_id, receiver_id, product_id FROM tblMessages WHERE message_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $original = mysqli_fetch_assoc($result);
    
    if (!$original) {
        return false;
    }
    
    // Store admin reply in tblAdminReplies
    $sql1 = "INSERT INTO tblAdminReplies (message_id, admin_id, reply_text, file_name, file_path, file_type, file_size, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "iissssi", $message_id, $admin_id, $reply_text, $file_name, $file_path, $file_type, $file_size);
    
    if (!mysqli_stmt_execute($stmt1)) {
        return false;
    }
    
    // Create a message in tblMessages that the user can see (admin sends message to the user)
    $receiver_id = $original['sender_id'];
    $sender_id = $admin_id;
    $product_id = $original['product_id'];
    $is_admin_reply = 1;
    
    return sendMessageWithFile($conn, $sender_id, $receiver_id, $product_id, $reply_text, $file_name, $file_path, $file_type, $file_size, $is_admin_reply);
}

/**
 * Get all conversations for admin monitoring
 */
function getAllConversations($conn) {
    $sql = "SELECT DISTINCT 
            LEAST(m.sender_id, m.receiver_id) as user1_id,
            GREATEST(m.sender_id, m.receiver_id) as user2_id,
            MAX(m.created_at) as last_message_time,
            (SELECT message_text FROM tblMessages WHERE (sender_id = LEAST(m.sender_id, m.receiver_id) AND receiver_id = GREATEST(m.sender_id, m.receiver_id)) OR (sender_id = GREATEST(m.sender_id, m.receiver_id) AND receiver_id = LEAST(m.sender_id, m.receiver_id)) ORDER BY created_at DESC LIMIT 1) as last_message,
            u1.username as user1_name,
            u1.name as user1_first,
            u1.surname as user1_last,
            u2.username as user2_name,
            u2.name as user2_first,
            u2.surname as user2_last
            FROM tblMessages m
            JOIN tblUser u1 ON u1.user_id = LEAST(m.sender_id, m.receiver_id)
            JOIN tblUser u2 ON u2.user_id = GREATEST(m.sender_id, m.receiver_id)
            GROUP BY LEAST(m.sender_id, m.receiver_id), GREATEST(m.sender_id, m.receiver_id)
            ORDER BY last_message_time DESC";
    return mysqli_query($conn, $sql);
}

/**
 * Get all messages between two users (including admin replies)
 */
function getConversationMessages($conn, $user1_id, $user2_id) {
    $sql = "SELECT m.*, 
            u.username as sender_name, u.name as sender_first, u.surname as sender_last,
            u2.username as receiver_name,
            a.reply_id, a.reply_text as admin_reply_text, a.created_at as admin_reply_time,
            a.file_name as admin_file_name, a.file_path as admin_file_path,
            a.file_type as admin_file_type, a.file_size as admin_file_size
            FROM tblMessages m
            JOIN tblUser u ON m.sender_id = u.user_id
            JOIN tblUser u2 ON m.receiver_id = u2.user_id
            LEFT JOIN tblAdminReplies a ON m.message_id = a.message_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Get messages for a specific user (shows both user and admin messages)
 */
function getUserMessagesWithAdmin($conn, $user_id, $limit = 20) {
    $sql = "SELECT m.*, 
            u.username as sender_name, u.name as sender_first, u.surname as sender_last,
            p.title as product_title,
            a.reply_id, a.reply_text as admin_reply_text, a.created_at as admin_reply_time,
            a.file_name as admin_file_name, a.file_path as admin_file_path,
            a.file_type as admin_file_type, a.file_size as admin_file_size,
            CASE 
                WHEN m.is_admin_reply = 1 THEN 'admin'
                WHEN m.sender_id = ? THEN 'sent'
                ELSE 'received'
            END as message_type
            FROM tblMessages m
            JOIN tblUser u ON m.sender_id = u.user_id
            LEFT JOIN tblClothes p ON m.product_id = p.product_id
            LEFT JOIN tblAdminReplies a ON m.message_id = a.message_id
            WHERE m.receiver_id = ? OR m.sender_id = ?
            ORDER BY m.created_at DESC
            LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $user_id, $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/**
 * Get unread messages count for admin
 */
function getUnreadMessagesCount($conn) {
    $sql = "SELECT COUNT(*) as count FROM tblMessages WHERE is_read = 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? 0;
}

/**
 * Mark message as read
 */
function markMessageAsRead($conn, $message_id) {
    $sql = "UPDATE tblMessages SET is_read = 1 WHERE message_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    return mysqli_stmt_execute($stmt);
}

/**
 * Upload file for chat
 */
function uploadChatFile($file, $upload_dir = null) {
    if ($upload_dir === null) {
        $upload_dir = __DIR__ . '/../uploads/chat_files/';
    }
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/zip'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;
    $relative_path = 'uploads/chat_files/' . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return [
            'success' => true,
            'file_name' => $file['name'],
            'file_path' => $relative_path,
            'file_type' => $file['type'],
            'file_size' => $file['size']
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Get file icon based on file type
 */
function getFileIcon($file_type) {
    if (strpos($file_type, 'image') !== false) return 'fa-image';
    if (strpos($file_type, 'pdf') !== false) return 'fa-file-pdf';
    if (strpos($file_type, 'msword') !== false || strpos($file_type, 'document') !== false) return 'fa-file-word';
    if (strpos($file_type, 'text') !== false) return 'fa-file-alt';
    if (strpos($file_type, 'zip') !== false) return 'fa-file-archive';
    return 'fa-file';
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}
?>
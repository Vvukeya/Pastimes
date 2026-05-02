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
?>
<?php
// User Dashboard
// Student: Vutivi & Karabo
// Date: April 2026

/** @var mysqli $conn */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Get the active tab from URL parameter
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';

// Get user data
$user_id = $_SESSION['user_id'];

// Get user details for profile
$user_sql = "SELECT user_id, name, surname, email, username, phone, delivery_address, is_verified, is_seller_verified, role, created_at, last_login FROM tblUser WHERE user_id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));

// Get orders
$orders_sql = "SELECT order_id, user_id, order_number, total_amount, delivery_address, status, payment_method, payment_status, tracking_number, created_at FROM tblAorder WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders = mysqli_stmt_get_result($orders_stmt);

$purchase_total_sql = "SELECT COALESCE(SUM(total_amount), 0) AS total_spent, COUNT(*) AS total_orders FROM tblAorder WHERE user_id = ?";
$purchase_total_stmt = mysqli_prepare($conn, $purchase_total_sql);
mysqli_stmt_bind_param($purchase_total_stmt, "i", $user_id);
mysqli_stmt_execute($purchase_total_stmt);
$purchase_summary = mysqli_fetch_assoc(mysqli_stmt_get_result($purchase_total_stmt));

// Get user's listings
$listings_sql = "SELECT product_id, seller_id, title, brand, description, price, `condition`, category, size, colour, image_url, status, views, created_at, sold_date FROM tblClothes WHERE seller_id = ? ORDER BY created_at DESC";
$listings_stmt = mysqli_prepare($conn, $listings_sql);
mysqli_stmt_bind_param($listings_stmt, "i", $user_id);
mysqli_stmt_execute($listings_stmt);
$listings = mysqli_stmt_get_result($listings_stmt);

// Get messages with admin replies using updated function
$messages = getUserMessagesWithAdmin($conn, $user_id, 20);

// Get followed sellers products for feed
$followed_products = getFollowedSellersProducts($conn, $user_id, 20);

// Get follow counts
$follower_count = getFollowerCount($conn, $user_id);
$following_count = getFollowingCount($conn, $user_id);

// Get followers list
$followers = getUserFollowers($conn, $user_id);
$following_list = getUserFollowing($conn, $user_id);

// Handle profile update
$profile_success = false;
$profile_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitizeInput($_POST['name']);
    $surname = sanitizeInput($_POST['surname']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $delivery_address = sanitizeInput($_POST['delivery_address']);
    
    if (empty($name) || empty($surname) || empty($email)) {
        $profile_error = 'Name, surname and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = 'Please enter a valid email address.';
    } else {
        $update_sql = "UPDATE tblUser SET name = ?, surname = ?, email = ?, phone = ?, delivery_address = ? WHERE user_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssssi", $name, $surname, $email, $phone, $delivery_address, $user_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $_SESSION['name'] = $name;
            $_SESSION['surname'] = $surname;
            $_SESSION['email'] = $email;
            $profile_success = true;
            // Refresh user data
            $user_data['name'] = $name;
            $user_data['surname'] = $surname;
            $user_data['email'] = $email;
            $user_data['phone'] = $phone;
            $user_data['delivery_address'] = $delivery_address;
        } else {
            $profile_error = 'Failed to update profile. Please try again.';
        }
    }
}

// Handle sending a reply with file attachment
$reply_sent = false;
$reply_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $reply_message = sanitizeInput($_POST['reply_message']);
    
    $file_name = null;
    $file_path = null;
    $file_type = null;
    $file_size = null;
    
    // Handle file upload
    if (isset($_FILES['reply_file']) && $_FILES['reply_file']['error'] == UPLOAD_ERR_OK) {
        $upload_result = uploadChatFile($_FILES['reply_file']);
        if ($upload_result['success']) {
            $file_name = $upload_result['file_name'];
            $file_path = $upload_result['file_path'];
            $file_type = $upload_result['file_type'];
            $file_size = $upload_result['file_size'];
        } else {
            $reply_error = $upload_result['error'];
        }
    }
    
    if (empty($reply_message) && !$file_name) {
        $reply_error = 'Please enter a message or attach a file.';
    } elseif (empty($reply_error)) {
        if (sendMessageWithFile($conn, $user_id, $receiver_id, $product_id, $reply_message, $file_name, $file_path, $file_type, $file_size)) {
            $reply_sent = true;
            // Refresh messages
            $messages = getUserMessagesWithAdmin($conn, $user_id, 20);
        } else {
            $reply_error = 'Failed to send message. Please try again.';
        }
    }
}

// Show error message if redirected from sell page
if (isset($_GET['error']) && $_GET['error'] == 'not_verified') {
    $verify_error = 'You need to be verified as a seller to sell items. Contact an administrator.';
}
?>

<div class="container" style="padding: 40px 0;">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
                <h3><?php echo htmlspecialchars($user_data['name'] . ' ' . $user_data['surname']); ?></h3>
                <p>@<?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 10px;">
                    <span style="cursor: pointer;" onclick="showFollowersModal()">
                        <i class="fas fa-users"></i> <strong><?php echo $follower_count; ?></strong> followers
                    </span>
                    <span style="cursor: pointer;" onclick="showFollowingModal()">
                        <i class="fas fa-user-friends"></i> <strong><?php echo $following_count; ?></strong> following
                    </span>
                </div>
                <?php if ($user_data['is_seller_verified'] == 1): ?>
                    <span class="status-badge status-approved" style="margin-top: 10px; display: inline-block;">
                        <i class="fas fa-check-circle"></i> Verified Seller
                    </span>
                <?php else: ?>
                    <span class="status-badge status-pending" style="margin-top: 10px; display: inline-block;">
                        <i class="fas fa-clock"></i> Not a Seller
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-nav">
                <a href="index.php?page=dashboard&tab=orders" class="<?php echo $active_tab == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
                <a href="index.php?page=dashboard&tab=following" class="<?php echo $active_tab == 'following' ? 'active' : ''; ?>">
                    <i class="fas fa-rss"></i> Following Feed
                </a>
                <?php if ($user_data['is_seller_verified'] == 1): ?>
                    <a href="index.php?page=dashboard&tab=listings" class="<?php echo $active_tab == 'listings' ? 'active' : ''; ?>">
                        <i class="fas fa-tag"></i> My Listings
                    </a>
                <?php endif; ?>
                <a href="index.php?page=dashboard&tab=messages" class="<?php echo $active_tab == 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                <a href="index.php?page=dashboard&tab=profile" class="<?php echo $active_tab == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> Profile
                </a>
                
                <?php if ($user_data['is_seller_verified'] == 0 && $user_data['role'] != 'admin'): ?>
                    <hr>
                    <div style="padding: 12px; background: var(--warm-beige); border-radius: var(--radius); margin-top: 10px;">
                        <small style="color: var(--grey);">
                            <i class="fas fa-info-circle"></i> Want to sell? Contact admin to become a verified seller.
                        </small>
                    </div>
                <?php endif; ?>
                
                <?php if ($user_data['is_seller_verified'] == 1): ?>
                    <hr>
                    <a href="index.php?page=sell" style="background: var(--pastime-green); color: white; margin-top: 10px;">
                        <i class="fas fa-plus-circle"></i> Sell New Item
                    </a>
                <?php endif; ?>
                
                <hr>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="dashboard-content">
            
            <?php if (isset($verify_error)): ?>
                <div style="background: #FFF3E0; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #FF9800;">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $verify_error; ?>
                </div>
            <?php endif; ?>
            
            <!-- ORDERS TAB -->
            <?php if ($active_tab == 'orders'): ?>
                <h2 style="margin-bottom: 20px;"><i class="fas fa-shopping-bag"></i> My Orders</h2>
                <div class="stats-grid" style="margin-bottom: 20px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <div class="stat-number"><?php echo intval($purchase_summary['total_orders'] ?? 0); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Purchases</h3>
                        <div class="stat-number">R <?php echo number_format($purchase_summary['total_spent'] ?? 0, 2); ?></div>
                    </div>
                </div>
                <?php if (mysqli_num_rows($orders) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                                    <tr>
                                        <td><strong><?php echo $order['order_number']; ?></strong></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>R <?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="alert('Order Details:\nOrder: <?php echo $order['order_number']; ?>\nTotal: R <?php echo number_format($order['total_amount'], 2); ?>\nStatus: <?php echo $order['status']; ?>\nDelivery: <?php echo htmlspecialchars(substr($order['delivery_address'], 0, 50)); ?>...')" class="btn-outline" style="padding: 4px 12px; font-size: 12px;">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-shopping-bag" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
                        <h3>No orders yet</h3>
                        <p style="color: var(--grey); margin-bottom: 20px;">You haven't placed any orders yet.</p>
                        <a href="index.php?page=browse" class="btn-primary">Start Shopping</a>
                    </div>
                <?php endif; ?>
            
            <!-- FOLLOWING FEED TAB -->
            <?php elseif ($active_tab == 'following'): ?>
                <h2 style="margin-bottom: 20px;"><i class="fas fa-rss"></i> Feed from Sellers You Follow</h2>
                
                <?php if (mysqli_num_rows($followed_products) > 0): ?>
                    <div class="products-grid" style="grid-template-columns: repeat(3, 1fr);">
                        <?php while ($product = mysqli_fetch_assoc($followed_products)): ?>
                            <div class="product-card">
                                <a href="index.php?page=product&id=<?php echo $product['product_id']; ?>" style="text-decoration: none; color: inherit;">
                                    <div class="product-image">
                                        <img src="<?php echo getProductImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.src='images/placeholder.jpg'">
                                        <?php if ($product['condition'] == 'New'): ?>
                                            <span style="position: absolute; top: 10px; left: 10px; background: var(--gold); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                                        <div class="product-price">R <?php echo number_format($product['price'], 2); ?></div>
                                        <small style="color: var(--grey); display: block; margin-top: 5px;">
                                            <i class="fas fa-store"></i> by <?php echo htmlspecialchars($product['seller_name']); ?>
                                        </small>
                                    </div>
                                </a>
                                <div style="padding: 0 16px 16px 16px;">
                                    <button onclick="addToCartWithPopup(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['title']); ?>', <?php echo $product['price']; ?>)" class="add-to-cart-btn" style="background: var(--pastime-green); color: white; border: none; padding: 10px; border-radius: var(--radius); cursor: pointer; width: 100%;">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-rss" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
                        <h3>No followed sellers yet</h3>
                        <p style="color: var(--grey); margin-bottom: 20px;">Follow sellers to see their latest products here.</p>
                        <a href="index.php?page=browse" class="btn-primary">Browse Products</a>
                    </div>
                <?php endif; ?>
            
            <!-- LISTINGS TAB -->
            <?php elseif ($active_tab == 'listings' && $user_data['is_seller_verified'] == 1): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                    <h2><i class="fas fa-tag"></i> My Listings</h2>
                    <a href="index.php?page=sell" class="btn-primary">
                        <i class="fas fa-plus"></i> List New Item
                    </a>
                </div>
                
                <?php if (mysqli_num_rows($listings) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Date Listed</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($listing = mysqli_fetch_assoc($listings)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($listing['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($listing['brand']); ?></td>
                                        <td>R <?php echo number_format($listing['price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $listing['status']; ?>">
                                                <?php echo ucfirst($listing['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $listing['views']; ?> views</td>
                                        <td><?php echo date('M d, Y', strtotime($listing['created_at'])); ?></td>
                                        <td>
                                            <a href="index.php?page=product&id=<?php echo $listing['product_id']; ?>" target="_blank" class="btn-outline" style="padding: 4px 12px; font-size: 12px; text-decoration: none;">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-tag" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
                        <h3>No listings yet</h3>
                        <p style="color: var(--grey); margin-bottom: 20px;">You haven't listed any items for sale yet.</p>
                        <a href="index.php?page=sell" class="btn-primary">Sell Your First Item</a>
                    </div>
                <?php endif; ?>
            
            <!-- MESSAGES TAB WITH ADMIN REPLY SUPPORT AND FILE SHARING -->
            <?php elseif ($active_tab == 'messages'): ?>
                <h2 style="margin-bottom: 20px;"><i class="fas fa-envelope"></i> Messages</h2>
                
                <?php if ($reply_sent): ?>
                    <div class="message-card-body admin" style="margin-bottom: 20px;">
                        <i class="fas fa-check-circle"></i> Reply sent successfully!
                    </div>
                <?php endif; ?>
                
                <?php if ($reply_error): ?>
                    <div class="message-card-body" style="background: #FFEBEE; border-color: #FBCACA; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $reply_error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (mysqli_num_rows($messages) > 0): ?>
                    <div class="messages-list">
                        <?php while ($msg = mysqli_fetch_assoc($messages)): 
                            $is_sender = ($msg['sender_id'] == $user_id);
                            $is_admin = ($msg['is_admin_reply'] == 1);
                            $is_received = (!$is_sender && !$is_admin);
                        ?>
                            <div class="message-item <?php echo ($is_received && $msg['is_read'] == 0) ? 'unread' : ''; ?>" id="message-<?php echo $msg['message_id']; ?>">
                                <div class="message-header">
                                    <div class="message-meta">
                                        <?php if ($is_admin): ?>
                                            <span class="message-pill message-pill-admin"><i class="fas fa-user-shield"></i> Admin</span>
                                        <?php else: ?>
                                            <span class="message-pill message-pill-user"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($msg['sender_name']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($is_sender): ?>
                                            <span class="message-pill message-pill-user">You</span>
                                        <?php endif; ?>
                                        <?php if ($is_admin): ?>
                                            <span class="message-pill message-pill-admin"><i class="fas fa-star"></i> Admin Response</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="message-meta">
                                        <span><i class="far fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                                    </div>
                                </div>

                                <?php if ($msg['product_title']): ?>
                                    <div class="message-product-tag">
                                        <i class="fas fa-tag"></i>
                                        <span>Regarding <a href="index.php?page=product&id=<?php echo $msg['product_id']; ?>" style="color: var(--pastime-green); text-decoration: none; font-weight: 600;"><?php echo htmlspecialchars($msg['product_title']); ?></a></span>
                                    </div>
                                <?php endif; ?>

                                <div class="message-content <?php echo $is_admin ? 'admin' : ''; ?>">
                                    <?php if (!empty($msg['message_text'])): ?>
                                        <p><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($msg['file_path'])): ?>
                                        <a href="<?php echo $msg['file_path']; ?>" target="_blank" class="message-attachment">
                                            <i class="fas <?php echo getFileIcon($msg['file_type']); ?>" style="font-size: 18px; color: var(--pastime-green);"></i>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600;"><?php echo htmlspecialchars($msg['file_name']); ?></div>
                                                <div style="font-size: 11px; color: var(--grey);"><?php echo formatFileSize($msg['file_size']); ?></div>
                                            </div>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <?php if ($is_received && !$is_admin): ?>
                                    <div style="margin-top: 12px;">
                                        <button onclick="toggleReplyForm(<?php echo $msg['message_id']; ?>, <?php echo $msg['sender_id']; ?>, <?php echo $msg['product_id'] ?: 'null'; ?>)" class="btn-outline" style="padding: 8px 16px; font-size: 13px;">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        
                                        <!-- Reply Form with File Upload (hidden by default) -->
                                        <div id="reply-form-<?php echo $msg['message_id']; ?>" class="reply-form-shell" style="display: none;">
                                            <form method="POST" action="index.php?page=dashboard&tab=messages" enctype="multipart/form-data" class="reply-form">
                                                <input type="hidden" name="receiver_id" value="<?php echo $msg['sender_id']; ?>">
                                                <input type="hidden" name="product_id" value="<?php echo $msg['product_id']; ?>">
                                                <div class="form-group">
                                                    <textarea name="reply_message" rows="3" placeholder="Type your reply here..."></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="file-label" style="display: inline-flex; align-items: center; gap: 8px; background: var(--warm-beige); padding: 8px 16px; border-radius: var(--radius); cursor: pointer;">
                                                        <i class="fas fa-paperclip"></i> Attach File
                                                        <input type="file" name="reply_file" style="display: none;" onchange="updateFileName(this, 'fileName_<?php echo $msg['message_id']; ?>')">
                                                    </label>
                                                    <span id="fileName_<?php echo $msg['message_id']; ?>" class="selected-file" style="margin-left: 10px; font-size: 12px; color: var(--grey);">No file selected (max 5MB)</span>
                                                </div>
                                                <div class="reply-form-actions">
                                                    <button type="submit" name="send_reply" class="btn-primary" style="padding: 8px 20px;">
                                                        <i class="fas fa-paper-plane"></i> Send Reply
                                                    </button>
                                                    <button type="button" onclick="toggleReplyForm(<?php echo $msg['message_id']; ?>)" class="btn-outline" style="padding: 8px 20px;">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-envelope-open" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
                        <h3>No messages yet</h3>
                        <p style="color: var(--grey);">When you receive messages from sellers or buyers, they will appear here.</p>
                        <p style="color: var(--grey); margin-top: 10px;">You can also message sellers from their product pages.</p>
                    </div>
                <?php endif; ?>
            
            <!-- PROFILE TAB -->
            <?php elseif ($active_tab == 'profile'): ?>
                <h2 style="margin-bottom: 20px;"><i class="fas fa-user"></i> Profile Settings</h2>
                
                <?php if ($profile_success): ?>
                    <div style="background: #E8F5E9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50;">
                        <i class="fas fa-check-circle"></i> Profile updated successfully!
                    </div>
                <?php endif; ?>
                
                <?php if ($profile_error): ?>
                    <div style="background: #FFEBEE; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #F44336;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $profile_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="index.php?page=dashboard&tab=profile">
                    <div class="form-group">
                        <label for="name">First Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="surname">Last Name *</label>
                        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user_data['surname']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username (cannot be changed)</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">Default Delivery Address</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" placeholder="Street address, city, postal code, province"><?php echo htmlspecialchars($user_data['delivery_address'] ?? ''); ?></textarea>
                        <small style="color: var(--grey);">This address will be pre-filled during checkout.</small>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Followers Modal -->
<div id="followersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-users"></i> Followers</h3>
            <span class="close" onclick="closeModal('followersModal')">&times;</span>
        </div>
        <div class="modal-body">
            <?php if (mysqli_num_rows($followers) > 0): ?>
                <?php while ($follower = mysqli_fetch_assoc($followers)): ?>
                    <div class="user-list-item">
                        <div class="user-avatar-small">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($follower['name'] . ' ' . $follower['surname']); ?></div>
                            <div class="user-username">@<?php echo htmlspecialchars($follower['username']); ?></div>
                        </div>
                        <a href="index.php?page=seller&id=<?php echo $follower['user_id']; ?>" class="btn-outline-small">View Profile</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-users" style="font-size: 48px; color: var(--grey); margin-bottom: 15px;"></i>
                    <p>No followers yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Following Modal -->
<div id="followingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-friends"></i> Following</h3>
            <span class="close" onclick="closeModal('followingModal')">&times;</span>
        </div>
        <div class="modal-body">
            <?php if (mysqli_num_rows($following_list) > 0): ?>
                <?php while ($following = mysqli_fetch_assoc($following_list)): ?>
                    <div class="user-list-item">
                        <div class="user-avatar-small">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($following['name'] . ' ' . $following['surname']); ?></div>
                            <div class="user-username">@<?php echo htmlspecialchars($following['username']); ?></div>
                        </div>
                        <a href="index.php?page=seller&id=<?php echo $following['user_id']; ?>" class="btn-outline-small">View Profile</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-user-friends" style="font-size: 48px; color: var(--grey); margin-bottom: 15px;"></i>
                    <p>Not following anyone yet.</p>
                    <a href="index.php?page=browse" class="btn-primary" style="margin-top: 15px;">Browse Sellers</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .dashboard-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px;
    }
    
    .dashboard-sidebar {
        background: var(--warm-beige);
        border-radius: var(--radius);
        padding: 24px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }
    
    .user-avatar {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--light-grey);
    }
    
    .user-avatar i {
        font-size: 64px;
        color: var(--pastime-green);
        margin-bottom: 12px;
    }
    
    .dashboard-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .dashboard-nav a {
        padding: 12px 16px;
        text-decoration: none;
        color: var(--charcoal);
        border-radius: var(--radius);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .dashboard-nav a:hover {
        background: white;
        color: var(--pastime-green);
    }
    
    .dashboard-nav a.active {
        background: white;
        color: var(--pastime-green);
        font-weight: 600;
    }
    
    .dashboard-content {
        background: white;
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .products-grid {
        display: grid;
        gap: 30px;
        margin-top: 20px;
    }
    
    .product-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .product-image {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.05);
    }
    
    .product-info {
        padding: 16px;
    }
    
    .product-brand {
        font-size: 12px;
        color: var(--grey);
        text-transform: uppercase;
    }
    
    .product-title {
        font-size: 16px;
        font-weight: 600;
        margin: 8px 0;
    }
    
    .product-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--pastime-green);
    }
    
    .add-to-cart-btn:hover {
        background: var(--pastime-green-dark) !important;
    }
    
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--light-grey);
    }
    
    .admin-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-approved, .status-paid, .status-delivered {
        background: #e8f5e9;
        color: #4caf50;
    }
    
    .status-pending {
        background: #fff3e0;
        color: #ff9800;
    }
    
    .status-rejected, .status-cancelled {
        background: #ffebee;
        color: #f44336;
    }
    
    .btn-outline {
        background: transparent;
        color: var(--pastime-green);
        border: 1px solid var(--pastime-green);
        border-radius: var(--radius);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
        background: var(--pastime-green);
        color: white;
    }
    
    .btn-outline-small {
        background: transparent;
        color: var(--pastime-green);
        border: 1px solid var(--pastime-green);
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-outline-small:hover {
        background: var(--pastime-green);
        color: white;
    }
    
    .btn-primary {
        background: var(--pastime-green);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius);
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: var(--pastime-green-dark);
        transform: translateY(-2px);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--light-grey);
        border-radius: var(--radius);
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--pastime-green);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .message-item {
        transition: all 0.3s ease;
    }
    
    .message-item:hover {
        background: #fafafa;
    }
    
    .reply-form textarea:focus {
        outline: none;
        border-color: var(--pastime-green);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .file-attachment {
        transition: all 0.3s ease;
    }
    
    .file-attachment:hover {
        background: #e0e0e0 !important;
    }
    
    .file-label {
        transition: all 0.3s ease;
    }
    
    .file-label:hover {
        background: var(--light-grey) !important;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s ease;
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        width: 90%;
        max-width: 500px;
        border-radius: var(--radius);
        box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        animation: slideDown 0.3s ease;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--light-grey);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
    }
    
    .close {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: var(--grey);
        transition: color 0.3s ease;
    }
    
    .close:hover {
        color: var(--error-red);
    }
    
    .modal-body {
        padding: 20px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .user-list-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        border-bottom: 1px solid var(--light-grey);
        transition: background 0.3s ease;
    }
    
    .user-list-item:hover {
        background: var(--warm-beige);
    }
    
    .user-avatar-small i {
        font-size: 40px;
        color: var(--pastime-green);
    }
    
    .user-info {
        flex: 1;
    }
    
    .user-name {
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .user-username {
        font-size: 12px;
        color: var(--grey);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @media (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            grid-template-columns: 1fr;
        }
        .dashboard-sidebar {
            position: static;
        }
        .admin-table {
            font-size: 12px;
        }
        .admin-table th,
        .admin-table td {
            padding: 8px;
        }
        .products-grid {
            grid-template-columns: 1fr !important;
        }
        .modal-content {
            margin: 15% auto;
            width: 95%;
        }
    }
</style>

<script>
function toggleReplyForm(messageId, receiverId, productId) {
    var form = document.getElementById('reply-form-' + messageId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        form.style.display = 'none';
    }
}

function updateFileName(input, spanId) {
    if (input.files && input.files[0]) {
        document.getElementById(spanId).textContent = input.files[0].name;
    } else {
        document.getElementById(spanId).textContent = 'No file selected (max 5MB)';
    }
}

function addToCartWithPopup(productId, productName, productPrice) {
    var quantity = 1;
    var totalPrice = productPrice * quantity;
    var userConfirmed = confirm(
        "🛒 Add to Cart\n\n" +
        "Product: " + productName + "\n" +
        "Price: R " + parseFloat(productPrice).toFixed(2) + "\n" +
        "Total: R " + totalPrice.toFixed(2) + "\n\n" +
        "Click OK to add this item to your cart."
    );
    if (userConfirmed) {
        fetch('api/add-to-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId + '&quantity=' + quantity
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification(productName + ' added to cart!', 'success');
                updateCartCount();
            } else if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                showNotification(data.error || 'Error adding to cart', 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Error adding to cart', 'error');
        });
    }
}

function updateCartCount() {
    fetch('api/cart-count.php')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            var cartCount = document.getElementById('cartCount');
            if (cartCount) cartCount.textContent = data.count || 0;
        })
        .catch(function(error) { console.error('Error:', error); });
}

function showNotification(message, type) {
    var notification = document.createElement('div');
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    var bgColor = type === 'success' ? '#4CAF50' : '#f44336';
    notification.innerHTML = '<i class="fas ' + icon + '"></i> ' + message;
    notification.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; z-index: 9999; animation: slideIn 0.3s ease; background-color: ' + bgColor + '; font-weight: 500; box-shadow: 0 2px 10px rgba(0,0,0,0.2);';
    document.body.appendChild(notification);
    setTimeout(function() { notification.remove(); }, 3000);
}

function showFollowersModal() {
    document.getElementById('followersModal').style.display = 'block';
}

function showFollowingModal() {
    document.getElementById('followingModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var followersModal = document.getElementById('followersModal');
    var followingModal = document.getElementById('followingModal');
    if (event.target == followersModal) {
        followersModal.style.display = 'none';
    }
    if (event.target == followingModal) {
        followingModal.style.display = 'none';
    }
}
</script>
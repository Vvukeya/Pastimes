<?php
// User Dashboard
// Student: Vutivi & Karabo
// Date: April 2026

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Get the active tab from URL parameter
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';

// Get user data
$user_id = $_SESSION['user_id'];

// Get user details for profile
$user_sql = "SELECT * FROM tblUser WHERE user_id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));

// Get orders
$orders_sql = "SELECT * FROM tblAorder WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders = mysqli_stmt_get_result($orders_stmt);

// Get user's listings
$listings_sql = "SELECT * FROM tblClothes WHERE seller_id = ? ORDER BY created_at DESC";
$listings_stmt = mysqli_prepare($conn, $listings_sql);
mysqli_stmt_bind_param($listings_stmt, "i", $user_id);
mysqli_stmt_execute($listings_stmt);
$listings = mysqli_stmt_get_result($listings_stmt);

// Get messages
$messages_sql = "SELECT m.*, u.username as sender_name, p.title as product_title 
                 FROM tblMessages m 
                 JOIN tblUser u ON m.sender_id = u.user_id 
                 LEFT JOIN tblClothes p ON m.product_id = p.product_id 
                 WHERE m.receiver_id = ? OR m.sender_id = ?
                 ORDER BY m.created_at DESC LIMIT 20";
$messages_stmt = mysqli_prepare($conn, $messages_sql);
mysqli_stmt_bind_param($messages_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($messages_stmt);
$messages = mysqli_stmt_get_result($messages_stmt);

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

// Handle sending a reply
$reply_sent = false;
$reply_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $reply_message = sanitizeInput($_POST['reply_message']);
    
    if (empty($reply_message)) {
        $reply_error = 'Please enter a message.';
    } else {
        if (sendMessage($conn, $user_id, $receiver_id, $product_id, $reply_message)) {
            $reply_sent = true;
            // Refresh messages
            $messages_stmt = mysqli_prepare($conn, $messages_sql);
            mysqli_stmt_bind_param($messages_stmt, "ii", $user_id, $user_id);
            mysqli_stmt_execute($messages_stmt);
            $messages = mysqli_stmt_get_result($messages_stmt);
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
            
            <!-- MESSAGES TAB WITH REPLY FUNCTIONALITY -->
            <?php elseif ($active_tab == 'messages'): ?>
                <h2 style="margin-bottom: 20px;"><i class="fas fa-envelope"></i> Messages</h2>
                
                <?php if ($reply_sent): ?>
                    <div style="background: #E8F5E9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50;">
                        <i class="fas fa-check-circle"></i> Reply sent successfully!
                    </div>
                <?php endif; ?>
                
                <?php if ($reply_error): ?>
                    <div style="background: #FFEBEE; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #F44336;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $reply_error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (mysqli_num_rows($messages) > 0): ?>
                    <div class="messages-list">
                        <?php while ($msg = mysqli_fetch_assoc($messages)): 
                            $is_sender = ($msg['sender_id'] == $user_id);
                        ?>
                            <div class="message-item" id="message-<?php echo $msg['message_id']; ?>" style="border-bottom: 1px solid var(--light-grey); padding: 20px; <?php echo (!$is_sender && $msg['is_read'] == 0) ? 'background: #F5F0E8;' : ''; ?>">
                                <!-- Message Header -->
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap;">
                                    <div>
                                        <strong style="font-size: 16px;">
                                            <i class="fas fa-user-circle"></i> 
                                            <?php echo htmlspecialchars($msg['sender_name']); ?>
                                        </strong>
                                        <?php if ($is_sender): ?>
                                            <span style="background: var(--pastime-green); color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 8px;">You</span>
                                        <?php endif; ?>
                                    </div>
                                    <small style="color: var(--grey);">
                                        <i class="far fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?>
                                    </small>
                                </div>
                                
                                <!-- Product Info -->
                                <?php if ($msg['product_title']): ?>
                                    <div style="margin-bottom: 12px; font-size: 13px; background: var(--warm-beige); padding: 8px 12px; border-radius: var(--radius); display: inline-block;">
                                        <i class="fas fa-tag"></i> Regarding: 
                                        <a href="index.php?page=product&id=<?php echo $msg['product_id']; ?>" style="color: var(--pastime-green); text-decoration: none; font-weight: 500;">
                                            <?php echo htmlspecialchars($msg['product_title']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Message Content -->
                                <div style="margin: 12px 0; padding: 15px; background: white; border-radius: var(--radius); border-left: 3px solid var(--pastime-green);">
                                    <?php echo nl2br(htmlspecialchars($msg['message_text'])); ?>
                                </div>
                                
                                <!-- Reply Button and Form -->
                                <?php if (!$is_sender): ?>
                                    <div style="margin-top: 12px;">
                                        <button onclick="toggleReplyForm(<?php echo $msg['message_id']; ?>, <?php echo $msg['sender_id']; ?>, <?php echo $msg['product_id'] ?: 'null'; ?>)" class="btn-outline" style="padding: 6px 16px; font-size: 13px;">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        
                                        <!-- Reply Form (hidden by default) -->
                                        <div id="reply-form-<?php echo $msg['message_id']; ?>" style="display: none; margin-top: 15px;">
                                            <form method="POST" action="index.php?page=dashboard&tab=messages" class="reply-form">
                                                <input type="hidden" name="receiver_id" value="<?php echo $msg['sender_id']; ?>">
                                                <input type="hidden" name="product_id" value="<?php echo $msg['product_id']; ?>">
                                                <div class="form-group">
                                                    <textarea name="reply_message" rows="3" placeholder="Type your reply here..." style="width: 100%; padding: 12px; border: 1px solid var(--light-grey); border-radius: var(--radius); resize: vertical;" required></textarea>
                                                </div>
                                                <div style="display: flex; gap: 10px;">
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
</script>
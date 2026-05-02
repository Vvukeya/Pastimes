<?php
// Admin Dashboard
// Student: Vutivi & Karabo
// Date: April 2026

session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check admin access - if not admin, redirect to login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

// Get statistics
$stats = [];

// Total users
$sql = "SELECT COUNT(*) as count FROM tblUser WHERE role = 'user'";
$result = mysqli_query($conn, $sql);
$stats['users'] = mysqli_fetch_assoc($result)['count'];

// Total products
$sql = "SELECT COUNT(*) as count FROM tblClothes";
$result = mysqli_query($conn, $sql);
$stats['products'] = mysqli_fetch_assoc($result)['count'];

// Pending products
$sql = "SELECT COUNT(*) as count FROM tblClothes WHERE status = 'pending'";
$result = mysqli_query($conn, $sql);
$stats['pending'] = mysqli_fetch_assoc($result)['count'];

// Total orders
$sql = "SELECT COUNT(*) as count FROM tblAorder";
$result = mysqli_query($conn, $sql);
$stats['orders'] = mysqli_fetch_assoc($result)['count'];

// Pending verifications
$sql = "SELECT COUNT(*) as count FROM tblUser WHERE is_verified = 0 AND role = 'user'";
$result = mysqli_query($conn, $sql);
$stats['pending_verification'] = mysqli_fetch_assoc($result)['count'];

// Total revenue
$sql = "SELECT SUM(total_amount) as total FROM tblAorder WHERE payment_status = 'paid'";
$result = mysqli_query($conn, $sql);
$stats['revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Recent orders
$sql = "SELECT o.*, u.name, u.surname FROM tblAorder o JOIN tblUser u ON o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = mysqli_query($conn, $sql);

// Recent messages
$sql = "SELECT m.*, u.username as sender, u2.username as receiver 
        FROM tblMessages m 
        JOIN tblUser u ON m.sender_id = u.user_id 
        JOIN tblUser u2 ON m.receiver_id = u2.user_id 
        ORDER BY m.created_at DESC LIMIT 5";
$recent_messages = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pastimes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 280px;
            background: #1a1a2e;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-sidebar .logo {
            padding: 24px;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .admin-sidebar .logo span:first-child {
            color: var(--gold);
        }
        
        .admin-sidebar .logo span:last-child {
            color: white;
        }
        
        .admin-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: var(--pastime-green);
            color: white;
        }
        
        .admin-sidebar nav a i {
            width: 20px;
        }
        
        .admin-sidebar hr {
            margin: 16px 24px;
            border-color: rgba(255,255,255,0.1);
        }
        
        .admin-main {
            flex: 1;
            margin-left: 280px;
        }
        
        .admin-header {
            background: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-header h1 {
            font-size: 24px;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .admin-content {
            padding: 24px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: var(--grey);
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--pastime-green);
        }
        
        .section-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .section-card h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-grey);
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
        
        .btn-primary, .btn-outline {
            padding: 8px 16px;
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--pastime-green);
            color: white;
            border: none;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--pastime-green);
            border: 1px solid var(--pastime-green);
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }
            .admin-sidebar .logo span:last-child,
            .admin-sidebar nav a span:last-child {
                display: none;
            }
            .admin-main {
                margin-left: 70px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo">
                <span>PAST</span><span>IMES</span>
            </div>
            <nav>
                <a href="index.php" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="products.php">
                    <i class="fas fa-tshirt"></i>
                    <span>Products</span>
                </a>
                <a href="pending-approvals.php">
                    <i class="fas fa-clock"></i>
                    <span>Pending Approvals</span>
                </a>
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
                <hr>
                <a href="../index.php">
                    <i class="fas fa-store"></i>
                    <span>View Store</span>
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
                </div>
            </div>
            
            <div class="admin-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><i class="fas fa-users"></i> Total Users</h3>
                        <div class="stat-number"><?php echo $stats['users']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-tshirt"></i> Total Products</h3>
                        <div class="stat-number"><?php echo $stats['products']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-clock"></i> Pending Approval</h3>
                        <div class="stat-number"><?php echo $stats['pending']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-shopping-cart"></i> Total Orders</h3>
                        <div class="stat-number"><?php echo $stats['orders']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-user-check"></i> Pending Verification</h3>
                        <div class="stat-number"><?php echo $stats['pending_verification']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-dollar-sign"></i> Total Revenue</h3>
                        <div class="stat-number">R <?php echo number_format($stats['revenue'], 2); ?></div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div class="section-card">
                        <h3><i class="fas fa-shopping-cart"></i> Recent Orders</h3>
                        <table class="admin-table">
                            <thead>
                                <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_orders) > 0): ?>
                                    <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td><?php echo $order['name'] . ' ' . $order['surname']; ?></td>
                                            <td>R <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="text-align: center;">No orders yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="section-card">
                        <h3><i class="fas fa-envelope"></i> Recent Messages</h3>
                        <table class="admin-table">
                            <thead>
                                <tr><th>From</th><th>To</th><th>Message</th></tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_messages) > 0): ?>
                                    <?php while ($msg = mysqli_fetch_assoc($recent_messages)): ?>
                                        <tr>
                                            <td><?php echo $msg['sender']; ?></td>
                                            <td><?php echo $msg['receiver']; ?></td>
                                            <td><?php echo substr($msg['message_text'], 0, 30); ?>...</td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" style="text-align: center;">No messages yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="section-card" style="margin-top: 24px;">
                    <h3><i class="fas fa-chart-line"></i> Quick Actions</h3>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <a href="pending-approvals.php" class="btn-primary">Review Pending Items</a>
                        <a href="products.php" class="btn-outline">Manage Products</a>
                        <a href="users.php" class="btn-outline">Manage Users</a>
                        <a href="orders.php" class="btn-outline">View All Orders</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
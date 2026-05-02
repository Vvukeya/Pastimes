<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

$sql = "SELECT m.*, u1.username as sender_name, u2.username as receiver_name, p.title as product_title 
        FROM tblMessages m 
        JOIN tblUser u1 ON m.sender_id = u1.user_id 
        JOIN tblUser u2 ON m.receiver_id = u2.user_id 
        LEFT JOIN tblClothes p ON m.product_id = p.product_id 
        ORDER BY m.created_at DESC";
$messages = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .admin-wrapper { display: flex; }
        .admin-sidebar { width: 280px; background: #1a1a2e; color: white; min-height: 100vh; }
        .admin-sidebar .logo { padding: 24px; font-size: 24px; font-weight: 800; text-align: center; }
        .admin-sidebar .logo span:first-child { color: var(--gold); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #aaa; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: var(--pastime-green); color: white; }
        .admin-main { flex: 1; }
        .admin-header { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .admin-content { padding: 24px; }
        .message-card { background: white; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .message-header { display: flex; justify-content: space-between; margin-bottom: 10px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo"><span>PAST</span><span>IMES</span></div>
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
                <a href="pending-approvals.php"><i class="fas fa-clock"></i> Pending</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header"><h1>Messages</h1></div>
            <div class="admin-content">
                <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <span><strong>From:</strong> <?php echo $msg['sender_name']; ?></span>
                            <span><strong>To:</strong> <?php echo $msg['receiver_name']; ?></span>
                            <span><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                        </div>
                        <?php if ($msg['product_title']): ?>
                            <div><strong>Product:</strong> <?php echo $msg['product_title']; ?></div>
                        <?php endif; ?>
                        <div style="margin-top: 10px; padding: 10px; background: #f5f0e8; border-radius: 8px;"><?php echo nl2br(htmlspecialchars($msg['message_text'])); ?></div>
                    </div>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($messages) == 0): ?>
                    <p>No messages found.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
<?php
// Admin Orders Management
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $sql = "UPDATE tblAorder SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);
}

$sql = "SELECT o.*, u.name, u.surname, u.email FROM tblAorder o JOIN tblUser u ON o.user_id = u.user_id ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
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
        .admin-table { width: 100%; background: white; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .admin-table th { background: #f8f9fa; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .status-pending { background: #fff3e0; color: #ff9800; }
        .status-processing { background: #e3f2fd; color: #2196f3; }
        .status-shipped { background: #e8eaf6; color: #3f51b5; }
        .status-delivered { background: #e8f5e9; color: #4caf50; }
        .btn-primary { background: var(--pastime-green); color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        select, button { padding: 6px 12px; border-radius: 4px; border: 1px solid #ddd; }
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
                <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header"><h1>Orders</h1></div>
            <div class="admin-content">
                <table class="admin-table">
                    <thead><tr><th>Order #</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo $order['name'] . ' ' . $order['surname']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>R <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                                    <td>
                                        <select name="status">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-primary">Update</button>
                                    </td>
                                </tr>
                            </form>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
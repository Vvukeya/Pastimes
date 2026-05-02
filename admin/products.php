<?php
// Admin Products Management
// Student: Vutivi & Karabo

session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM tblClothes WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Product deleted successfully!";
    }
}

// Handle status update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id = intval($_GET['id']);
    $sql = "UPDATE tblClothes SET status = ? WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Product status updated!";
    }
}

// Get all products
$sql = "SELECT p.*, u.username as seller_name FROM tblClothes p JOIN tblUser u ON p.seller_id = u.user_id ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 280px; background: #1a1a2e; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        .admin-sidebar .logo { padding: 24px; font-size: 24px; font-weight: 800; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .admin-sidebar .logo span:first-child { color: var(--gold); }
        .admin-sidebar .logo span:last-child { color: white; }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #aaa; text-decoration: none; transition: all 0.3s ease; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: var(--pastime-green); color: white; }
        .admin-sidebar nav a i { width: 20px; }
        .admin-sidebar hr { margin: 16px 24px; border-color: rgba(255,255,255,0.1); }
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-header { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .admin-header h1 { font-size: 24px; }
        .admin-content { padding: 24px; }
        .admin-table { width: 100%; border-collapse: collapse; background: white; border-radius: var(--radius); overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--light-grey); }
        .admin-table th { background: #f8f9fa; font-weight: 600; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-approved { background: #e8f5e9; color: #4caf50; }
        .status-pending { background: #fff3e0; color: #ff9800; }
        .status-rejected { background: #ffebee; color: #f44336; }
        .btn-primary, .btn-outline { padding: 6px 12px; border-radius: var(--radius); text-decoration: none; font-size: 12px; display: inline-block; }
        .btn-primary { background: var(--pastime-green); color: white; border: none; }
        .btn-outline { background: transparent; color: var(--pastime-green); border: 1px solid var(--pastime-green); }
        .success-message { background: #e8f5e9; color: #4caf50; padding: 12px; border-radius: var(--radius); margin-bottom: 20px; }
        @media (max-width: 768px) { .admin-sidebar { width: 70px; } .admin-main { margin-left: 70px; } .admin-sidebar .logo span:last-child, .admin-sidebar nav a span:last-child { display: none; } }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo"><span>PAST</span><span>IMES</span></div>
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="products.php" class="active"><i class="fas fa-tshirt"></i><span>Products</span></a>
                <a href="pending-approvals.php"><i class="fas fa-clock"></i><span>Pending Approvals</span></a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
                <a href="users.php"><i class="fas fa-users"></i><span>Users</span></a>
                <a href="messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i><span>View Store</span></a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Product Management</h1>
                <a href="product-edit.php" class="btn-primary">+ Add New Product</a>
            </div>
            
            <div class="admin-content">
                <?php if (isset($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <table class="admin-table">
                    <thead>
                        <tr><th>ID</th><th>Image</th><th>Title</th><th>Brand</th><th>Price</th><th>Seller</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($products) > 0): ?>
                            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                                <tr>
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td><img src="../<?php echo !empty($product['image_url']) ? $product['image_url'] : 'images/placeholder.jpg'; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td><?php echo $product['brand']; ?></td>
                                    <td>R <?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['seller_name']; ?></td>
                                    <td><span class="status-badge status-<?php echo $product['status']; ?>"><?php echo $product['status']; ?></span></td>
                                    <td>
                                        <a href="product-edit.php?id=<?php echo $product['product_id']; ?>" class="btn-outline" style="margin-right: 5px;">Edit</a>
                                        <?php if ($product['status'] == 'pending'): ?>
                                            <a href="?status=approved&id=<?php echo $product['product_id']; ?>" class="btn-primary" style="margin-right: 5px;">Approve</a>
                                            <a href="?status=rejected&id=<?php echo $product['product_id']; ?>" class="btn-outline" style="margin-right: 5px;">Reject</a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $product['product_id']; ?>" onclick="return confirm('Delete this product permanently?')" class="btn-outline" style="color: #f44336; border-color: #f44336;">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center;">No products found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php
// Pending Approvals - Seller verification and product approvals
// Student: Vutivi & Karabo

session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

$success = '';

// Handle user verification
if (isset($_GET['verify_user'])) {
    $user_id = intval($_GET['verify_user']);
    $sql = "UPDATE tblUser SET is_verified = 1 WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "User verified successfully!";
    }
}

// Handle seller verification
if (isset($_GET['verify_seller'])) {
    $user_id = intval($_GET['verify_seller']);
    $sql = "UPDATE tblUser SET is_seller_verified = 1 WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Seller verified successfully!";
    }
}

// Handle product approval
if (isset($_GET['approve_product'])) {
    $product_id = intval($_GET['approve_product']);
    $sql = "UPDATE tblClothes SET status = 'approved' WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Product approved!";
    }
}

// Handle product rejection
if (isset($_GET['reject_product'])) {
    $product_id = intval($_GET['reject_product']);
    $sql = "UPDATE tblClothes SET status = 'rejected' WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Product rejected!";
    }
}

// Get pending user verifications
$pending_users_sql = "SELECT * FROM tblUser WHERE is_verified = 0 AND role = 'user'";
$pending_users = mysqli_query($conn, $pending_users_sql);

// Get pending seller verifications
$pending_sellers_sql = "SELECT * FROM tblUser WHERE is_seller_verified = 0 AND role = 'user' AND is_verified = 1";
$pending_sellers = mysqli_query($conn, $pending_sellers_sql);

// Get pending products
$pending_products_sql = "SELECT p.*, u.username as seller_name FROM tblClothes p JOIN tblUser u ON p.seller_id = u.user_id WHERE p.status = 'pending'";
$pending_products = mysqli_query($conn, $pending_products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - Admin Panel</title>
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
        .section-card { background: white; border-radius: var(--radius); padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .section-card h3 { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--light-grey); }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--light-grey); }
        .admin-table th { background: #f8f9fa; font-weight: 600; }
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
                <a href="products.php"><i class="fas fa-tshirt"></i><span>Products</span></a>
                <a href="pending-approvals.php" class="active"><i class="fas fa-clock"></i><span>Pending Approvals</span></a>
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
                <h1>Pending Approvals</h1>
            </div>
            
            <div class="admin-content">
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <!-- Pending User Verifications -->
                <div class="section-card">
                    <h3><i class="fas fa-user-plus"></i> Pending User Verifications</h3>
                    <?php if (mysqli_num_rows($pending_users) > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Username</th><th>Registered</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($pending_users)): ?>
                                    <tr>
                                        <td><?php echo $user['name'] . ' ' . $user['surname']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td><a href="?verify_user=<?php echo $user['user_id']; ?>" class="btn-primary">Verify User</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No pending user verifications.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Pending Seller Verifications -->
                <div class="section-card">
                    <h3><i class="fas fa-store"></i> Pending Seller Verifications</h3>
                    <?php if (mysqli_num_rows($pending_sellers) > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Username</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($pending_sellers)): ?>
                                    <tr>
                                        <td><?php echo $user['name'] . ' ' . $user['surname']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><a href="?verify_seller=<?php echo $user['user_id']; ?>" class="btn-primary">Verify as Seller</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No pending seller verifications.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Pending Product Approvals -->
                <div class="section-card">
                    <h3><i class="fas fa-tshirt"></i> Pending Product Approvals</h3>
                    <?php if (mysqli_num_rows($pending_products) > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr><th>Product</th><th>Seller</th><th>Price</th><th>Condition</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($product = mysqli_fetch_assoc($pending_products)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['title']); ?></td>
                                        <td><?php echo $product['seller_name']; ?></td>
                                        <td>R <?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['condition']; ?></td>
                                        <td>
                                            <a href="?approve_product=<?php echo $product['product_id']; ?>" class="btn-primary" style="margin-right: 5px;">Approve</a>
                                            <a href="?reject_product=<?php echo $product['product_id']; ?>" class="btn-outline">Reject</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No pending product approvals.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
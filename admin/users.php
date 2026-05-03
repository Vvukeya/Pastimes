<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

// Handle update user
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $is_seller_verified = isset($_POST['is_seller_verified']) ? 1 : 0;
    $sql = "UPDATE tblUser SET is_verified = ?, is_seller_verified = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $is_verified, $is_seller_verified, $user_id);
    mysqli_stmt_execute($stmt);
    $success = "User updated successfully!";
}

// Handle delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    // Don't allow deleting admin
    $check_sql = "SELECT role FROM tblUser WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $user_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user['role'] != 'admin') {
        $sql = "DELETE FROM tblUser WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $success = "User deleted successfully!";
    }
    header('Location: users.php');
    exit();
}

$sql = "SELECT * FROM tblUser ORDER BY created_at DESC";
$users = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin</title>
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
        .admin-header { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; }
        .admin-content { padding: 24px; }
        .admin-table { width: 100%; background: white; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .admin-table th { background: #f8f9fa; }
        .btn-primary { background: var(--pastime-green); color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: #f44336; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-left: 5px; }
        .success-message { background: #e8f5e9; color: #4caf50; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
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
                <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
                <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h1>User Management</h1>
                <a href="add-user.php" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
            </div>
            <div class="admin-content">
                <?php if (isset($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Registered</th>
                            <th>Verified</th>
                            <th>Seller</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo $user['name'] . ' ' . $user['surname']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <input type="checkbox" name="is_verified" value="1" <?php echo $user['is_verified'] ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="is_seller_verified" value="1" <?php echo $user['is_seller_verified'] ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <button type="submit" name="update_user" class="btn-primary" style="padding: 4px 12px;">Update</button>
                                        <?php if ($user['role'] != 'admin'): ?>
                                            <a href="?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Delete this user permanently?')" class="btn-danger" style="padding: 4px 12px;">Delete</a>
                                        <?php endif; ?>
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
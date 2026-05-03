<?php
// At the top of any file that uses $conn, add:

// Admin Add User Page
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = sanitizeInput($_POST['name']);
    $surname = sanitizeInput($_POST['surname']);
    $email = sanitizeInput($_POST['email']);
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $is_seller_verified = isset($_POST['is_seller_verified']) ? 1 : 0;
    
    // Validation
    if (empty($name) || empty($surname) || empty($email) || empty($username) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT user_id FROM tblUser WHERE username = ? OR email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Username or email already exists';
        } else {
            $password_hash = md5($password);
            $sql = "INSERT INTO tblUser (name, surname, email, username, password_hash, is_verified, is_seller_verified, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssii", $name, $surname, $email, $username, $password_hash, $is_verified, $is_seller_verified);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'User added successfully!';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Failed to add user. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Admin Panel</title>
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
        .admin-content { padding: 24px; max-width: 800px; }
        .form-container { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-group { display: flex; gap: 20px; align-items: center; margin-top: 10px; }
        .checkbox-group label { display: flex; align-items: center; gap: 8px; margin: 0; }
        .btn-primary { background: var(--pastime-green); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-secondary { background: #666; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .success-message { background: #e8f5e9; color: #4caf50; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .error-message { background: #ffebee; color: #f44336; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
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
                <h1>Add New User</h1>
                <a href="users.php" class="btn-secondary">← Back to Users</a>
            </div>
            <div class="admin-content">
                <div class="form-container">
                    <?php if ($success): ?>
                        <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">First Name *</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="surname">Last Name *</label>
                                <input type="text" id="surname" name="surname" required value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password * (min 8 characters)</label>
                                <input type="password" id="password" name="password" required>
                                <small style="color: #666;">Password will be stored securely using MD5 hash</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" name="is_verified" value="1" checked> 
                                <i class="fas fa-check-circle"></i> Verify User (allow login immediately)
                            </label>
                            <label>
                                <input type="checkbox" name="is_seller_verified" value="1"> 
                                <i class="fas fa-store"></i> Verify as Seller
                            </label>
                        </div>
                        
                        <button type="submit" name="add_user" class="btn-primary" style="margin-top: 20px; width: 100%;">
                            <i class="fas fa-user-plus"></i> Add User
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
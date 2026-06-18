<?php
// Login Page
// Student: Vutivi & Karabo

/** @var mysqli $conn */

$error = '';
$username = '';
$admin_portal = isset($_GET['admin']) && $_GET['admin'] == '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Get user from database
        $sql = "SELECT user_id, name, surname, username, email, password_hash, is_verified, is_seller_verified, role 
                FROM tblUser WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Check if user is verified
            if ($user['is_verified'] == 0) {
                $error = 'Your account is pending verification. Please wait for admin approval.';
            } else {
                // Verify password (MD5 for compatibility)
                if (md5($password) === $user['password_hash']) {
                    if ($admin_portal && $user['role'] !== 'admin') {
                        $error = 'This portal is for administrators only. Enter your administrator credential.';
                    } else {
                        // Set session variables
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['surname'] = $user['surname'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['is_seller_verified'] = $user['is_seller_verified'];
                        $_SESSION['role'] = $user['role'];

                        // Update last login
                        $update_sql = "UPDATE tblUser SET last_login = NOW() WHERE user_id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "i", $user['user_id']);
                        mysqli_stmt_execute($update_stmt);

                        // Redirect based on role
                        if ($user['role'] === 'admin') {
                            header('Location: admin/index.php');
                        } else {
                            header('Location: index.php?page=dashboard');
                        }
                        exit();
                    }
                } else {
                    $error = 'Invalid password';
                }
            }
        } else {
            $error = 'User not found';
        }
    }
}
?>

<div class="form-container">
    <h2 class="form-title"><?php echo $admin_portal ? 'Admin Portal Login' : 'Login to Pastimes'; ?></h2>
    <?php if ($admin_portal): ?>
        <div style="background: #FFF3E0; padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; color: #8A5A00; border-left: 4px solid #F39C12;">
            Admin portal. Enter your administrator credential.
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error-message" style="background: #FFEBEE; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #F44336;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo $admin_portal ? 'index.php?page=login&admin=1' : 'index.php?page=login'; ?>" data-validate>
        <div class="form-group">
            <label for="username">Username or Email *</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username); ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%;">Login</button>
        
        <div class="form-footer">
            <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
            <p><a href="#">Forgot Password?</a></p>
        </div>
    </form>
</div>
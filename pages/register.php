<?php
// Registration Page
// Student: Vutivi & Karabo

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $surname = sanitizeInput($_POST['surname'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    // Validation
    if (empty($name) || empty($surname) || empty($email) || empty($username) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address';
    } elseif (!validatePassword($password)) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username or email already exists
        $sql = "SELECT user_id FROM tblUser WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password (using MD5 for compatibility with sample data, but use password_hash for production)
            $password_hash = md5($password);
            
            // Insert user (pending verification)
            $sql = "INSERT INTO tblUser (name, surname, email, username, password_hash, phone, is_verified, is_seller_verified) 
                    VALUES (?, ?, ?, ?, ?, ?, 0, 0)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $surname, $email, $username, $password_hash, $phone);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Registration successful! Please wait for admin verification before logging in.';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<div class="form-container">
    <h2 class="form-title">Create Account</h2>
    
    <?php if ($error): ?>
        <div class="error-message" style="background: #FFEBEE; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #F44336;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message">
            <?php echo $success; ?>
            <br><br>
            <a href="index.php?page=login" class="btn-primary">Go to Login</a>
        </div>
    <?php else: ?>
        <form method="POST" action="" data-validate>
            <div class="form-group">
                <label for="name">First Name *</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="surname">Last Name *</label>
                <input type="text" id="surname" name="surname" required value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password * (minimum 8 characters)</label>
                <input type="password" id="password" name="password" required>
                <div id="password-strength" style="font-size: 12px; margin-top: 5px;"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">Register</button>
            
            <div class="form-footer">
                <p>Already have an account? <a href="index.php?page=login">Login here</a></p>
            </div>
        </form>
    <?php endif; ?>
</div>
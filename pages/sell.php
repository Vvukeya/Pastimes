<?php
// Sell Page - Submit items for sale
// Student: Vutivi & Karabo

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Check if user is a verified seller
if (!isset($_SESSION['is_seller_verified']) || $_SESSION['is_seller_verified'] != 1) {
    header('Location: index.php?page=dashboard&error=not_verified');
    exit();
}

$error = '';
$success = '';

// Create uploads directory if not exists
$upload_dir = __DIR__ . '/../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_item'])) {
    $title = sanitizeInput($_POST['title'] ?? '');
    $brand = sanitizeInput($_POST['brand'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $condition = sanitizeInput($_POST['condition'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $size = sanitizeInput($_POST['size'] ?? '');
    $colour = sanitizeInput($_POST['colour'] ?? '');
    
    // Validation
    if (empty($title) || empty($brand) || empty($price) || empty($condition)) {
        $error = 'Please fill in all required fields';
    } elseif ($price <= 0) {
        $error = 'Please enter a valid price';
    } else {
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . uniqid() . '.' . $ext;
                $upload_path = $upload_dir . $new_filename;
                $relative_path = 'uploads/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = $relative_path;
                } else {
                    $error = 'Failed to upload image. Please check folder permissions.';
                }
            } else {
                $error = 'Invalid file type. Please upload JPG, PNG, or GIF.';
            }
        }
        
        if (empty($error)) {
            // Insert product with pending status
            $sql = "INSERT INTO tblClothes (seller_id, title, brand, description, price, `condition`, category, size, colour, image_url, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssdsssss", $_SESSION['user_id'], $title, $brand, $description, $price, $condition, $category, $size, $colour, $image_url);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Your item has been submitted for review. An administrator will approve it soon.';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Failed to submit item. Please try again.';
            }
        }
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <div class="form-container" style="max-width: 700px; margin: 0 auto;">
        <h2 class="form-title">Sell an Item</h2>
        <p style="text-align: center; margin-bottom: 30px; color: var(--grey);">
            Fill out the form below to submit your item for sale. Our team will review and list it for you.
        </p>
        
        <?php if ($error): ?>
            <div style="background: #FFEBEE; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #F44336; border-left: 4px solid #F44336;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #E8F5E9; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #4CAF50; border-left: 4px solid #4CAF50;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <br><br>
                <a href="index.php?page=dashboard&tab=listings" class="btn-primary" style="display: inline-block;">View My Listings</a>
            </div>
        <?php else: ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Product Title *</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input type="text" id="brand" name="brand" required value="<?php echo htmlspecialchars($_POST['brand'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe the item's condition, features, measurements, etc."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (R) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="condition">Condition *</label>
                    <select id="condition" name="condition" required>
                        <option value="">Select condition</option>
                        <option value="New" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'New') ? 'selected' : ''; ?>>New with tags</option>
                        <option value="Like New" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'Like New') ? 'selected' : ''; ?>>Like New</option>
                        <option value="Good" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'Good') ? 'selected' : ''; ?>>Good</option>
                        <option value="Fair" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">Select category</option>
                        <option value="Jeans" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Jeans') ? 'selected' : ''; ?>>Jeans</option>
                        <option value="Dresses" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Dresses') ? 'selected' : ''; ?>>Dresses</option>
                        <option value="Jackets" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Jackets') ? 'selected' : ''; ?>>Jackets</option>
                        <option value="Shoes" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Shoes') ? 'selected' : ''; ?>>Shoes</option>
                        <option value="T-Shirts" <?php echo (isset($_POST['category']) && $_POST['category'] == 'T-Shirts') ? 'selected' : ''; ?>>T-Shirts</option>
                        <option value="Accessories" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="size">Size</label>
                    <input type="text" id="size" name="size" placeholder="e.g., S, M, L, XL, 32, 8" value="<?php echo htmlspecialchars($_POST['size'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="colour">Colour</label>
                    <input type="text" id="colour" name="colour" value="<?php echo htmlspecialchars($_POST['colour'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: var(--grey); display: block; margin-top: 5px;">Upload a clear photo of the item (JPG, PNG, GIF - max 5MB)</small>
                </div>
                
                <button type="submit" name="submit_item" class="btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">
                    <i class="fas fa-paper-plane"></i> Submit for Review
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
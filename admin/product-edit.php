<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?page=login');
    exit();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($product_id > 0) {
    $sql = "SELECT * FROM tblClothes WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $condition = $_POST['condition'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $colour = $_POST['colour'];
    $status = $_POST['status'];
    
    if ($product_id > 0) {
        $sql = "UPDATE tblClothes SET title=?, brand=?, description=?, price=?, `condition`=?, category=?, size=?, colour=?, status=? WHERE product_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssdsssssi", $title, $brand, $description, $price, $condition, $category, $size, $colour, $status, $product_id);
    } else {
        $sql = "INSERT INTO tblClothes (seller_id, title, brand, description, price, `condition`, category, size, colour, status) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssdsssss", $title, $brand, $description, $price, $condition, $category, $size, $colour, $status);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: products.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_id ? 'Edit' : 'Add'; ?> Product</title>
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
        .admin-content { padding: 24px; max-width: 800px; }
        .form-container { background: white; padding: 24px; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-primary { background: var(--pastime-green); color: white; padding: 10px 24px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo"><span>PAST</span><span>IMES</span></div>
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php" class="active"><i class="fas fa-tshirt"></i> Products</a>
                <a href="pending-approvals.php"><i class="fas fa-clock"></i> Pending</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                <hr>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="admin-header">
                <h1><?php echo $product_id ? 'Edit' : 'Add'; ?> Product</h1>
                <a href="products.php" class="btn-primary">Back to Products</a>
            </div>
            <div class="admin-content">
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group"><label>Title *</label><input type="text" name="title" value="<?php echo $product['title'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Brand *</label><input type="text" name="brand" value="<?php echo $product['brand'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Description</label><textarea name="description" rows="4"><?php echo $product['description'] ?? ''; ?></textarea></div>
                        <div class="form-group"><label>Price *</label><input type="number" step="0.01" name="price" value="<?php echo $product['price'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Condition</label><select name="condition"><option value="New">New</option><option value="Like New">Like New</option><option value="Good">Good</option><option value="Fair">Fair</option></select></div>
                        <div class="form-group"><label>Category</label><input type="text" name="category" value="<?php echo $product['category'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Size</label><input type="text" name="size" value="<?php echo $product['size'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Colour</label><input type="text" name="colour" value="<?php echo $product['colour'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Status</label><select name="status"><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option><option value="sold">Sold</option></select></div>
                        <button type="submit" class="btn-primary">Save Product</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php
// Order Success Page
// Student: Vutivi & Karabo

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$order_number = isset($_GET['order']) ? sanitizeInput($_GET['order']) : '';

if (!$order_number) {
    header('Location: index.php?page=home');
    exit();
}

// Get order details
$sql = "SELECT o.*, oi.*, p.title, p.image_url 
        FROM tblAorder o 
        JOIN tblOrderItems oi ON o.order_id = oi.order_id 
        JOIN tblClothes p ON oi.product_id = p.product_id 
        WHERE o.order_number = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $order_number, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$order_items = mysqli_stmt_get_result($stmt);

$order_info = null;
if ($row = mysqli_fetch_assoc($order_items)) {
    $order_info = $row;
    mysqli_data_seek($order_items, 0);
}
?>

<div class="container" style="padding: 60px 0; text-align: center;">
    <div style="background: white; border-radius: var(--radius); padding: 40px; max-width: 600px; margin: 0 auto;">
        <div style="background: var(--success-green); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
        </div>
        
        <h1 style="color: var(--success-green); margin-bottom: 16px;">Order Confirmed!</h1>
        
        <?php if ($order_info): ?>
            <p style="margin-bottom: 8px;">Thank you for your purchase, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
            <p style="margin-bottom: 24px;">Your order number is: <strong><?php echo $order_number; ?></strong></p>
            
            <div style="background: var(--warm-beige); padding: 20px; border-radius: var(--radius); text-align: left; margin-bottom: 24px;">
                <h3 style="margin-bottom: 16px;">Order Summary</h3>
                <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                    <div style="display: flex; gap: 16px; margin-bottom: 12px;">
                        <img src="<?php echo $item['image_url'] ?? 'images/placeholder.jpg'; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius);">
                        <div>
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                            <p>Quantity: <?php echo $item['quantity']; ?> | R <?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
                <hr style="margin: 16px 0;">
                <p><strong>Total: R <?php echo number_format($order_info['total_amount'], 2); ?></strong></p>
            </div>
        <?php endif; ?>
        
        <p>We'll send you an email confirmation with tracking information once your order ships.</p>
        
        <div style="display: flex; gap: 16px; justify-content: center; margin-top: 30px;">
            <a href="index.php?page=dashboard" class="btn-outline">View Order History</a>
            <a href="index.php?page=browse" class="btn-primary">Continue Shopping</a>
        </div>
    </div>
</div>
<?php
// Checkout Page
// Student: Vutivi & Karabo

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$cart_items = getCartItems($conn, $_SESSION['user_id']);
$cart_total = getCartTotal($conn, $_SESSION['user_id']);

if (mysqli_num_rows($cart_items) == 0) {
    header('Location: index.php?page=cart');
    exit();
}

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = sanitizeInput($_POST['delivery_address'] ?? '');
    $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
    
    if (empty($delivery_address)) {
        $error = 'Please enter your delivery address';
    } elseif (empty($payment_method)) {
        $error = 'Please select a payment method';
    } else {
        $order_number = createOrder($conn, $_SESSION['user_id'], $delivery_address, $payment_method);
        
        if ($order_number) {
            header("Location: index.php?page=order-success&order=" . urlencode($order_number));
            exit();
        } else {
            $error = 'Failed to process order. Please try again.';
        }
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <h1 class="section-title">Checkout</h1>
<!-- php if-statement to handle error-->
    <?php if ($error): ?>
        <div class="error-message" style="background: #FFEBEE; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #F44336;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="cart-container">
        <div>
            <form method="POST" action="">
                <div style="background: white; border-radius: var(--radius); padding: 24px; margin-bottom: 24px;">
                    <h3 style="margin-bottom: 20px;">Delivery Information</h3>
                    
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address *</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" required placeholder="Street address, city, postal code, province" style="width: 100%; padding: 12px; border: 1px solid var(--light-grey); border-radius: var(--radius);"><?php echo htmlspecialchars($_POST['delivery_address'] ?? $_SESSION['delivery_address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div style="background: white; border-radius: var(--radius); padding: 24px;">
                    <h3 style="margin-bottom: 20px;">Payment Method</h3>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="card" required> Credit/Debit Card
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="paypal"> PayPal
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="bank_transfer"> Bank Transfer
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="cash_on_delivery"> Cash on Delivery
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 24px; padding: 16px; font-size: 16px;">
                    Place Order
                </button>
            </form>
        </div>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <?php 
            mysqli_data_seek($cart_items, 0);
            while ($item = mysqli_fetch_assoc($cart_items)): 
            ?>
                <div class="summary-row" style="font-size: 14px;">
                    <span><?php echo htmlspecialchars($item['title']); ?> x<?php echo $item['quantity']; ?></span>
                    <span>R <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endwhile; ?>
            <div class="summary-row summary-total">
                <span>Total</span>
                <span>R <?php echo number_format($cart_total, 2); ?></span>
            </div>
        </div>
    </div>
</div>
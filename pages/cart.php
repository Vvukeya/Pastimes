<?php
// Shopping Cart Page
// Student: Vutivi & Karabo

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$cart_items = getCartItems($conn, $_SESSION['user_id']);
$cart_total = getCartTotal($conn, $_SESSION['user_id']);

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity <= 0) {
            removeFromCart($conn, $_SESSION['user_id'], $product_id);
        } else {
            $sql = "UPDATE tblCart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['user_id'], $product_id);
            mysqli_stmt_execute($stmt);
        }
    }
    header('Location: index.php?page=cart');
    exit();
}
?>

<div class="container" style="padding: 40px 0;">
    <h1 class="section-title">Your <span>Shopping Cart</span></h1>
    
    <?php if (mysqli_num_rows($cart_items) > 0): ?>
        <div class="cart-container">
            <div class="cart-items">
                <form method="POST" action="">
                    <?php while ($item = mysqli_fetch_assoc($cart_items)): ?>
                        <div class="cart-item">
                            <img src="<?php echo $item['image_url'] ?? 'images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="product-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                <p class="cart-item-price">R <?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="cart-item-quantity">
                                <button type="button" class="quantity-btn minus" data-product="<?php echo $item['product_id']; ?>">-</button>
                                <input type="number" name="quantity[<?php echo $item['product_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="quantity-input" data-product="<?php echo $item['product_id']; ?>">
                                <button type="button" class="quantity-btn plus" data-product="<?php echo $item['product_id']; ?>">+</button>
                            </div>
                            <div>
                                <a href="?remove=<?php echo $item['product_id']; ?>" class="remove-btn" onclick="return confirm('Remove this item?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div style="padding: 20px; text-align: right;">
                        <button type="submit" name="update_cart" class="btn-outline">Update Cart</button>
                    </div>
                </form>
            </div>
            
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>R <?php echo number_format($cart_total, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Calculated at checkout</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>R <?php echo number_format($cart_total, 2); ?></span>
                </div>
                <a href="index.php?page=checkout" class="btn-primary" style="display: block; text-align: center; margin-top: 20px;">
                    Proceed to Checkout
                </a>
                <a href="index.php?page=browse" style="display: block; text-align: center; margin-top: 12px;">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px;">
            <i class="fas fa-shopping-cart" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
            <h2>Your cart is empty</h2>
            <p style="margin-bottom: 20px;">Looks like you haven't added any items yet.</p>
            <a href="index.php?page=browse" class="btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantity-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.product;
        const input = document.querySelector(`.quantity-input[data-product="${productId}"]`);
        let value = parseInt(input.value);
        
        if (this.classList.contains('minus') && value > 0) {
            value--;
        } else if (this.classList.contains('plus')) {
            value++;
        }
        
        input.value = value;
        
        clearTimeout(window.updateTimeout);
        window.updateTimeout = setTimeout(() => {
            this.closest('form').submit();
        }, 500);
    });
});
</script>
<?php
// Product Detail Page
// Student: Vutivi & Karabo

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: index.php?page=browse');
    exit();
}

$product = getProductById($conn, $product_id);

if (!$product) {
    header('Location: index.php?page=browse');
    exit();
}

// Increment view count
$update_sql = "UPDATE tblClothes SET views = views + 1 WHERE product_id = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "i", $product_id);
mysqli_stmt_execute($update_stmt);

// Get related products (same brand or category)
$related_sql = "SELECT * FROM tblClothes WHERE status = 'approved' AND product_id != ? AND (brand = ? OR category = ?) LIMIT 4";
$related_stmt = mysqli_prepare($conn, $related_sql);
mysqli_stmt_bind_param($related_stmt, "iss", $product_id, $product['brand'], $product['category']);
mysqli_stmt_execute($related_stmt);
$related_products = mysqli_stmt_get_result($related_stmt);

// Handle message sending
$message_sent = false;
$message_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    if (!isset($_SESSION['user_id'])) {
        $login_required = true;
    } else {
        $message = sanitizeInput($_POST['message'] ?? '');
        if (!empty($message)) {
            if (sendMessage($conn, $_SESSION['user_id'], $product['seller_id'], $product_id, $message)) {
                $message_sent = true;
            } else {
                $message_error = 'Failed to send message. Please try again.';
            }
        } else {
            $message_error = 'Please enter a message.';
        }
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <!-- Breadcrumbs -->
    <div style="margin-bottom: 30px;">
        <a href="index.php?page=home" style="color: var(--grey); text-decoration: none;">Home</a>
        <span style="color: var(--grey);"> / </span>
        <a href="index.php?page=browse" style="color: var(--grey); text-decoration: none;">Browse</a>
        <span style="color: var(--grey);"> / </span>
        <span style="color: var(--pastime-green);"><?php echo htmlspecialchars($product['title']); ?></span>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if (isset($login_required)): ?>
        <div style="background: #FFF3E0; padding: 15px; border-radius: var(--radius); margin-bottom: 20px; border-left: 4px solid var(--warning-orange);">
            <i class="fas fa-exclamation-triangle"></i> Please <a href="index.php?page=login">login</a> to send messages or add items to cart.
        </div>
    <?php endif; ?>
    
    <?php if ($message_sent): ?>
        <div style="background: #E8F5E9; padding: 15px; border-radius: var(--radius); margin-bottom: 20px; border-left: 4px solid var(--success-green);">
            <i class="fas fa-check-circle"></i> Message sent to seller successfully!
        </div>
    <?php endif; ?>
    
    <?php if ($message_error): ?>
        <div style="background: #FFEBEE; padding: 15px; border-radius: var(--radius); margin-bottom: 20px; border-left: 4px solid var(--error-red);">
            <i class="fas fa-exclamation-circle"></i> <?php echo $message_error; ?>
        </div>
    <?php endif; ?>
    
    <!-- Product Main Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
        <!-- Product Images -->
        <div class="product-images">
            <div class="main-image" style="background: var(--warm-beige); border-radius: var(--radius); overflow: hidden; position: relative;">
                <img src="<?php echo getProductImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" style="width: 100%; height: auto;" onerror="this.src='images/placeholder.jpg'">
                <?php if ($product['condition'] == 'New'): ?>
                    <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: white; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">NEW WITH TAGS</span>
                <?php endif; ?>
                <?php if ($product['status'] == 'sold'): ?>
                    <span style="position: absolute; top: 20px; right: 20px; background: #F44336; color: white; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">SOLD</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="product-info-detail">
            <div class="product-brand" style="font-size: 14px; color: var(--grey); text-transform: uppercase; letter-spacing: 1px;">
                <?php echo htmlspecialchars($product['brand']); ?>
            </div>
            <h1 style="font-size: 32px; margin: 16px 0;"><?php echo htmlspecialchars($product['title']); ?></h1>
            
            <div class="product-price" style="font-size: 36px; color: var(--pastime-green); font-weight: 700; margin-bottom: 16px;">
                R <?php echo number_format($product['price'], 2); ?>
            </div>
            
            <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
                <span class="status-badge status-approved" style="background: var(--warm-beige); color: var(--charcoal); padding: 6px 12px;">
                    <i class="fas fa-tag"></i> <?php echo $product['condition']; ?>
                </span>
                <span class="status-badge" style="background: var(--warm-beige); padding: 6px 12px;">
                    <i class="fas fa-eye"></i> <?php echo $product['views']; ?> views
                </span>
                <?php if ($product['status'] == 'sold'): ?>
                    <span class="status-badge" style="background: #FFEBEE; color: var(--error-red); padding: 6px 12px;">
                        <i class="fas fa-check-circle"></i> SOLD
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Product Details Table -->
            <div style="background: var(--warm-beige); border-radius: var(--radius); padding: 20px; margin-bottom: 24px;">
                <h3 style="margin-bottom: 15px;">Product Details</h3>
                <div style="display: grid; grid-template-columns: 120px 1fr; gap: 12px;">
                    <div><strong>Category:</strong></div>
                    <div><?php echo $product['category'] ?? 'N/A'; ?></div>
                    
                    <div><strong>Size:</strong></div>
                    <div><?php echo $product['size'] ?? 'N/A'; ?></div>
                    
                    <div><strong>Colour:</strong></div>
                    <div><?php echo $product['colour'] ?? 'N/A'; ?></div>
                    
                    <div><strong>Seller:</strong></div>
                    <div><?php echo htmlspecialchars($product['seller_name']); ?></div>
                    
                    <div><strong>Listed:</strong></div>
                    <div><?php echo date('M d, Y', strtotime($product['created_at'])); ?></div>
                </div>
            </div>
            
            <!-- Description -->
            <div style="margin-bottom: 24px;">
                <h3>Description</h3>
                <div style="color: var(--grey); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?>
                </div>
            </div>
            
            <!-- Add to Cart Form with Popup -->
            <?php if ($product['status'] != 'sold'): ?>
                <div style="background: white; border: 2px solid var(--light-grey); border-radius: var(--radius); padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                        <div class="quantity-selector" style="display: flex; align-items: center; border: 1px solid var(--light-grey); border-radius: var(--radius);">
                            <button type="button" class="qty-btn minus-btn" data-id="quantity" style="padding: 12px 16px; background: none; border: none; cursor: pointer; font-size: 18px;">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" style="width: 60px; text-align: center; border: none; padding: 12px 0; font-size: 16px;">
                            <button type="button" class="qty-btn plus-btn" data-id="quantity" style="padding: 12px 16px; background: none; border: none; cursor: pointer; font-size: 18px;">+</button>
                        </div>
                        <button onclick="addToCartWithPopup(<?php echo $product_id; ?>, '<?php echo addslashes($product['title']); ?>', <?php echo $product['price']; ?>)" class="btn-primary" style="flex: 1; padding: 14px 24px; font-size: 16px;">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div style="background: #FFEBEE; border-radius: var(--radius); padding: 20px; text-align: center; margin-bottom: 24px;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: var(--error-red); margin-bottom: 10px;"></i>
                    <h3>This item has been sold</h3>
                    <p>Check out similar items below</p>
                </div>
            <?php endif; ?>
            
            <!-- Contact Seller Form -->
            <div style="border-top: 1px solid var(--light-grey); padding-top: 24px;">
                <h3><i class="fas fa-envelope"></i> Contact Seller</h3>
                <p style="color: var(--grey); font-size: 14px; margin-bottom: 15px;">Have questions about this item? Message the seller directly.</p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea name="message" rows="3" placeholder="Ask a question about this item..." style="width: 100%; padding: 12px; border: 1px solid var(--light-grey); border-radius: var(--radius); resize: vertical;"></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="index.php?page=login">login</a> to message the seller.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (mysqli_num_rows($related_products) > 0): ?>
        <section style="margin-top: 60px;">
            <h2 class="section-title">You May Also <span>Like</span></h2>
            <div class="products-grid">
                <?php while ($related = mysqli_fetch_assoc($related_products)): ?>
                    <a href="index.php?page=product&id=<?php echo $related['product_id']; ?>" class="product-card">
                        <div class="product-image">
                            <img src="<?php echo getProductImage($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?php echo htmlspecialchars($related['brand']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                            <div class="product-price">R <?php echo number_format($related['price'], 2); ?></div>
                            <span class="product-condition"><?php echo $related['condition']; ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
    .qty-btn:hover {
        background: var(--warm-beige);
    }
    .product-info-detail h3 {
        margin-bottom: 10px;
        font-size: 18px;
    }
    input[type="number"]::-webkit-inner-spin-button, 
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .btn-primary {
        background: var(--pastime-green);
        color: white;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: var(--pastime-green-dark);
        transform: translateY(-2px);
    }
    .form-group {
        margin-bottom: 15px;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        margin-top: 20px;
    }
    .product-card {
        text-decoration: none;
        color: inherit;
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .product-image {
        aspect-ratio: 1;
        overflow: hidden;
        position: relative;
    }
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-image img {
        transform: scale(1.05);
    }
    .product-info {
        padding: 16px;
    }
    .product-brand {
        font-size: 12px;
        color: var(--grey);
        text-transform: uppercase;
    }
    .product-title {
        font-size: 16px;
        font-weight: 600;
        margin: 8px 0;
    }
    .product-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--pastime-green);
    }
    .product-condition {
        display: inline-block;
        padding: 4px 8px;
        background: var(--warm-beige);
        border-radius: 20px;
        font-size: 11px;
        margin-top: 8px;
    }
    @media (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .container > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
// Quantity selector functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.minus-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-id');
            var input = document.getElementById(targetId);
            if (input) {
                var value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                }
            }
        });
    });
    
    document.querySelectorAll('.plus-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-id');
            var input = document.getElementById(targetId);
            if (input) {
                var value = parseInt(input.value);
                if (value < 99) {
                    input.value = value + 1;
                }
            }
        });
    });
});

// Add to Cart with Popup showing price (includes quantity from product page)
function addToCartWithPopup(productId, productName, productPrice) {
    // Get quantity if on product page
    var quantity = 1;
    var quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantity = parseInt(quantityInput.value) || 1;
    }
    
    var totalPrice = productPrice * quantity;
    
    // Show confirmation popup with product details
    var userConfirmed = confirm(
        "🛒 Add to Cart\n\n" +
        "Product: " + productName + "\n" +
        "Price per item: R " + parseFloat(productPrice).toFixed(2) + "\n" +
        "Quantity: " + quantity + "\n" +
        "Total: R " + totalPrice.toFixed(2) + "\n\n" +
        "Click OK to add this item to your cart."
    );
    
    if (userConfirmed) {
        // Add to cart via API
        fetch('api/add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId + '&quantity=' + quantity
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showNotification(productName + ' added to cart!', 'success');
                // Update cart count
                updateCartCount();
            } else if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                showNotification(data.error || 'Error adding to cart', 'error');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showNotification('Error adding to cart', 'error');
        });
    }
}

function updateCartCount() {
    fetch('api/cart-count.php')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            var cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = data.count || 0;
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
}

function showNotification(message, type) {
    var notification = document.createElement('div');
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    var bgColor = type === 'success' ? '#4CAF50' : '#f44336';
    
    notification.innerHTML = '<i class="fas ' + icon + '"></i> ' + message;
    notification.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; z-index: 9999; animation: slideIn 0.3s ease; background-color: ' + bgColor + '; font-weight: 500; box-shadow: 0 2px 10px rgba(0,0,0,0.2);';
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.remove();
    }, 3000);
}

// Add CSS animation if not exists
if (!document.querySelector('#notification-style')) {
    var style = document.createElement('style');
    style.id = 'notification-style';
    style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
    document.head.appendChild(style);
}
</script>
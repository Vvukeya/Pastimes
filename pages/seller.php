<?php
// pages/seller.php - View seller profile and their products

$seller_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($seller_id == 0) {
    header('Location: index.php?page=browse');
    exit();
}

// Get seller information
$sql = "SELECT * FROM tblUser WHERE user_id = ? AND is_seller_verified = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$seller = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$seller) {
    header('Location: index.php?page=browse');
    exit();
}

// Get seller's products
$products_sql = "SELECT * FROM tblClothes WHERE seller_id = ? AND status = 'approved' ORDER BY created_at DESC";
$products_stmt = mysqli_prepare($conn, $products_sql);
mysqli_stmt_bind_param($products_stmt, "i", $seller_id);
mysqli_stmt_execute($products_stmt);
$products = mysqli_stmt_get_result($products_stmt);

$product_count = mysqli_num_rows($products);

// Get follow status
$is_following = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $seller_id) {
    $is_following = isFollowing($conn, $_SESSION['user_id'], $seller_id);
}

$follower_count = getFollowerCount($conn, $seller_id);
?>

<div class="container" style="padding: 40px 0;">
    <!-- Seller Profile Header -->
    <div style="background: linear-gradient(135deg, var(--warm-beige) 0%, #E8E0D5 100%); border-radius: var(--radius); padding: 40px; margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
                <div style="background: var(--pastime-green); width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-store" style="font-size: 50px; color: white;"></i>
                </div>
                <div>
                    <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($seller['name'] . ' ' . $seller['surname']); ?></h1>
                    <p style="color: var(--grey); margin-bottom: 10px;">
                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($seller['email']); ?>
                    </p>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <span><i class="fas fa-tshirt"></i> <?php echo $product_count; ?> products</span>
                        <span><i class="fas fa-users"></i> <span id="followerCount"><?php echo $follower_count; ?></span> followers</span>
                        <span><i class="fas fa-calendar"></i> Joined <?php echo date('M Y', strtotime($seller['created_at'])); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $seller_id): ?>
                <button id="followBtn" 
                        onclick="toggleFollow(<?php echo $seller_id; ?>)"
                        class="<?php echo $is_following ? 'btn-outline' : 'btn-primary'; ?>"
                        style="min-width: 140px; padding: 12px 24px; font-size: 16px;">
                    <i class="fas <?php echo $is_following ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                    <span id="followBtnText"><?php echo $is_following ? 'Unfollow Seller' : 'Follow Seller'; ?></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Seller's Products -->
    <h2 class="section-title">Products from <span><?php echo htmlspecialchars($seller['name']); ?></span></h2>
    
    <?php if ($product_count > 0): ?>
        <div class="products-grid">
            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                <div class="product-card">
                    <a href="index.php?page=product&id=<?php echo $product['product_id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-image">
                            <img src="<?php echo getProductImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                            <div class="product-price">R <?php echo number_format($product['price'], 2); ?></div>
                            <span class="product-condition"><?php echo $product['condition']; ?></span>
                        </div>
                    </a>
                    <div style="padding: 0 16px 16px 16px;">
                        <button onclick="addToCartWithPopup(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['title']); ?>', <?php echo $product['price']; ?>)" class="add-to-cart-btn" style="background: var(--pastime-green); color: white; border: none; padding: 10px; border-radius: var(--radius); cursor: pointer; width: 100%;">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: var(--radius);">
            <i class="fas fa-box-open" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
            <h3>No products yet</h3>
            <p style="color: var(--grey);">This seller hasn't listed any items yet.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleFollow(sellerId) {
    var btn = document.getElementById('followBtn');
    var isCurrentlyFollowing = btn.classList.contains('btn-outline');
    var action = isCurrentlyFollowing ? 'unfollow' : 'follow';
    
    fetch('api/follow.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=' + action + '&following_id=' + sellerId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.is_following) {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline');
                btn.innerHTML = '<i class="fas fa-user-minus"></i> <span>Unfollow Seller</span>';
            } else {
                btn.classList.remove('btn-outline');
                btn.classList.add('btn-primary');
                btn.innerHTML = '<i class="fas fa-user-plus"></i> <span>Follow Seller</span>';
            }
            document.getElementById('followerCount').textContent = data.follower_count;
            showNotification(data.message, 'success');
        } else {
            showNotification(data.error || 'Error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error processing request', 'error');
    });
}

function showNotification(message, type) {
    var notification = document.createElement('div');
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    var bgColor = type === 'success' ? '#4CAF50' : '#f44336';
    notification.innerHTML = '<i class="fas ' + icon + '"></i> ' + message;
    notification.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; z-index: 9999; animation: slideIn 0.3s ease; background-color: ' + bgColor + '; font-weight: 500; box-shadow: 0 2px 10px rgba(0,0,0,0.2);';
    document.body.appendChild(notification);
    setTimeout(function() { notification.remove(); }, 3000);
}
</script>

<style>
.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-top: 20px;
}
.product-card {
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
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
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
.add-to-cart-btn:hover {
    background: var(--pastime-green-dark) !important;
}
@media (max-width: 1024px) {
    .products-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .products-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .products-grid { grid-template-columns: 1fr; }
}
</style>
<?php
// Home Page
// Student: Vutivi & Karabo

$featured_products = getFeaturedProducts($conn, 8);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <h1 class="hero-title">
                    Quality Branded Clothing,<br>
                    <span>Pre-Loved and Purposeful</span>
                </h1>
                <p class="hero-text">
                    Discover premium second-hand fashion at unbeatable prices. 
                    Sustainable shopping made easy with Pastimes.
                </p>
                <div class="hero-buttons">
                    <a href="index.php?page=browse" class="btn-primary">Shop Now</a>
                    <a href="#how-it-works" class="btn-outline">How It Works</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/hero-fashion.jpg" alt="Sustainable Fashion" onerror="this.src='images/placeholder.jpg'">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <h2 class="section-title">Shop by <span>Category</span></h2>
        <div class="category-grid">
            <a href="index.php?page=browse&category=Jeans" class="category-card" style="text-decoration: none;">
                <div class="category-icon"><i class="fas fa-tshirt"></i></div>
                <h3>Jeans</h3>
                <p>Premium denim</p>
            </a>
            <a href="index.php?page=browse&category=Dresses" class="category-card" style="text-decoration: none;">
                <div class="category-icon"><i class="fas fa-female"></i></div>
                <h3>Dresses</h3>
                <p>Stylish dresses</p>
            </a>
            <a href="index.php?page=browse&category=Jackets" class="category-card" style="text-decoration: none;">
                <div class="category-icon"><i class="fas fa-tshirt"></i></div>
                <h3>Jackets</h3>
                <p>Outerwear</p>
            </a>
            <a href="index.php?page=browse&category=Shoes" class="category-card" style="text-decoration: none;">
                <div class="category-icon"><i class="fas fa-shoe-prints"></i></div>
                <h3>Shoes</h3>
                <p>Footwear</p>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Featured <span>Products</span></h2>
        <div class="products-grid">
            <?php while ($product = mysqli_fetch_assoc($featured_products)): ?>
                <a href="index.php?page=product&id=<?php echo $product['product_id']; ?>" class="product-card">
                    <div class="product-image">
                        <img src="<?php echo getProductImage($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.src='images/placeholder.jpg'">
                        <?php if ($product['condition'] == 'New'): ?>
                            <span style="position: absolute; top: 10px; left: 10px; background: var(--gold); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">NEW</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <div class="product-price">R <?php echo number_format($product['price'], 2); ?></div>
                        <span class="product-condition"><?php echo $product['condition']; ?></span>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php?page=browse" class="btn-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <h2 class="section-title">How <span>It Works</span></h2>
        <div class="steps-grid">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Browse & Discover</h3>
                <p>Explore our curated collection of pre-loved branded clothing</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Add to Cart</h3>
                <p>Select your favorite items and add them to your shopping cart</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Checkout & Enjoy</h3>
                <p>Complete your purchase and receive your sustainable fashion</p>
            </div>
        </div>
    </div>
</section>

<!-- Impact Section -->
<section class="categories" style="background: var(--warm-beige);">
    <div class="container">
        <h2 class="section-title">Our <span>Impact</span></h2>
        <div class="category-grid">
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-leaf"></i></div>
                <h3>10,000+</h3>
                <p>Items Saved from Landfill</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-users"></i></div>
                <h3>5,000+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-chart-line"></i></div>
                <h3>50,000+</h3>
                <p>Items Sold</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-heart"></i></div>
                <h3>98%</h3>
                <p>Customer Satisfaction</p>
            </div>
        </div>
    </div>
</section>
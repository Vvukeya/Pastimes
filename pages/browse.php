<?php
// Browse Products Page
// Student: Vutivi & Karabo

// Get filter parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$brand = isset($_GET['brand']) ? sanitizeInput($_GET['brand']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$condition = isset($_GET['condition']) ? sanitizeInput($_GET['condition']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : 10000;
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';

// Build query
$sql = "SELECT * FROM tblClothes WHERE status = 'approved'";
$params = [];
$types = "";

if ($search) {
    $sql .= " AND (title LIKE ? OR brand LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($brand) {
    $sql .= " AND brand = ?";
    $params[] = $brand;
    $types .= "s";
}

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($condition) {
    $sql .= " AND `condition` = ?";
    $params[] = $condition;
    $types .= "s";
}

if ($min_price > 0) {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price < 10000) {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
}

// Execute query
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
} else {
    $products = mysqli_query($conn, $sql);
}

// Get unique brands for filter
$brands_sql = "SELECT DISTINCT brand FROM tblClothes WHERE status = 'approved' ORDER BY brand";
$brands_result = mysqli_query($conn, $brands_sql);
?>

<div class="container" style="padding: 40px 0;">
    <h1 class="section-title">Browse <span>Products</span></h1>
    
    <!-- Hero Banner -->
    <div style="background: linear-gradient(135deg, var(--pastime-green) 0%, var(--pastime-green-dark) 100%); border-radius: var(--radius); padding: 40px; margin-bottom: 40px; color: white; text-align: center;">
        <h2 style="color: white; margin-bottom: 10px;">Sustainable Fashion Awaits</h2>
        <p style="margin-bottom: 20px;">Discover pre-loved branded clothing at unbeatable prices</p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;"> 500+ Items</span>
            <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;"> 50+ Brands</span>
            <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px;"> Eco-Friendly</span>
        </div>
    </div>
    
    <!-- Category Quick Links -->
    <div class="category-grid" style="margin-bottom: 40px;">
        <a href="index.php?page=browse" class="category-card" style="text-decoration: none; <?php echo !$category ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-th-large"></i></div>
            <h3>All</h3>
            <p>All Products</p>
        </a>
        <a href="index.php?page=browse&category=Jeans" class="category-card" style="text-decoration: none; <?php echo $category == 'Jeans' ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-tshirt"></i></div>
            <h3>Jeans</h3>
            <p>Denim Collection</p>
        </a>
        <a href="index.php?page=browse&category=Dresses" class="category-card" style="text-decoration: none; <?php echo $category == 'Dresses' ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-female"></i></div>
            <h3>Dresses</h3>
            <p>Stylish Dresses</p>
        </a>
        <a href="index.php?page=browse&category=Jackets" class="category-card" style="text-decoration: none; <?php echo $category == 'Jackets' ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-tshirt"></i></div>
            <h3>Jackets</h3>
            <p>Outerwear</p>
        </a>
        <a href="index.php?page=browse&category=Shoes" class="category-card" style="text-decoration: none; <?php echo $category == 'Shoes' ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-shoe-prints"></i></div>
            <h3>Shoes</h3>
            <p>Footwear</p>
        </a>
        <a href="index.php?page=browse&category=Accessories" class="category-card" style="text-decoration: none; <?php echo $category == 'Accessories' ? 'border: 2px solid var(--pastime-green);' : ''; ?>">
            <div class="category-icon"><i class="fas fa-glasses"></i></div>
            <h3>Accessories</h3>
            <p>Bags & More</p>
        </a>
    </div>
    
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <!-- Filters Sidebar -->
        <aside style="width: 280px; flex-shrink: 0;">
            <div class="filter-section" style="background: var(--warm-beige); padding: 24px; border-radius: var(--radius); position: sticky; top: 100px;">
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                
                <form method="GET" action="" id="filterForm">
                    <input type="hidden" name="page" value="browse">
                    
                    <div class="form-group">
                        <label for="search"><i class="fas fa-search"></i> Search</label>
                        <input type="text" id="search" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                    </div>
                    
                    <div class="form-group">
                        <label for="brand"><i class="fas fa-tag"></i> Brand</label>
                        <select id="brand" name="brand" style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                            <option value="">All Brands</option>
                            <?php while ($b = mysqli_fetch_assoc($brands_result)): ?>
                                <option value="<?php echo htmlspecialchars($b['brand']); ?>" <?php echo $brand == $b['brand'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b['brand']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="condition"><i class="fas fa-star"></i> Condition</label>
                        <select id="condition" name="condition" style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                            <option value="">All Conditions</option>
                            <option value="New" <?php echo $condition == 'New' ? 'selected' : ''; ?>>New with Tags</option>
                            <option value="Like New" <?php echo $condition == 'Like New' ? 'selected' : ''; ?>>Like New</option>
                            <option value="Good" <?php echo $condition == 'Good' ? 'selected' : ''; ?>>Good</option>
                            <option value="Fair" <?php echo $condition == 'Fair' ? 'selected' : ''; ?>>Fair</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> Price Range</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price ?: ''; ?>" step="10" style="width: 50%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                            <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price != 10000 ? $max_price : ''; ?>" step="10" style="width: 50%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort"><i class="fas fa-sort"></i> Sort By</label>
                        <select id="sort" name="sort" style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--light-grey);">
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    
                    <a href="index.php?page=browse" style="display: block; text-align: center; margin-top: 15px; color: var(--grey);">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </form>
            </div>
        </aside>
        
        <!-- Products Grid -->
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 10px;">
                <p style="color: var(--grey);">
                    <i class="fas fa-box"></i> <?php echo mysqli_num_rows($products); ?> products found
                </p>
                <div style="display: flex; gap: 10px;">
                    <button onclick="document.getElementById('filterForm').submit();" class="btn-outline" style="padding: 8px 16px;">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="products-grid">
                <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php while ($product = mysqli_fetch_assoc($products)): ?>
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
                <?php else: ?>
                    <div style="text-align: center; padding: 60px; grid-column: 1/-1; background: white; border-radius: var(--radius);">
                        <i class="fas fa-search" style="font-size: 64px; color: var(--grey); margin-bottom: 20px;"></i>
                        <h3>No products found</h3>
                        <p style="color: var(--grey); margin-bottom: 20px;">Try adjusting your filters or search terms</p>
                        <a href="index.php?page=browse" class="btn-primary">Clear All Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card {
        position: relative;
    }
    .product-image {
        position: relative;
    }
</style>
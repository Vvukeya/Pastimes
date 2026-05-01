<?php
// Get current page for active class
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes - Second Hand Branded Clothing Marketplace</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .nav-link.active {
            color: var(--pastime-green);
            font-weight: 600;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        .user-dropdown:hover .dropdown-menu {
            display: block;
        }
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        .notification-success { background-color: #4CAF50; }
        .notification-error { background-color: #f44336; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="index.php?page=home" class="logo">
                    <span class="logo-green">PAST</span><span class="logo-gold">IMES</span>
                </a>
                
  <nav class="nav">
    <a href="index.php?page=home" class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
    <a href="index.php?page=browse" class="nav-link <?php echo ($current_page == 'browse') ? 'active' : ''; ?>">Browse</a>
    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_seller_verified']) && $_SESSION['is_seller_verified'] == 1): ?>
        <a href="index.php?page=sell" class="nav-link <?php echo ($current_page == 'sell') ? 'active' : ''; ?>">Sell</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?page=dashboard" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
    <?php endif; ?>
</nav>
                
                <div class="header-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php?page=cart" class="cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </a>
                        <div class="user-dropdown">
                            <button class="user-btn">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                                <a href="index.php?page=cart"><i class="fas fa-shopping-cart"></i> Cart</a>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <hr>
                                    <a href="admin/index.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                                <?php endif; ?>
                                <hr>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn-outline">Login</a>
                        <a href="index.php?page=register" class="btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main>

# PASTIMES E-COMMERCE PLATFORM

## Second-Hand Branded Clothing Marketplace

---

## PROJECT OVERVIEW

Pastimes is a fully functional web-based e-commerce platform designed for trading second-hand branded clothing. The platform connects sellers who wish to declutter their wardrobes with buyers seeking quality branded items at affordable prices. Unlike traditional classifieds or marketplace platforms, Pastimes employs an administrator-mediated model where platform staff handle the complex tasks of photography, description writing, pricing, and listing management, while sellers simply register interest and ship their items.

The platform was developed as a complete web application using PHP for backend logic, MySQL for database management, HTML5, CSS3, and JavaScript for frontend presentation. The system implements user authentication with role-based access control, product management with administrator approval workflow, shopping cart functionality, checkout processing with delivery information capture, and a comprehensive messaging system for buyer-seller communication.

---

## STUDENT INFORMATION

| Detail | Information |
|--------|-------------|
| **Student Names** | Vutivi & Karabo |
| **Student Numbers** | ST10445789 & ST10461176 |
| **Module Code** | WEDE6021/w |
| **Module Name** | Web Development (Intermediate) |
| **Institution** | The Independent Institute of Education (Pty) Ltd |
| **Submission Date** | June 2026 |

---

## DECLARATION OF ORIGINAL WORK

We hereby declare that the work contained in this assignment is our own original work except where explicitly stated and referenced. All code, planning, and design is our own, and any external sources have been properly acknowledged. This work has not been submitted for any other assessment purpose. We have read and understood the institution's policy on plagiarism and confirm that this submission adheres to those guidelines.

---

## TECHNICAL REQUIREMENTS

### Server Requirements

| Requirement | Version/Configuration |
|-------------|----------------------|
| **Web Server** | Apache 2.4+ (XAMPP / WAMP / MAMP) |
| **PHP Version** | PHP 7.4 or higher |
| **Database** | MySQL 5.7+ or MariaDB 10.2+ |
| **Extensions** | MySQLi, GD Library (for images), Session, JSON |

### Client Requirements

| Requirement | Specification |
|-------------|---------------|
| **Browser** | Chrome 90+, Firefox 88+, Edge 90+, Safari 14+ |
| **JavaScript** | Enabled (required for AJAX functionality) |
| **Screen Resolution** | 1024x768 or higher recommended |
| **Internet Connection** | Required for Google Fonts and Font Awesome CDN |

### Development Environment

| Tool | Purpose |
|------|---------|
| **XAMPP / WAMP / MAMP** | Local server environment |
| **phpMyAdmin** | Database management |
| **Visual Studio Code** | Code editor |
| **Git** | Version control |

---

## INSTALLATION GUIDE

### Step 1: Install Local Server Environment

**For Windows Users (XAMPP):**
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP in `C:\xampp\`
3. Open XAMPP Control Panel
4. Start Apache and MySQL services

**For Windows Users (WAMP):**
1. Download WAMP from https://www.wampserver.com/
2. Install WAMP in `C:\wamp\`
3. Launch WAMP and wait for icon to turn green

**For Mac Users (MAMP):**
1. Download MAMP from https://www.mamp.info/
2. Install MAMP in Applications folder
3. Start MAMP servers

### Step 2: Extract Project Files

1. Extract the Pastimes project folder
2. Copy the `pastimes` folder to your web server's root directory:
   - XAMPP: `C:\xampp\htdocs\pastimes\`
   - WAMP: `C:\wamp\www\pastimes\`
   - MAMP: `/Applications/MAMP/htdocs/pastimes/`

### Step 3: Create the Database

**Option A: Using phpMyAdmin (Recommended)**
1. Open your browser and navigate to `http://localhost/phpmyadmin/`
2. Click on "New" to create a database
3. Enter database name: `ClothingStore`
4. Select "utf8mb4_general_ci" as collation
5. Click "Create"
6. Click on the "Import" tab
7. Click "Choose File" and select `pastimes/scripts/myClothingStore.sql`
8. Click "Go" to import the database structure and sample data

**Option B: Using the Setup Script**
1. Navigate to `http://localhost/pastimes/scripts/loadClothingStore.php`
2. The script will automatically create all tables and load sample data
3. Navigate to `http://localhost/pastimes/scripts/createTable.php`
4. This will create and populate the tblUser table

### Step 4: Configure Database Connection

1. Open `pastimes/config/database.php`
2. Verify or update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for XAMPP, 'root' for MAMP
define('DB_NAME', 'ClothingStore');
```

### Step 5: Set Folder Permissions

**For Windows:**
- Right-click the `uploads` folder
- Select Properties > Security
- Add "Everyone" with Write permissions

**For Mac/Linux:**
```bash
chmod -R 755 pastimes/uploads/
chmod -R 755 pastimes/images/
```

### Step 6: Access the Application

| Page | URL |
|------|-----|
| **Homepage** | `http://localhost/pastimes/` |
| **Browse Products** | `http://localhost/pastimes/index.php?page=browse` |
| **User Login** | `http://localhost/pastimes/index.php?page=login` |
| **User Registration** | `http://localhost/pastimes/index.php?page=register` |
| **Admin Panel** | `http://localhost/pastimes/admin/` |

---

## LOGIN CREDENTIALS

### Administrator Account

| Field | Value |
|-------|-------|
| **Username** | admin |
| **Email** | admin@pastimes.com |
| **Password** | admin123 |
| **Role** | Administrator |

### Test User Accounts (Verified Sellers)

| Username | Password | Status |
|----------|----------|--------|
| johnsmith | password123 | Verified Seller |
| sarahj | password123 | Verified Seller |
| emilyd | password123 | Verified Seller |
| lisam | password123 | Verified Seller |
| mariaa | password123 | Verified Seller |

### Test User Accounts (Regular Buyers)

| Username | Password | Status |
|----------|----------|--------|
| mbrown | password123 | Verified User |
| dwilson | password123 | Verified User |
| jamest | password123 | Verified User |
| robertt | password123 | Verified User |
| jenj | password123 | Verified User |

### Pending Approval Users (For Admin Testing)

| Username | Password | Status |
|----------|----------|--------|
| [Various] | password123 | Pending Verification |

---

## PROJECT FILE STRUCTURE

```
pastimes/
│
├── index.php                    # Main entry point with routing
├── logout.php                   # User logout handler
│
├── config/
│   └── database.php             # Database configuration and connection
│
├── includes/
│   ├── header.php               # Page header with navigation
│   ├── footer.php               # Page footer with copyright
│   ├── auth.php                 # Authentication helper functions
│   └── functions.php            # Core business logic functions
│
├── pages/
│   ├── home.php                 # Landing page with hero and featured products
│   ├── register.php             # User registration form
│   ├── login.php                # User login authentication
│   ├── browse.php               # Product browsing with filters
│   ├── product.php              # Individual product details
│   ├── cart.php                 # Shopping cart management
│   ├── checkout.php             # Order checkout process
│   ├── order-success.php        # Order confirmation page
│   ├── dashboard.php            # User dashboard (orders, listings, messages)
│   └── sell.php                 # Seller item submission form
│
├── admin/
│   ├── index.php                # Admin dashboard with statistics
│   ├── products.php             # Product management CRUD
│   ├── pending-approvals.php    # User and product approvals
│   ├── orders.php               # Order status management
│   ├── users.php                # User management CRUD
│   ├── messages.php             # Message oversight
│   └── product-edit.php         # Product add/edit form
│
├── api/
│   ├── add-to-cart.php          # AJAX add to cart endpoint
│   ├── cart-count.php           # AJAX cart count endpoint
│   └── update-cart.php          # AJAX cart update endpoint
│
├── css/
│   └── style.css                # Main stylesheet
│
├── js/
│   └── main.js                  # Client-side JavaScript
│
├── images/
│   ├── products/                # Product photographs (30+ files)
│   ├── categories/              # Category icon images
│   ├── hero/                    # Hero banner images
│   ├── social/                  # Social media icons
│   └── placeholder.jpg          # Fallback placeholder image
│
├── uploads/                     # User-uploaded product images
│   └── .htaccess               # Security configuration
│
├── scripts/
│   ├── DBConn.php               # Database connection for scripts
│   ├── createTable.php          # Table creation and data loading
│   ├── loadClothingStore.php    # Complete database setup
│   └── myClothingStore.sql      # SQL export with DDL statements
│
└── data/
    ├── userData.txt             # Sample user data for loading
    └── products.txt             # Sample product data
```

---

## DATABASE SCHEMA

### Database Name: `ClothingStore`

| Table Name | Description | Key Fields |
|------------|-------------|------------|
| **tblUser** | User accounts and authentication | user_id (PK), email, username, password_hash, is_verified, is_seller_verified, role |
| **tblAdmin** | Administrator extensions | admin_id (PK), user_id (FK), permissions |
| **tblClothes** | Product listings | product_id (PK), seller_id (FK), title, brand, price, status |
| **tblAorder** | Customer orders | order_id (PK), user_id (FK), order_number, total_amount, status |
| **tblOrderItems** | Order line items | item_id (PK), order_id (FK), product_id (FK), quantity, price |
| **tblCart** | Shopping cart items | cart_id (PK), user_id (FK), product_id (FK), quantity |
| **tblMessages** | Buyer-seller communications | message_id (PK), sender_id (FK), receiver_id (FK), message_text |
| **tblSellerRequests** | Seller applications | request_id (PK), seller_id (FK), status |
| **tblAdminLogs** | Administrator action audit | log_id (PK), admin_id (FK), action, timestamp |

---

## FEATURES IMPLEMENTED

### User Features (Buyer)

| Feature | Description | Status |
|---------|-------------|--------|
| User Registration | Create account with name, email, username, password (8+ chars) | ✅ Complete |
| User Login | Authenticate with username/email + password | ✅ Complete |
| Browse Products | View all approved products with search and filters | ✅ Complete |
| Product Details | View detailed product information and images | ✅ Complete |
| Shopping Cart | Add items, update quantities, remove items | ✅ Complete |
| Checkout | Enter delivery address, select payment method | ✅ Complete |
| Order History | View all past orders with status tracking | ✅ Complete |
| Contact Seller | Send messages to sellers about products | ✅ Complete |
| View Messages | Read and reply to messages from sellers | ✅ Complete |
| Profile Management | Update personal information and delivery address | ✅ Complete |

### Seller Features

| Feature | Description | Status |
|---------|-------------|--------|
| Seller Verification | Admin approval required before selling | ✅ Complete |
| Submit Items | Upload product details and images for review | ✅ Complete |
| View Listings | Track submitted items and their approval status | ✅ Complete |
| Respond to Buyers | Reply to buyer messages about listings | ✅ Complete |

### Administrator Features

| Feature | Description | Status |
|---------|-------------|--------|
| Admin Login | Separate authentication with email-based username | ✅ Complete |
| User Verification | Approve pending user registrations | ✅ Complete |
| Seller Verification | Grant seller privileges to verified users | ✅ Complete |
| Product Approval | Review and approve/reject seller submissions | ✅ Complete |
| Product Management | Add, edit, delete products | ✅ Complete |
| User Management | Add, update, delete user accounts | ✅ Complete |
| Order Management | Update order status (pending/shipped/delivered) | ✅ Complete |
| Message Oversight | View all buyer-seller communications | ✅ Complete |
| Dashboard Statistics | View platform metrics and recent activity | ✅ Complete |

### Technical Features

| Feature | Description | Status |
|---------|-------------|--------|
| Password Hashing | MD5 hashing for sample data, ready for password_hash | ✅ Complete |
| Session Management | User state maintained across pages | ✅ Complete |
| Sticky Forms | Preserve input values on validation errors | ✅ Complete |
| AJAX Operations | Add to cart without page reload | ✅ Complete |
| Responsive Design | Mobile-friendly layout with media queries | ✅ Complete |
| SQL Injection Prevention | Prepared statements with parameter binding | ✅ Complete |
| XSS Protection | HTML escaping of user input | ✅ Complete |
| File Upload | Product image upload with validation | ✅ Complete |

---

## API ENDPOINTS

| Endpoint | Method | Purpose | Response |
|----------|--------|---------|----------|
| `api/add-to-cart.php` | POST | Add product to cart | JSON `{success, redirect}` |
| `api/cart-count.php` | GET | Get cart item count | JSON `{count}` |
| `api/update-cart.php` | POST | Update cart quantity | JSON `{success}` |
| `api/remove-from-cart.php` | POST | Remove cart item | JSON `{success}` |

---

## ROUTING SYSTEM

The application uses a simple but effective routing system based on URL parameters:

| URL Pattern | Page Loaded | Authentication Required |
|-------------|-------------|------------------------|
| `index.php?page=home` | home.php | No |
| `index.php?page=register` | register.php | No |
| `index.php?page=login` | login.php | No |
| `index.php?page=browse` | browse.php | No |
| `index.php?page=product&id=X` | product.php | No |
| `index.php?page=cart` | cart.php | Yes |
| `index.php?page=checkout` | checkout.php | Yes |
| `index.php?page=dashboard` | dashboard.php | Yes |
| `index.php?page=dashboard&tab=orders` | dashboard.php (Orders tab) | Yes |
| `index.php?page=dashboard&tab=listings` | dashboard.php (Listings tab) | Yes |
| `index.php?page=dashboard&tab=messages` | dashboard.php (Messages tab) | Yes |
| `index.php?page=dashboard&tab=profile` | dashboard.php (Profile tab) | Yes |
| `index.php?page=sell` | sell.php | Yes (Seller only) |
| `index.php?page=order-success&order=X` | order-success.php | Yes |
| `admin/index.php` | Admin Dashboard | Yes (Admin only) |

---

## STYLING AND DESIGN

### Colour Palette

| Colour Name | Hex Code | Usage |
|-------------|----------|-------|
| **Pastime Green** | `#2E7D32` | Primary buttons, links, prices |
| **Pastime Green Dark** | `#1B5E20` | Button hover states |
| **Warm Beige** | `#F5F0E8` | Background accents, cards |
| **Gold** | `#C6A43F` | Accents, badges, new tags |
| **Charcoal** | `#333333` | Primary text |
| **Grey** | `#666666` | Secondary text |
| **Light Grey** | `#E0E0E0` | Borders, dividers |

### Typography

| Font | Usage | Weights |
|------|-------|---------|
| **Poppins** | Headings (h1, h2, h3, h4) | 400, 500, 600, 700, 800 |
| **Inter** | Body text, navigation, buttons | 300, 400, 500, 600 |

### Responsive Breakpoints

| Screen Width | Grid Columns | Layout Adjustments |
|--------------|--------------|---------------------|
| > 1024px | 4 columns | Full desktop layout |
| 768px - 1024px | 3 columns | Tablet layout |
| 480px - 768px | 2 columns | Large phone layout |
| < 480px | 1 column | Mobile layout |

---

## TROUBLESHOOTING

### Common Issues and Solutions

**Issue: Database connection error**
- Solution: Verify MySQL service is running in XAMPP/WAMP
- Check database credentials in `config/database.php`
- Ensure database name 'ClothingStore' exists

**Issue: 404 Not Found error**
- Solution: Ensure files are in correct htdocs folder
- Restart Apache service
- Check URL is `http://localhost/pastimes/`

**Issue: Images not displaying**
- Solution: Verify image files exist in `images/products/` folder
- Check file permissions (755 for folders, 644 for files)
- Run `test_upload.php` to debug

**Issue: Upload failing on sell page**
- Solution: Create `uploads` folder with write permissions
- Check PHP upload limits in php.ini
- Verify file size is under 5MB

**Issue: Session not persisting**
- Solution: Clear browser cookies and cache
- Check `session_start()` is called at beginning of files
- Verify no output before session_start()

**Issue: Admin panel access denied**
- Solution: Login with admin credentials first
- Verify role in database is set to 'admin'
- Check session variables after login

**Issue: Cart count not updating**
- Solution: Clear browser cache
- Check JavaScript console for errors
- Verify API endpoints are accessible

---

## SECURITY CONSIDERATIONS

The following security measures have been implemented:

| Security Measure | Implementation |
|------------------|----------------|
| **SQL Injection Prevention** | Prepared statements with parameter binding (MySQLi) |
| **XSS Protection** | htmlspecialchars() on all user output |
| **Password Protection** | MD5 hashing (sample data) / password_hash (ready) |
| **Session Security** | Session regeneration on login |
| **File Upload Security** | File type validation, unique filename generation |
| **Access Control** | Role-based access control (user/admin) |
| **Directory Protection** | .htaccess to prevent directory listing |

---

## TESTING THE APPLICATION

### Test Scenarios

1. **User Registration and Login**
   - Register a new user account
   - Verify pending status message appears
   - Login as admin to verify the user
   - Login as the new verified user

2. **Product Browsing and Filtering**
   - Browse all products
   - Search by keyword
   - Filter by brand, category, condition
   - Sort by price and date

3. **Shopping Cart Operations**
   - Add product to cart
   - Update quantity using +/- buttons
   - Remove item from cart
   - Proceed to checkout

4. **Checkout Process**
   - Enter delivery address
   - Select payment method
   - Place order
   - View order confirmation

5. **Seller Functionality**
   - Login as verified seller
   - Submit new item with image
   - View pending listing
   - Login as admin to approve

6. **Admin Panel**
   - Verify pending users
   - Approve seller requests
   - Manage products
   - Update order status
   - Manage user accounts

7. **Messaging System**
   - Send message from product page
   - View message in dashboard
   - Reply to message
   - View conversation thread

---

## RUBRIC REQUIREMENTS MAPPING

| Rubric Requirement | Implementation Location | Status |
|--------------------|------------------------|--------|
| User Table Structure and DB Connection | `config/database.php`, `scripts/myClothingStore.sql` | ✅ |
| Text file with five names | `data/userData.txt` | ✅ |
| Data preloaded into tbl_user | `scripts/createTable.php` | ✅ |
| DBConn.php include statement | `scripts/createTable.php` | ✅ |
| Code makes a connection | `config/database.php` | ✅ |
| createTable.php delete/create/load | `scripts/createTable.php` | ✅ |
| Login Page | `pages/login.php` | ✅ |
| Password hash validation | `pages/login.php` | ✅ |
| Sticky form on incorrect password | `pages/login.php` | ✅ |
| Associative columns fetched | `includes/functions.php` (getProductById) | ✅ |
| User pending registration list | `pages/register.php`, `admin/pending-approvals.php` | ✅ |
| User page after verification | `pages/dashboard.php` | ✅ |
| Clothes table structure | `scripts/myClothingStore.sql` (tblClothes) | ✅ |
| Images folder with 5+ JPG files | `images/products/` (30+ files) | ✅ |
| tbl_Item associative array display | `pages/browse.php`, `pages/home.php` | ✅ |
| AddToCart button with picture | `pages/product.php` | ✅ |
| SellPrice popup on AddToCart | `js/main.js` (addToCart function) | ✅ |
| Admin login with email username | `admin/index.php` | ✅ |
| User verification by admin | `admin/pending-approvals.php` | ✅ |
| Add, update, delete users | `admin/users.php` | ✅ |
| Coding standards (comments, naming) | All PHP files | ✅ |
| CSS professional look and feel | `css/style.css` | ✅ |
| Demonstration video | Included in submission | ✅ |

---

## IMAGES DIRECTORY STRUCTURE

### Root Images Folder

The root images folder serves as the central container for all visual assets used across the Pastimes e-commerce platform. The folder is strategically organized with subdirectories that separate different categories of images, ensuring efficient file management and logical asset grouping. The folder structure includes a products subdirectory for product photographs organized by item, a categories subdirectory for category card icons displayed on the homepage, a hero subdirectory for banner images used in the hero section, and a social subdirectory for social media icon images displayed in the footer. The root images folder also contains the favicon.ico file for browser tab display and the placeholder.jpg image that serves as a fallback when product images are missing or unavailable.

### Products Subdirectory

The products subdirectory contains over 30 product photographs displayed throughout the Pastimes application, covering multiple categories including jeans, shoes, dresses, jackets, hoodies, t-shirts, pants, and accessories. Each image file is named using a descriptive convention that matches the product brand and type, such as levis-jeans.jpg, nike-airmax.jpg, zara-coat.jpg, adidas-hoodie.jpg, hm-dress.jpg, rayban.jpg, ck-tshirt.jpg, tommy-polo.jpg, guess-jacket.jpg, and puma-pants.jpg. Each image is sized appropriately for web display at 400 by 400 pixels, maintaining consistent aspect ratios across all product cards. The images are optimized for web delivery with appropriate compression to balance visual quality against file size, ensuring fast page loading times.

### Categories Subdirectory

The categories subdirectory contains visual icon images used in the category cards displayed on the homepage hero section. The directory includes four category images representing the main product classifications on the Pastimes platform: category-men.jpg for men's fashion, category-women.jpg for women's fashion, category-shoes.jpg for footwear, and category-accessories.jpg for accessories. Each category image is sized at 300 by 300 pixels with consistent framing and composition, ensuring uniform appearance across the category card grid.

### Hero Subdirectory

The hero subdirectory contains large banner images displayed in the hero section at the top of the homepage, creating an immediate visual impact that communicates the Pastimes brand identity and value proposition. The directory includes hero-fashion.jpg and hero-sustainable.jpg, both sized at 800 by 600 pixels and optimized for display across desktop, tablet, and mobile devices with responsive scaling.

### Social Subdirectory

The social subdirectory contains icon images used in the footer section for social media platform links. The directory includes instagram.png, facebook.png, twitter.png, and pinterest.png, each sized consistently at 24 by 24 pixels for uniform appearance across the social links row.

### Placeholder Image

The placeholder.jpg file serves as a critical fallback mechanism ensuring that the Pastimes application always displays visual content even when product images are missing, corrupted, or not yet uploaded. The image features a neutral beige background with centred text reading "No Image" and smaller "Pastimes" text below, maintaining brand consistency while clearly communicating that an image is not available.

---

## CONCLUSION

Pastimes is a complete, fully functional e-commerce platform for second-hand branded clothing that meets all requirements specified in the POE documentation. The application implements user authentication with administrator verification, product management with approval workflow, shopping cart functionality, checkout processing, order tracking, and a comprehensive messaging system. The platform is responsive, secure, and user-friendly, providing a professional solution for buying and selling pre-loved clothing online.

---

## VERSION HISTORY

| Version | Date | Description |
|---------|------|-------------|
| 1.0 | April 2026 | Part 1 - Planning and Design Document |
| 2.0 | May 2026 | Part 2 - Prototype Development |
| 3.0 | June 2026 | Final POE Submission - Complete Application |

---

**END OF README FILE**

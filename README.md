
# Pastimes

Pastimes is a PHP and MySQL second-hand clothing marketplace built for the WEDE6021/w Web Development (Intermediate) module. It supports buyer browsing, seller profiles, cart and checkout flows, messaging, seller following, and an admin back office for managing users, products, orders, and conversations.

## Student Information

| Detail | Information |
|--------|-------------|
| Student Names | Vutivi & Karabo |
| Student Numbers | ST10445789 & ST10461176 |
| Module Code | WEDE6021/w |
| Module Name | Web Development (Intermediate) |
| Institution | The Independent Institute of Education (Pty) Ltd |
| Submission Date | June 2026 |

## What The App Does

- Users can register, log in, and manage their profile, orders, messages, and saved seller relationships.
- Buyers can browse approved products in grid or table view, filter by brand, category, condition, price, and sort order, then add items to the cart.
- Product pages show full item details, seller information, follower counts, related products, and a contact form with file attachments.
- Verified sellers can list items and view their products from the dashboard.
- Admins can review users, products, orders, messages, and chat conversations from the admin panel.
- Checkout creates an order, writes order items, clears the cart, stores the last order details in session, and decrements stock.

## Key Updates In The Current Build

- `includes/ShoppingCart.php` now wraps cart actions through `AddItem`, `RemoveItem`, `Checkout`, `EmptyCart`, `Login`, and `ProcessInput`.
- Browse supports both a grid and an `eShop` table view, with direct Add to Cart and Show Cart actions.
- Guest cart and checkout access redirects to login with a context message.
- Seller following is available through `tblFollows`, with follower counts and a follow feed in the dashboard.
- Messaging supports attachments, and admins can reply from the chat monitor using `tblAdminReplies` and `tblMessages`.

## Tech Stack

- PHP 7.4+
- MySQL / MariaDB
- HTML5, CSS3, JavaScript
- Apache via XAMPP, WAMP, or MAMP

## Requirements

### Server

- Apache 2.4+ or equivalent local stack
- PHP 7.4 or newer
- MySQL 5.7+ or MariaDB 10.2+
- PHP extensions: MySQLi, Session, JSON

### Client

- Modern browser with JavaScript enabled
- Font Awesome and Google Fonts require an internet connection

## Setup

1. Copy the project folder into your web root.
   - XAMPP: `C:\xampp\htdocs\pastimes\`
   - WAMP: `C:\wamp\www\pastimes\`
   - MAMP: `/Applications/MAMP/htdocs/pastimes/`
2. Create a database named `ClothingStore`.
3. Import `scripts/myClothingStore.sql` in phpMyAdmin, or run `scripts/loadClothingStore.php`.
4. If needed, run `scripts/createTable.php` to recreate and seed `tblUser` from `data/userData.txt`.
5. Confirm the database connection in `config/database.php`.

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ClothingStore');
```

6. Make sure `uploads/` and `images/` are writable in your local environment.
7. Open the site at `http://localhost/pastimes/`.

## Main Routes

| Route | Purpose |
|-------|---------|
| `index.php?page=home` | Landing page |
| `index.php?page=browse` | Product browsing |
| `index.php?page=product&id=X` | Product details |
| `index.php?page=cart` | Shopping cart |
| `index.php?page=checkout` | Checkout form |
| `index.php?page=dashboard` | User dashboard |
| `index.php?page=sell` | Seller listing form |
| `index.php?page=seller&id=X` | Seller profile and products |
| `index.php?page=order-success&order=X` | Order confirmation |
| `admin/index.php` | Admin dashboard |

## Admin Tools

- Dashboard statistics and recent activity
- User verification and management
- Product approval and management
- Order tracking and status updates
- Message oversight and chat monitoring

## Database Tables

The current SQL dump includes these core tables:

- `tblUser`
- `tblAdmin`
- `tblClothes`
- `tblAorder`
- `tblOrderItems`
- `tblCart`
- `tblMessages`

The current codebase also expects follow and admin-reply support for seller feeds and chat replies:

- `tblFollows`
- `tblAdminReplies`

If you are starting from an older database export, make sure those tables exist before using the follow or admin chat features.

## Project Structure

```
pastimes/
├── index.php
├── logout.php
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── ShoppingCart.php
├── pages/
│   ├── browse.php
│   ├── cart.php
│   ├── checkout.php
│   ├── dashboard.php
│   ├── home.php
│   ├── login.php
│   ├── order-success.php
│   ├── product.php
│   ├── register.php
│   ├── sell.php
│   └── seller.php
├── admin/
│   ├── add-user.php
│   ├── chat-monitor.php
│   ├── index.php
│   ├── messages.php
│   ├── orders.php
│   ├── pending-approvals.php
│   ├── product-edit.php
│   ├── products.php
│   └── users.php
├── api/
│   ├── add-to-cart.php
│   ├── cart-count.php
│   ├── follow.php
│   └── update-cart.php
├── css/
│   └── style.css
├── js/
│   └── main.js
├── scripts/
│   ├── DBConn.php
│   ├── createTable.php
│   ├── loadClothingStore.php
│   └── myClothingStore.sql
├── data/
├── images/
├── uploads/
└── Pastimes/
    └── README.md
```

## Notes

- Login and seeded data still use MD5 password hashes in the sample dataset.
- Product images fall back to `images/placeholder.jpg` when an image path is missing.
- Cart and checkout logic is implemented in `includes/functions.php` and coordinated through `includes/ShoppingCart.php`.

## Troubleshooting

- If the database connection fails, verify the credentials in `config/database.php` and confirm `ClothingStore` exists.
- If browse or checkout redirects to login, that is expected for guest cart and checkout access.
- If follow or admin reply features fail on an older export, add the missing `tblFollows` and `tblAdminReplies` tables.

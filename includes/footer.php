    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">PASTIMES</h3>
                    <p class="footer-text">Quality branded clothing, pre-loved and purposeful. Sustainable fashion for a better tomorrow.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Shop</h4>
                    <ul>
                        <li><a href="index.php?page=browse">All Products</a></li>
                        <li><a href="index.php?page=browse&category=Jeans">Jeans</a></li>
                        <li><a href="index.php?page=browse&category=Dresses">Dresses</a></li>
                        <li><a href="index.php?page=browse&category=Jackets">Jackets</a></li>
                        <li><a href="index.php?page=browse&category=Shoes">Shoes</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns Policy</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Pastimes. All rights reserved. | Student: Vutivi & Karabo</p>
            </div>
        </div>
    </footer>
    
    <script src="js/main.js"></script>
    <script>
        // Update cart count on page load
        function updateCartCount() {
            fetch('api/cart-count.php')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) cartCount.textContent = data.count || 0;
                })
                .catch(error => console.error('Error:', error));
        }
        
        updateCartCount();
        
        // Add to cart function for product pages
        window.addToCart = function(productId, quantity = 1) {
            fetch('api/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Item added to cart!', 'success');
                    updateCartCount();
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showNotification(data.error || 'Error adding to cart', 'error');
                }
            });
        };
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    </script>
</body>
</html>
// Pastimes Main JavaScript
// Student: Vutivi & Karabo

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
    
    // Password strength indicator
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.addEventListener('input', checkPasswordStrength);
    });
    
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });
});

function validateForm(e) {
    const form = e.target;
    let isValid = true;
    
    // Required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showError(field, 'This field is required');
            isValid = false;
        } else {
            clearError(field);
        }
    });
    
    // Email validation
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            showError(emailField, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Password confirmation
    const password = form.querySelector('#password');
    const confirmPassword = form.querySelector('#confirm_password');
    if (password && confirmPassword) {
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
    }
    
    // Password length
    if (password && password.value && password.value.length < 8) {
        showError(password, 'Password must be at least 8 characters');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
}

function showError(field, message) {
    field.classList.add('error');
    let errorDiv = field.parentElement.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

function clearError(field) {
    field.classList.remove('error');
    const errorDiv = field.parentElement.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function checkPasswordStrength(e) {
    const password = e.target.value;
    const strengthIndicator = document.getElementById('password-strength');
    
    if (!strengthIndicator) return;
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const strengths = ['Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['#F44336', '#FF9800', '#FFC107', '#4CAF50'];
    
    strengthIndicator.textContent = strengths[strength - 1] || 'Very Weak';
    strengthIndicator.style.color = colors[strength - 1] || '#F44336';
}

function addToCart(productId) {
    fetch('api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item added to cart!', 'success');
            // Update cart count
            fetch('api/cart-count.php')
                .then(res => res.json())
                .then(data => {
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) cartCount.textContent = data.count || 0;
                });
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            showNotification(data.error || 'Error adding to cart', 'error');
        }
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 24px;
        border-radius: 8px;
        color: white;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        notification.style.backgroundColor = '#4CAF50';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#F44336';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
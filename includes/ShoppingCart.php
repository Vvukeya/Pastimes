<?php
// Shopping cart class wrapper for rubric compliance

class ShoppingCart {
    private mysqli $conn;
    private ?int $userId;

    public function __construct(mysqli $conn, ?int $userId = null) {
        $this->conn = $conn;
        $this->userId = $userId ?? (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null);
    }

    public function Login($username, $password) {
        $sql = "SELECT user_id, name, surname, username, email, password_hash, is_verified, is_seller_verified, role 
                FROM tblUser WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['is_verified'] == 0) {
                return ['success' => false, 'error' => 'Your account is pending verification.'];
            }

            if (md5($password) === $user['password_hash']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['surname'] = $user['surname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_seller_verified'] = $user['is_seller_verified'];
                $_SESSION['role'] = $user['role'];
                $this->userId = intval($user['user_id']);

                return ['success' => true, 'role' => $user['role']];
            }

            return ['success' => false, 'error' => 'Invalid password'];
        }

        return ['success' => false, 'error' => 'User not found'];
    }

    public function AddItem($productId, $quantity = 1) {
        if (!$this->userId) {
            return false;
        }

        return addToCart($this->conn, $this->userId, intval($productId), intval($quantity));
    }

    public function RemoveItem($productId) {
        if (!$this->userId) {
            return false;
        }

        return removeFromCart($this->conn, $this->userId, intval($productId));
    }

    public function Checkout($deliveryAddress, $paymentMethod) {
        if (!$this->userId) {
            return false;
        }

        return createOrder($this->conn, $this->userId, $deliveryAddress, $paymentMethod);
    }

    public function EmptyCart() {
        if (!$this->userId) {
            return false;
        }

        $sql = "DELETE FROM tblCart WHERE user_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $this->userId);
        return mysqli_stmt_execute($stmt);
    }

    public function ProcessInput(?array $input = null) {
        $input = $input ?? $_REQUEST;
        $action = $input['cart_action'] ?? '';

        if ($action === 'add' && isset($input['product_id'])) {
            return $this->AddItem($input['product_id'], $input['quantity'] ?? 1);
        }

        if ($action === 'remove' && isset($input['product_id'])) {
            return $this->RemoveItem($input['product_id']);
        }

        if ($action === 'empty') {
            return $this->EmptyCart();
        }

        if ($action === 'checkout' && isset($input['delivery_address'], $input['payment_method'])) {
            return $this->Checkout($input['delivery_address'], $input['payment_method']);
        }

        return null;
    }
}
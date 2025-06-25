<?php
require_once 'config.php';

if (!isset($_POST['action'])) {
            header('Location: ' . BASE_URL);
            exit();
}
$action = $_POST['action'];

if ($action == 'add' && isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $customization = isset($_POST['customization_details']) ? trim($_POST['customization_details']) : null;

            $stmt = mysqli_prepare($conn, "SELECT name, price, stock, image_url FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if ($product && $quantity > 0 && $product['stock'] >= $quantity) {
                        if (!isset($_SESSION['cart'])) {
                                    $_SESSION['cart'] = [];
                        }
                        $cart_item = [
                                    'id' => $product_id,
                                    'name' => $product['name'],
                                    'price' => $product['price'],
                                    'quantity' => $quantity,
                                    'image_url' => $product['image_url'],
                                    'customization' => $customization
                        ];
                        $_SESSION['cart'][] = $cart_item;
            }
            header('Location: ' . BASE_URL . 'cart');
            exit();
} elseif ($action == 'update' && isset($_POST['cart_key']) && isset($_POST['quantity'])) {
            $cart_key = (int)$_POST['cart_key'];
            $quantity = (int)$_POST['quantity'];

            if (isset($_SESSION['cart'][$cart_key])) {
                        if ($quantity > 0) {
                                    $_SESSION['cart'][$cart_key]['quantity'] = $quantity;
                        } else {
                                    unset($_SESSION['cart'][$cart_key]);
                        }
            }
            header('Location: ' . BASE_URL . 'cart');
            exit();
} elseif ($action == 'remove' && isset($_POST['cart_key'])) {
            $cart_key = (int)$_POST['cart_key'];
            if (isset($_SESSION['cart'][$cart_key])) {
                        unset($_SESSION['cart'][$cart_key]);
            }
            header('Location: ' . BASE_URL . 'cart');
            exit();
}

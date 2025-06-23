<?php
require_once 'config.php';
if (!isset($_POST['action'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$action = $_POST['action'];

if ($action == 'add' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $stmt = mysqli_prepare($conn, "SELECT name, price, stock FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if ($product && $quantity > 0 && $product['stock'] >= $quantity) {
                        if (!isset($_SESSION['cart'])) {
                                    $_SESSION['cart'] = [];
                        }
                        if (isset($_SESSION['cart'][$product_id])) {
                                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                        } else {
                                    $_SESSION['cart'][$product_id] = [
                                                'name' => $product['name'],
                                                'price' => $product['price'],
                                                'quantity' => $quantity
                                    ];
                        }
            }
            header('Location: ' . BASE_URL . 'cart');
            exit();
} elseif ($action == 'update' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                        unset($_SESSION['cart'][$product_id]);
            }
            header('Location: ' . BASE_URL . 'cart');
            exit();
} elseif ($action == 'remove' && isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            unset($_SESSION['cart'][$product_id]);
            header('Location: ' . BASE_URL . 'cart');
            exit();
} else {
            header('Location: ' . BASE_URL);
            exit();
}

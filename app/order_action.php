<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$user_id = $_SESSION['user_id'];
$shipping_address = mysqli_real_escape_string($conn, $_POST['address']);
$shipping_method = mysqli_real_escape_string($conn, $_POST['shipping_method']);
$shipping_cost = (float)$_POST['shipping_cost'];
$total_amount = (float)$_POST['total_amount'];

mysqli_begin_transaction($conn);

try {
            $stmt1 = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, status, shipping_address, shipping_method, shipping_cost) VALUES (?, ?, 'Menunggu Pembayaran', ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1, "idssd", $user_id, $total_amount, $shipping_address, $shipping_method, $shipping_cost);
            mysqli_stmt_execute($stmt1);

            $order_id = mysqli_insert_id($conn);
            $stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt3 = mysqli_prepare($conn, "UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($_SESSION['cart'] as $product_id => $item) {
                        $quantity = $item['quantity'];
                        $price = $item['price'];
                        mysqli_stmt_bind_param($stmt2, "iiid", $order_id, $product_id, $quantity, $price);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_bind_param($stmt3, "ii", $quantity, $product_id);
                        mysqli_stmt_execute($stmt3);
            }
            mysqli_commit($conn);
            unset($_SESSION['cart']);
            header('Location: ' . BASE_URL . 'order_success?order_id=' . $order_id);
            exit();
} catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            header('Location: ' . BASE_URL . 'checkout?error=order_failed');
            exit();
}

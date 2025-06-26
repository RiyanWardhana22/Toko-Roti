<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$voucher_code = $_SESSION['voucher']['code'] ?? null;
$discount_amount = $_SESSION['voucher']['discount_amount'] ?? 0;

$user_id = $_SESSION['user_id'];
$shipping_address = mysqli_real_escape_string($conn, $_POST['address']);
$shipping_method = mysqli_real_escape_string($conn, $_POST['shipping_method']);
$shipping_cost = (float)$_POST['shipping_cost'];
$total_amount = (float)$_POST['total_amount'];

mysqli_begin_transaction($conn);

try {
            $stmt1 = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, status, shipping_address, shipping_method, shipping_cost, voucher_code, discount_amount) VALUES (?, ?, 'Menunggu Pembayaran', ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1, "idssdsd", $user_id, $total_amount, $shipping_address, $shipping_method, $shipping_cost, $voucher_code, $discount_amount);
            mysqli_stmt_execute($stmt1);
            $order_id = mysqli_insert_id($conn);

            $stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price, customization_details) VALUES (?, ?, ?, ?, ?)");
            $stmt3 = mysqli_prepare($conn, "UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($_SESSION['cart'] as $item) {
                        mysqli_stmt_bind_param($stmt2, "iiids", $order_id, $item['id'], $item['quantity'], $item['price'], $item['customization']);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_bind_param($stmt3, "ii", $item['quantity'], $item['id']);
                        mysqli_stmt_execute($stmt3);
            }

            if ($voucher_code) {
                        mysqli_query($conn, "UPDATE vouchers SET usage_count = usage_count + 1 WHERE code = '$voucher_code'");
            }
            mysqli_commit($conn);

            unset($_SESSION['cart']);
            unset($_SESSION['voucher']);

            header('Location: ' . BASE_URL . 'order_success?order_id=' . $order_id);
            exit();
} catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            header('Location: ' . BASE_URL . 'checkout?error=order_failed');
            exit();
}

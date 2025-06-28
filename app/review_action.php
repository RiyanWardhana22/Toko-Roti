<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_POST['order_id'];

if (isset($_POST['rating']) && is_array($_POST['rating'])) {
            foreach ($_POST['rating'] as $product_id => $rating) {
                        $product_id = (int)$product_id;
                        $rating = (int)$rating;
                        $comment = isset($_POST['comment'][$product_id]) ? trim($_POST['comment'][$product_id]) : '';
                        $comment = mysqli_real_escape_string($conn, $comment);

                        $stmt1 = mysqli_prepare($conn, "INSERT INTO reviews (product_id, user_id, order_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, ?, 1)");
                        mysqli_stmt_bind_param($stmt1, "iiiis", $product_id, $user_id, $order_id, $rating, $comment);
                        mysqli_stmt_execute($stmt1);

                        $stmt2 = mysqli_prepare($conn, "UPDATE order_items SET has_reviewed = 1 WHERE order_id = ? AND product_id = ?");
                        mysqli_stmt_bind_param($stmt2, "ii", $order_id, $product_id);
                        mysqli_stmt_execute($stmt2);
            }
}

header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&review_status=success');
exit();

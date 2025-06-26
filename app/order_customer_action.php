<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['action']) || !isset($_GET['id'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['id'];
$action = $_GET['action'];

if ($action == 'complete') {
            $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'Selesai' WHERE id = ? AND user_id = ? AND status = 'Dikirim'");
            mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
            mysqli_stmt_execute($stmt);
}

header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&status=completed');
exit();

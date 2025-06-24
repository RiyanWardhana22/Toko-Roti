<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_POST['order_id'];

function handle_proof_image_upload($file_input_name, $order_id)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = '../assets/images/proofs/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                        $file_extension = pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION);
                        $file_name = "proof-order-" . $order_id . "-" . time() . "." . $file_extension;
                        $target_file = $upload_dir . $file_name;

                        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_file)) {
                                    return $file_name;
                        }
            }
            return null;
}
$bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
$account_holder = mysqli_real_escape_string($conn, $_POST['account_holder']);
$transfer_amount = (float)$_POST['transfer_amount'];
$transfer_date = $_POST['transfer_date'];

$proof_image_name = handle_proof_image_upload('proof_image', $order_id);
if (!$proof_image_name) {
            die("Error: Gagal mengupload bukti pembayaran. Pastikan file adalah gambar dan ukurannya tidak terlalu besar.");
}

mysqli_begin_transaction($conn);

try {
            $stmt1 = mysqli_prepare($conn, "INSERT INTO payment_confirmations (order_id, bank_name, account_holder, transfer_amount, transfer_date, proof_image_url) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1, "issdss", $order_id, $bank_name, $account_holder, $transfer_amount, $transfer_date, $proof_image_name);
            mysqli_stmt_execute($stmt1);

            $new_status = 'Menunggu Verifikasi';
            $stmt2 = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ? AND user_id = ?");
            mysqli_stmt_bind_param($stmt2, "sii", $new_status, $order_id, $user_id);
            mysqli_stmt_execute($stmt2);
            mysqli_commit($conn);
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&status=confirm_success');
            exit();
} catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            header('Location: ' . BASE_URL . 'payment_confirmation?id=' . $order_id . '&error=failed');
            exit();
}

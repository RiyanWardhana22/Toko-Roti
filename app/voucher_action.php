<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['action'])) {
            echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
            exit();
}

if ($_POST['action'] == 'apply_voucher') {
            $code = strtoupper(mysqli_real_escape_string($conn, $_POST['voucher_code']));
            $subtotal = (float)$_POST['subtotal'];

            $stmt = mysqli_prepare($conn, "SELECT * FROM vouchers WHERE code = ? AND is_active = 1");
            mysqli_stmt_bind_param($stmt, "s", $code);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $voucher = mysqli_fetch_assoc($result);

            if (!$voucher) {
                        echo json_encode(['status' => 'error', 'message' => 'Kode voucher tidak ditemukan atau tidak aktif.']);
                        exit();
            }
            if ($voucher['expires_at'] && strtotime($voucher['expires_at']) < time()) {
                        echo json_encode(['status' => 'error', 'message' => 'Kode voucher sudah kedaluwarsa.']);
                        exit();
            }
            if ($voucher['usage_count'] >= $voucher['usage_limit']) {
                        echo json_encode(['status' => 'error', 'message' => 'Limit penggunaan voucher sudah habis.']);
                        exit();
            }
            if ($subtotal < $voucher['min_purchase']) {
                        echo json_encode(['status' => 'error', 'message' => 'Minimal belanja tidak tercapai untuk menggunakan voucher ini.']);
                        exit();
            }

            $discount_amount = 0;
            if ($voucher['type'] == 'percentage') {
                        $discount_amount = ($voucher['value'] / 100) * $subtotal;
            } else {
                        $discount_amount = $voucher['value'];
            }

            $_SESSION['voucher'] = [
                        'code' => $voucher['code'],
                        'type' => $voucher['type'],
                        'value' => $voucher['value'],
                        'discount_amount' => $discount_amount
            ];

            echo json_encode([
                        'status' => 'success',
                        'message' => 'Voucher berhasil diterapkan!',
                        'discount_amount' => $discount_amount
            ]);
            exit();
}

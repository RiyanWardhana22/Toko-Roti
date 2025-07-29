<?php
require_once 'config.php';
require_once 'mailer.php';

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
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

$initial_status = (stripos($payment_method, 'Bayar di Toko') !== false) ? 'Diproses' : 'Menunggu Pembayaran';

mysqli_begin_transaction($conn);

try {
            $stmt1 = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, status, shipping_address, shipping_method, payment_method, shipping_cost, voucher_code, discount_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt1, "idsssssdd", $user_id, $total_amount, $initial_status, $shipping_address, $shipping_method, $payment_method, $shipping_cost, $voucher_code, $discount_amount);
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

            if ($initial_status == 'Diproses') {
                        $user_res = mysqli_query($conn, "SELECT name, email FROM users WHERE id = $user_id");
                        $customer_data = mysqli_fetch_assoc($user_res);
                        $settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
                        $website_name = mysqli_fetch_assoc($settings_res)['setting_value'] ?? 'Toko Anda';

                        $order_details_html = '<table class="order-details-table"><thead><tr><th>Nama Item</th><th>Jumlah</th><th>Harga</th></tr></thead><tbody>';
                        foreach ($_SESSION['cart'] as $item) {
                                    $order_details_html .= '<tr><td>' . htmlspecialchars($item['name']) . '</td><td>' . $item['quantity'] . 'x</td><td>Rp ' . number_format($item['price'] * $item['quantity'], 0, ',', '.') . '</td></tr>';
                        }
                        if ($discount_amount > 0) {
                                    $order_details_html .= '<tr><td colspan="2">Diskon </td><td>- Rp ' . number_format($discount_amount, 0, ',', '.') . '</td></tr>';
                        }
                        $order_details_html .= '<tr><td colspan="2">Ongkos Kirim</td><td>Rp ' . number_format($shipping_cost, 0, ',', '.') . '</td></tr>';
                        $order_details_html .= '</tbody></table>';

                        $template_path = realpath(__DIR__ . '/../templates/email/ereceipt_template.html');
                        if (file_exists($template_path)) {
                                    $email_body = file_get_contents($template_path);
                                    $placeholders = [
                                                '{{customer_name}}' => $customer_data['name'],
                                                '{{order_id}}' => $order_id,
                                                '{{order_date}}' => date('d F Y, H:i'),
                                                '{{total_amount}}' => number_format($total_amount, 0, ',', '.'),
                                                '{{website_name}}' => $website_name,
                                                '{{payment_method}}' => htmlspecialchars($payment_method),
                                                '{{shipping_method}}' => htmlspecialchars($shipping_method),
                                                '{{order_details_table}}' => $order_details_html,
                                    ];
                                    $email_body = str_replace(array_keys($placeholders), array_values($placeholders), $email_body);

                                    $subject = "Pesanan Anda #" . $order_id . " sedang diproses";
                                    send_email($customer_data['email'], $customer_data['name'], $subject, $email_body);
                        }
            }

            unset($_SESSION['cart']);
            unset($_SESSION['voucher']);

            header('Location: ' . BASE_URL . 'order_success?order_id=' . $order_id);
            exit();
} catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            header('Location: ' . BASE_URL . 'checkout?error=order_failed');
            exit();
}

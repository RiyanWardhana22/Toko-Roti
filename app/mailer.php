<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Fungsi untuk mengirim email.
 *
 * @param string $to Email tujuan
 * @param string $recipient_name Nama penerima
 * @param string $subject Judul email
 * @param string $body Isi email (bisa dalam format HTML)
 * @return bool True jika berhasil, string error jika gagal.
 */
function send_email($to, $recipient_name, $subject, $body)
{
            $mail = new PHPMailer(true);
            try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'silmarils2025@gmail.com';
                        $mail->Password   = 'qaal otje dear fskb';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->setFrom('silmarils2025@gmail.com', 'Silmarils Cookies Dessert');
                        $mail->addAddress($to, $recipient_name);

                        // KONTEN EMAIL
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = $body;
                        $mail->AltBody = strip_tags($body);

                        // Kirim email
                        $mail->send();
                        return true;
            } catch (Exception $e) {
                        return "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
            }
}

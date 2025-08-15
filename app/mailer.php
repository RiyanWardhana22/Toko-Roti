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
                        // PENGATURAN SERVER (SMTP - Simple Mail Transfer Protocol)
                        // ----------------------------------------------------
                        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktifkan ini untuk melihat proses debug detail
                        $mail->isSMTP();                                      // Menggunakan SMTP
                        $mail->Host       = 'smtp.gmail.com';                 // Set server SMTP ke server Gmail
                        $mail->SMTPAuth   = true;                             // Aktifkan otentikasi SMTP

                        // GANTI DENGAN KREDENSIAL GMAIL ANDA
                        $mail->Username   = 'silmarils2025@gmail.com';           // Alamat email Gmail Anda
                        $mail->Password   = 'qaal otje dear fskb'; // Gunakan App Password 16 digit yang sudah Anda buat

                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // Aktifkan enkripsi TLS
                        $mail->Port       = 587;                              // Port TCP untuk koneksi (587 untuk TLS, 465 untuk SSL)

                        // PENERIMA (RECIPIENTS)
                        // ----------------------------------------------------
                        // Set alamat "Dari" (From)
                        $mail->setFrom('silmarils2025@gmail.com', 'Silmarils Cookies Dessert'); // Ganti 'Toko Roti Anda' dengan nama toko Anda
                        // Tambahkan alamat "Ke" (To)
                        $mail->addAddress($to, $recipient_name);              // Email dan nama penerima

                        // KONTEN EMAIL
                        // ----------------------------------------------------
                        $mail->isHTML(true);                                  // Set format email ke HTML
                        $mail->Subject = $subject;
                        $mail->Body    = $body;
                        $mail->AltBody = strip_tags($body); // Versi teks biasa untuk email client non-HTML

                        // Kirim email
                        $mail->send();
                        return true;
            } catch (Exception $e) {
                        return "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
            }
}

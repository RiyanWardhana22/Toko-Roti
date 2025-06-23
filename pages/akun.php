<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}
?>
<h2>Selamat Datang, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
<p>Ini adalah halaman akun Anda. Di sini Anda nanti bisa melihat riwayat pesanan dan mengubah profil.</p>
<a href="<?= BASE_URL ?>app/logout.php" class="btn btn-danger">Logout</a>
<?php
require_once 'app/config.php';

$request_uri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url_parts = explode('/', $request_uri);
$page = $url_parts[0];

$allowed_pages = [
  'home',
  'produk',
  'cart',
  'checkout',
  'login',
  'register',
  'akun'
];

$page_file = 'pages/' . $page . '.php';

include 'pages/parts/header.php';

if (in_array($page, $allowed_pages) && file_exists($page_file)) {
  include $page_file;
} else {
  echo '<div class="container text-center py-5">
            <h1>404 - Halaman Tidak Ditemukan</h1>
            <p>Maaf, halaman yang Anda cari tidak ada.</p>
            <a href="' . BASE_URL . '" class="btn btn-primary">Kembali ke Beranda</a>
          </div>';
}

include 'pages/parts/footer.php';

mysqli_close($conn);

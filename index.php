<?php
require_once 'app/config.php';

$request_uri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url_parts = explode('/', $request_uri);
$page = $url_parts[0];

$allowed_pages = [
  'home',
  'produk',
  'about',
  'cart',
  'checkout',
  'login',
  'register',
  'akun',
  'order_success',
  'admin'
];

if ($page == 'admin') {
  include 'pages/admin/index_admin.php';
} elseif (in_array($page, $allowed_pages) && file_exists('pages/' . $page . '.php')) {
  include 'pages/parts/header.php';
  include 'pages/' . $page . '.php';
  include 'pages/parts/footer.php';
} else {
  include 'pages/parts/header.php';
  echo '<div class="container text-center py-5"><h1>404 - Halaman Tidak Ditemukan</h1></div>';
  include 'pages/parts/footer.php';
}

mysqli_close($conn);

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
  'akun',
  'order_success',
  'admin',
  'about',
  'order_detail_customer',
  'payment_confirmation'
];

$standalone_pages = ['admin', 'order_detail_customer', 'payment_confirmation'];
if ($page == 'admin') {
  if (file_exists('pages/admin/index_admin.php')) {
    include 'pages/admin/index_admin.php';
  } else {
    include 'pages/parts/header.php';
    echo '<div class="container text-center py-5"><h1>404 - Admin Not Found</h1></div>';
    include 'pages/parts/footer.php';
  }
} elseif (in_array($page, $standalone_pages)) {
  if (file_exists('pages/' . $page . '.php')) {
    include 'pages/' . $page . '.php';
  } else {
    include 'pages/parts/header.php';
    echo '<div class="container text-center py-5"><h1>404 - Halaman Tidak Ditemukan</h1></div>';
    include 'pages/parts/footer.php';
  }
} elseif (in_array($page, $allowed_pages) && file_exists('pages/' . $page . '.php')) {
  include 'pages/parts/header.php';
  include 'pages/' . $page . '.php';
  include 'pages/parts/footer.php';
} else {
  include 'pages/parts/header.php';
  echo '<div class="container text-center py-5"><h1>404 - Halaman Tidak Ditemukan</h1></div>';
  include 'pages/parts/footer.php';
}

if (isset($conn)) {
  mysqli_close($conn);
}

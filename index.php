<?php
// Start session
session_start();

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Define base path
define('BASE_PATH', __DIR__);

// Load configuration
require_once __DIR__ . '/config/database.php';

// Load functions
require_once __DIR__ . '/includes/functions.php';

// Determine requested page
$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';
$allowed_pages = [
            'home',
            'products',
            'product-detail',
            'cart',
            'checkout',
            'account',
            'login',
            'register',
            'about',
            'contact',
            'search'
];

// Validate page request
if (!in_array($page, $allowed_pages)) {
            $page = 'home';
}

// Handle admin pages separately
if (strpos($page, 'admin/') === 0 && isAdmin()) {
            require_once __DIR__ . '/admin.php';
            exit;
}

// Load header
require_once __DIR__ . '/includes/header.php';

// Load the requested page
$page_file = __DIR__ . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
            // Check authentication for protected pages
            $protected_pages = ['account', 'checkout', 'cart'];
            if (in_array($page, $protected_pages) && !isLoggedIn()) {
                        redirect('/pages/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            }

            require_once $page_file;
} else {
            // Page not found - load 404
            require_once __DIR__ . '/pages/404.php';
}

// Load footer
require_once __DIR__ . '/includes/footer.php';

// Close any open database connections
if (function_exists('close_all_db_connections')) {
            close_all_db_connections();
}

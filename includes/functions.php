<?php
require_once __DIR__ . '/../config/database.php';

// Fungsi untuk mendapatkan produk
function getProducts($limit = null, $category = null, $featured = false, $offset = null)
{
            $conn = connectDB();
            $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";

            if ($category) {
                        $sql .= " AND p.category_id = $category";
            }

            if ($featured) {
                        $sql .= " AND p.is_featured = 1";
            }

            $sql .= " ORDER BY p.created_at DESC";

            if ($limit) {
                        $sql .= " LIMIT $limit";
                        if ($offset !== null) {
                                    $sql .= " OFFSET $offset";
                        }
            }

            $result = $conn->query($sql);
            $products = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $products[] = $row;
                        }
            }

            $conn->close();
            return $products;
}

// Fungsi untuk mendapatkan kategori
function getCategories()
{
            $conn = connectDB();
            $sql = "SELECT * FROM categories ORDER BY name";
            $result = $conn->query($sql);
            $categories = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $categories[] = $row;
                        }
            }

            $conn->close();
            return $categories;
}

// Fungsi untuk mendapatkan produk berdasarkan ID
function getProductById($id)
{
            $conn = connectDB();
            $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = $id";
            $result = $conn->query($sql);
            $product = null;

            if ($result->num_rows > 0) {
                        $product = $result->fetch_assoc();

                        // Ambil gambar tambahan
                        $images_sql = "SELECT * FROM product_images WHERE product_id = $id";
                        $images_result = $conn->query($images_sql);
                        $product['images'] = [];

                        if ($images_result->num_rows > 0) {
                                    while ($row = $images_result->fetch_assoc()) {
                                                $product['images'][] = $row;
                                    }
                        }

                        // Ambil ulasan produk
                        $reviews_sql = "SELECT r.*, u.name as user_name FROM reviews r
                        JOIN users u ON r.user_id = u.id
                        WHERE r.product_id = $id
                        ORDER BY r.created_at DESC";
                        $reviews_result = $conn->query($reviews_sql);
                        $product['reviews'] = [];
                        $product['avg_rating'] = 0;
                        $product['review_count'] = 0;

                        if ($reviews_result->num_rows > 0) {
                                    $total_rating = 0;
                                    while ($row = $reviews_result->fetch_assoc()) {
                                                $product['reviews'][] = $row;
                                                $total_rating += $row['rating'];
                                    }
                                    $product['review_count'] = count($product['reviews']);
                                    $product['avg_rating'] = $total_rating / $product['review_count'];
                        }
            }

            $conn->close();
            return $product;
}

// Fungsi untuk memeriksa login
function isLoggedIn()
{
            return isset($_SESSION['user_id']);
}

// Fungsi untuk memeriksa role admin
function isAdmin()
{
            return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fungsi untuk redirect
function redirect($url)
{
            header("Location: $url");
            exit();
}

// Fungsi untuk mendapatkan item keranjang
function getCartItems($user_id)
{
            $conn = connectDB();
            $sql = "SELECT c.id as cart_id, p.id, p.name, p.image, p.price, p.discount_price, p.stock, 
                   c.quantity, v.name as variant_name
            FROM cart c
            JOIN products p ON c.product_id = p.id
            LEFT JOIN product_variants v ON c.variant_id = v.id
            WHERE c.user_id = $user_id";
            $result = $conn->query($sql);
            $items = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $items[] = $row;
                        }
            }

            $conn->close();
            return $items;
}

// Fungsi untuk menghitung subtotal keranjang
function calculateCartSubtotal($cart_items)
{
            $subtotal = 0;

            foreach ($cart_items as $item) {
                        $price = $item['discount_price'] ? $item['discount_price'] : $item['price'];
                        $subtotal += $price * $item['quantity'];
            }

            return $subtotal;
}

// Fungsi untuk memproses checkout
function processCheckout($user_id, $shipping_address, $shipping_method, $payment_method, $subtotal, $notes = '')
{
            $conn = connectDB();

            // Generate order number
            $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Start transaction
            $conn->begin_transaction();

            try {
                        // Insert order
                        $sql = "INSERT INTO orders (user_id, order_number, status, total_amount, 
                                    shipping_address, shipping_method, payment_method, notes)
                VALUES (?, ?, 'pending', ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param(
                                    "issdsss",
                                    $user_id,
                                    $order_number,
                                    $subtotal,
                                    $shipping_address,
                                    $shipping_method,
                                    $payment_method,
                                    $notes
                        );
                        $stmt->execute();
                        $order_id = $stmt->insert_id;

                        // Get cart items
                        $cart_items = getCartItems($user_id);

                        // Insert order items and update product stock
                        foreach ($cart_items as $item) {
                                    $price = $item['discount_price'] ? $item['discount_price'] : $item['price'];

                                    // Insert order item
                                    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $price);
                                    $stmt->execute();

                                    // Update product stock
                                    $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("ii", $item['quantity'], $item['id']);
                                    $stmt->execute();
                        }

                        // Clear cart
                        $sql = "DELETE FROM cart WHERE user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();

                        // Commit transaction
                        $conn->commit();

                        return $order_id;
            } catch (Exception $e) {
                        // Rollback transaction on error
                        $conn->rollback();
                        return false;
            } finally {
                        $conn->close();
            }
}

// Fungsi untuk mendapatkan statistik dashboard
function getDashboardStatistics()
{
            $conn = connectDB();
            $stats = [];

            // Today's income
            $sql = "SELECT SUM(total_amount) as today_income FROM orders 
             WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'";
            $result = $conn->query($sql);
            $stats['today_income'] = $result->fetch_assoc()['today_income'] ?? 0;

            // Today's orders
            $sql = "SELECT COUNT(*) as today_orders FROM orders 
             WHERE DATE(created_at) = CURDATE()";
            $result = $conn->query($sql);
            $stats['today_orders'] = $result->fetch_assoc()['today_orders'] ?? 0;

            // Total customers
            $sql = "SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'";
            $result = $conn->query($sql);
            $stats['total_customers'] = $result->fetch_assoc()['total_customers'] ?? 0;

            // Total products
            $sql = "SELECT COUNT(*) as total_products FROM products";
            $result = $conn->query($sql);
            $stats['total_products'] = $result->fetch_assoc()['total_products'] ?? 0;

            // Last 7 days sales
            $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as daily_sales 
             FROM orders 
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status != 'cancelled'
             GROUP BY DATE(created_at) 
             ORDER BY DATE(created_at)";
            $result = $conn->query($sql);
            $last_7_days = [];

            // Initialize last 7 days with 0 values
            for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $last_7_days[$date] = 0;
            }

            // Fill with actual data
            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $last_7_days[$row['date']] = (float)$row['daily_sales'];
                        }
            }

            $stats['last_7_days_sales'] = $last_7_days;

            // Order status counts
            $sql = "SELECT 
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
             FROM orders";
            $result = $conn->query($sql);
            $status_counts = $result->fetch_assoc();
            $stats['order_status_counts'] = [
                        'pending' => $status_counts['pending'] ?? 0,
                        'processing' => $status_counts['processing'] ?? 0,
                        'shipped' => $status_counts['shipped'] ?? 0,
                        'completed' => $status_counts['completed'] ?? 0,
                        'cancelled' => $status_counts['cancelled'] ?? 0
            ];

            $conn->close();
            return $stats;
}

// Fungsi untuk mendapatkan pesanan terbaru
function getRecentOrders($limit = 5)
{
            $conn = connectDB();
            $sql = "SELECT o.id, o.order_number, o.created_at, o.total_amount, o.status, 
                   u.name as customer_name
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT $limit";
            $result = $conn->query($sql);
            $orders = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $orders[] = $row;
                        }
            }

            $conn->close();
            return $orders;
}

// Fungsi untuk mendapatkan produk dengan stok menipis
function getLowStockProducts($limit = 5, $threshold = 10)
{
            $conn = connectDB();
            $sql = "SELECT p.id, p.name, p.stock, p.price, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock <= $threshold
            ORDER BY p.stock ASC
            LIMIT $limit";
            $result = $conn->query($sql);
            $products = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $products[] = $row;
                        }
            }

            $conn->close();
            return $products;
}

// Fungsi untuk mendapatkan class badge berdasarkan status
function getStatusBadgeClass($status)
{
            switch ($status) {
                        case 'pending':
                                    return 'warning';
                        case 'processing':
                                    return 'info';
                        case 'shipped':
                                    return 'primary';
                        case 'delivered':
                                    return 'success';
                        case 'cancelled':
                                    return 'danger';
                        default:
                                    return 'secondary';
            }
}

// Fungsi untuk mendapatkan teks status
function getStatusText($status)
{
            switch ($status) {
                        case 'pending':
                                    return 'Menunggu Pembayaran';
                        case 'processing':
                                    return 'Diproses';
                        case 'shipped':
                                    return 'Dikirim';
                        case 'delivered':
                                    return 'Selesai';
                        case 'cancelled':
                                    return 'Dibatalkan';
                        default:
                                    return $status;
            }
}

// Fungsi untuk memeriksa apakah produk sudah dibeli oleh user
function hasPurchasedProduct($user_id, $product_id)
{
            $conn = connectDB();
            $sql = "SELECT COUNT(*) as count FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];

            $conn->close();
            return $count > 0;
}

// Fungsi untuk mendapatkan produk terkait
function getRelatedProducts($product_id, $category_id, $limit = 4)
{
            $conn = connectDB();
            $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? AND p.id != ?
            ORDER BY RAND()
            LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $category_id, $product_id, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = [];

            if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                    $products[] = $row;
                        }
            }

            $conn->close();
            return $products;
}

// Fungsi untuk memendekkan deskripsi
function shortenDescription($text, $length = 100)
{
            if (strlen($text) <= $length) {
                        return $text;
            }
            return substr($text, 0, $length) . '...';
}

// Fungsi untuk menghitung persentase diskon
function calculateDiscountPercentage($original_price, $discount_price)
{
            if ($original_price <= 0 || $discount_price >= $original_price) {
                        return 0;
            }
            return round((($original_price - $discount_price) / $original_price) * 100);
}

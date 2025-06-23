<?php
require_once 'config.php';
function handle_image_upload($file_input_name)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = '../assets/images/';
                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                        $file_name = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
                        $target_file = $upload_dir . $file_name;

                        if (!in_array($_FILES[$file_input_name]['type'], $allowed_types)) {
                                    die("Error: Tipe file tidak diizinkan.");
                        }

                        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_file)) {
                                    return $file_name;
                        }
            }
            return null;
}

$action = $_POST['action'] ?? '';
if ($action == 'create') {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $desc = mysqli_real_escape_string($conn, $_POST['description']);
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock'];
            $cat_id = (int)$_POST['category_id'];

            $image_name = handle_image_upload('image');
            if (!$image_name) {
                        die("Error saat mengupload gambar.");
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, price, stock, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssdiis", $name, $desc, $price, $stock, $cat_id, $image_name);
            mysqli_stmt_execute($stmt);
} elseif ($action == 'update') {
            $product_id = (int)$_POST['product_id'];
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $desc = mysqli_real_escape_string($conn, $_POST['description']);
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock'];
            $cat_id = (int)$_POST['category_id'];

            $image_name = handle_image_upload('image');

            if ($image_name) {
                        $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, image_url=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssdiisi", $name, $desc, $price, $stock, $cat_id, $image_name, $product_id);
            } else {
                        $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssdiis", $name, $desc, $price, $stock, $cat_id, $product_id);
            }
            mysqli_stmt_execute($stmt);
}

header('Location: ' . BASE_URL . 'admin?page=products&status=success');
exit();

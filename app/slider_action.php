<?php
require_once 'config.php';

function handle_slider_image_upload($file_input_name)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = '../assets/images/sliders/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                        $file_name = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
                        $target_file = $upload_dir . $file_name;

                        if (in_array($_FILES[$file_input_name]['type'], $allowed_types) && move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_file)) {
                                    return $file_name;
                        }
            }
            return null;
}

if (isset($_POST['action']) && $_POST['action'] == 'create') {
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
            $button_text = mysqli_real_escape_string($conn, $_POST['button_text']);
            $button_link = mysqli_real_escape_string($conn, $_POST['button_link']);

            $image_name = handle_slider_image_upload('image');
            if (!$image_name) die("Error: Gagal mengupload gambar.");

            $stmt = mysqli_prepare($conn, "INSERT INTO sliders (title, subtitle, image_url, button_text, button_link) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $title, $subtitle, $image_name, $button_text, $button_link);
            mysqli_stmt_execute($stmt);

            header('Location: ' . BASE_URL . 'admin?page=settings_slider');
            exit();
}

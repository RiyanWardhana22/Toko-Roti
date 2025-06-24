<?php
require_once 'config.php';

function update_setting($key, $value)
{
            global $conn;
            $stmt = mysqli_prepare($conn, "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            mysqli_stmt_bind_param($stmt, "sss", $key, $value, $value);
            mysqli_stmt_execute($stmt);
}

function handle_settings_image_upload($file_input_name)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = '../assets/images/';
                        $file_name = 'site-' . $file_input_name . '-' . time() . '.' . pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION);
                        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $upload_dir . $file_name)) {
                                    return $file_name;
                        }
            }
            return null;
}

if (isset($_POST['update_website_settings'])) {
            update_setting('website_title', $_POST['website_title']);
            update_setting('navbar_brand_type', $_POST['navbar_brand_type']);
            update_setting('navbar_brand_text', $_POST['navbar_brand_text']);
            if ($favicon = handle_settings_image_upload('website_favicon')) {
                        update_setting('website_favicon', $favicon);
            }
            if ($logo = handle_settings_image_upload('navbar_brand_logo')) {
                        update_setting('navbar_brand_logo', $logo);
            }
}

header('Location: ' . BASE_URL . 'admin?page=settings_website&status=success');
exit();

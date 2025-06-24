<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) exit();
$admin_check = mysqli_query($conn, "SELECT role FROM users WHERE id = " . $_SESSION['user_id']);
$admin_role = mysqli_fetch_assoc($admin_check)['role'];
if ($admin_role !== 'admin') exit();


if (isset($_POST['action']) && $_POST['action'] == 'update_role') {
            $user_id_to_update = (int)$_POST['user_id'];
            $new_role = mysqli_real_escape_string($conn, $_POST['role']);

            if ($user_id_to_update == $_SESSION['user_id']) {
                        header('Location: ' . BASE_URL . 'admin?page=settings_users&error=self_edit');
                        exit();
            }

            if ($new_role == 'admin' || $new_role == 'customer') {
                        $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "si", $new_role, $user_id_to_update);
                        mysqli_stmt_execute($stmt);
            }
}

header('Location: ' . BASE_URL . 'admin?page=settings_users&status=success');
exit();

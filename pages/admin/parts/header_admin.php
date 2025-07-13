<?php
$user_id_admin = $_SESSION['user_id'];
$user_res_admin = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id_admin");
$user_admin = mysqli_fetch_assoc($user_res_admin);
?>
<header class="admin-header">
            <div>
                        <button class="btn d-md-none mobile-toggler"><i class="fas fa-bars"></i></button>
                        <h1 class="page-title d-none d-md-block">
                        </h1>
            </div>
            <div class="user-profile dropdown">
                        <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                    <span class="me-2 d-none d-sm-block"><?= htmlspecialchars($user_admin['name']) ?></span>
                                    <i class="fas fa-user-circle fa-2x"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>" target="_blank"><i class="fas fa-globe me-2"></i>Lihat Website</a></li>
                                    <li>
                                                <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>app/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
            </div>
</header>
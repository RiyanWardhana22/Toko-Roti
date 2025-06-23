<?php
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-2 text-muted text-uppercase">
                                    <span>Toko Roti Admin</span>
                        </h6>

                        <ul class="nav flex-column">
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=dashboard">
                                                            Dashboard
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'orders') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=orders">
                                                            Manajemen Pesanan
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'products') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=products">
                                                            Manajemen Produk
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'customers') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=customers">
                                                            Manajemen Pelanggan
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'categories') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=categories">
                                                            Manajemen Kategori
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=reports">
                                                            Laporan
                                                </a>
                                    </li>
                        </ul>
            </div>
</nav>
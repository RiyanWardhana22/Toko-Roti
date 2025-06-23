<?php
$result_new = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 4");
$result_featured = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.price DESC LIMIT 4");
$result_categories = mysqli_query($conn, "SELECT * FROM categories LIMIT 3");
?>

<div class="p-5 mb-4 bg-light rounded-3 text-center" style="background-image: url('<?= BASE_URL ?>assets/images/hero-bg.jpg'); background-size: cover; background-position: center; color: white; text-shadow: 2px 2px 4px #000000;">
            <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Roti & Kue Segar Setiap Hari</h1>
                        <p class="fs-4">Dibuat dengan bahan-bahan terbaik dan resep warisan keluarga.</p>
                        <a href="<?= BASE_URL ?>produk" class="btn btn-primary btn-lg">Belanja Sekarang</a>
            </div>
</div>

<div class="container my-5">
            <h2 class="text-center mb-4">Produk Terbaru</h2>
            <div class="row">
                        <?php while ($product = mysqli_fetch_assoc($result_new)) : ?>
                                    <?php include 'parts/product_card.php'; ?>
                        <?php endwhile; ?>
            </div>
</div>

<div class="container my-5">
            <h2 class="text-center mb-4">Kategori Populer</h2>
            <div class="row text-center">
                        <?php while ($category = mysqli_fetch_assoc($result_categories)): ?>
                                    <div class="col-md-4">
                                                <a href="<?= BASE_URL ?>produk?kategori=<?= $category['slug'] ?>" class="text-decoration-none text-dark">
                                                            <img src="<?= BASE_URL ?>assets/images/kategori-<?= $category['slug'] ?>.jpg" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                                                            <h4><?= htmlspecialchars($category['name']) ?></h4>
                                                </a>
                                    </div>
                        <?php endwhile; ?>
            </div>
</div>

<div class="container my-5">
            <h2 class="text-center mb-4">Produk Unggulan</h2>
            <div class="row">
                        <?php while ($product = mysqli_fetch_assoc($result_featured)) : ?>
                                    <?php include 'parts/product_card.php'; ?>
                        <?php endwhile; ?>
            </div>
</div>

<div class="container my-5 bg-light p-5 rounded">
            <h2 class="text-center mb-4">Apa Kata Mereka?</h2>
            <div class="row">
                        <div class="col-md-4 text-center">
                                    <p class="fst-italic">"Rotinya lembut banget, anak-anak suka. Pasti pesan lagi!"</p>
                                    <strong>- Ibu Siti -</strong>
                        </div>
                        <div class="col-md-4 text-center">
                                    <p class="fst-italic">"Kue ulang tahunnya juara! Desainnya cantik, rasanya enak."</p>
                                    <strong>- Bapak Budi -</strong>
                        </div>
                        <div class="col-md-4 text-center">
                                    <p class="fst-italic">"Nastar di sini paling the best, kejunya berasa banget."</p>
                                    <strong>- Kak Rina -</strong>
                        </div>
            </div>
</div>
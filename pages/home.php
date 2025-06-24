<?php
$result_sliders = mysqli_query($conn, "SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC");

$result_new = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 4");
$result_featured = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.price DESC LIMIT 4");
$result_categories = mysqli_query($conn, "SELECT * FROM categories LIMIT 3");
?>

<?php if (mysqli_num_rows($result_sliders) > 0): ?>
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                        <div class="carousel-inner">
                                    <?php $first = true;
                                    while ($slide = mysqli_fetch_assoc($result_sliders)): ?>
                                                <div class="carousel-item <?= $first ? 'active' : '' ?>">
                                                            <div class="p-5 mb-4 rounded-3 text-center" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?= BASE_URL ?>assets/images/sliders/<?= $slide['image_url'] ?>'); background-size: cover; background-position: center; color: white; text-shadow: 2px 2px 4px #000000;">
                                                                        <div class="container-fluid py-5">
                                                                                    <h1 class="display-5 fw-bold"><?= htmlspecialchars($slide['title']) ?></h1>
                                                                                    <p class="fs-4"><?= htmlspecialchars($slide['subtitle']) ?></p>
                                                                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                                                                                <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="btn btn-primary btn-lg"><?= htmlspecialchars($slide['button_text']) ?></a>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                            </div>
                                                </div>
                                    <?php $first = false;
                                    endwhile; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                        </button>
            </div>
<?php endif; ?>

<div class="container my-5">
            <h2 class="text-center mb-4">Produk Terbaru</h2>
            <div class="row">
                        <?php mysqli_data_seek($result_new, 0);
                        while ($product = mysqli_fetch_assoc($result_new)) : ?>
                                    <?php include 'parts/product_card.php'; ?>
                        <?php endwhile; ?>
            </div>
</div>
<div class="container my-5">
            <h2 class="text-center mb-4">Kategori Populer</h2>
            <div class="row text-center">
                        <?php mysqli_data_seek($result_categories, 0);
                        while ($category = mysqli_fetch_assoc($result_categories)): ?>
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
                        <?php mysqli_data_seek($result_featured, 0);
                        while ($product = mysqli_fetch_assoc($result_featured)) : ?>
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
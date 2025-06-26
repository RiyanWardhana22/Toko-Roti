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
                                                <div class="carousel-item <?= $first ? 'active' : '' ?>" style="background-image: url('<?= BASE_URL ?>assets/images/sliders/<?= $slide['image_url'] ?>'); background-size: cover; background-position: center;">
                                                            <div class="container-fluid">
                                                                        <div class="carousel-caption-custom text-center">
                                                                                    <h1 class="display-4 fw-bold"><?= htmlspecialchars($slide['title']) ?></h1>
                                                                                    <p class="fs-5 lead"><?= htmlspecialchars($slide['subtitle']) ?></p>
                                                                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                                                                                <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="btn btn-primary btn-lg mt-3"><?= htmlspecialchars($slide['button_text']) ?></a>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                            </div>
                                                </div>
                                    <?php $first = false;
                                    endwhile; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
            </div>
<?php endif; ?>

<main>
            <div class="container my-5 py-5">
                        <div class="section-title text-center">
                                    <h2>Kategori Pilihan</h2>
                                    <p>Temukan kelezatan dalam setiap kategori yang kami tawarkan.</p>
                        </div>
                        <div class="row text-center g-4">
                                    <?php mysqli_data_seek($result_categories, 0);
                                    while ($category = mysqli_fetch_assoc($result_categories)): ?>
                                                <div class="col-md-4">
                                                            <a href="<?= BASE_URL ?>produk?kategori=<?= $category['slug'] ?>" class="text-decoration-none text-dark">
                                                                        <img src="<?= BASE_URL ?>assets/images/kategori-<?= $category['slug'] ?>.jpg" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                                                                        <h4 class="mt-3"><?= htmlspecialchars($category['name']) ?></h4>
                                                            </a>
                                                </div>
                                    <?php endwhile; ?>
                        </div>
            </div>

            <div class="py-5" style="background-color: var(--secondary-color);">
                        <div class="container">
                                    <div class="section-title text-center">
                                                <h2>Produk Unggulan Kami</h2>
                                                <p>Roti dan kue terbaik yang menjadi favorit pelanggan setia kami.</p>
                                    </div>
                                    <div class="row g-4">
                                                <?php mysqli_data_seek($result_featured, 0);
                                                while ($product = mysqli_fetch_assoc($result_featured)) : ?>
                                                            <?php include 'parts/product_card.php'; ?>
                                                <?php endwhile; ?>
                                    </div>
                        </div>
            </div>

            <div class="container my-5 py-5">
                        <div class="section-title text-center">
                                    <h2>Apa Kata Mereka?</h2>
                                    <p>Kepuasan Anda adalah prioritas utama kami.</p>
                        </div>
                        <div class="row g-4">
                                    <div class="col-md-4 text-center">
                                                <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                                                <p class="fst-italic">"Rotinya lembut banget, anak-anak suka. Pasti pesan lagi!"</p>
                                                <strong>- Ibu Siti -</strong>
                                    </div>
                        </div>
            </div>

</main>
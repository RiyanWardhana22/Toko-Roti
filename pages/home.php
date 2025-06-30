<?php
$result_sliders = mysqli_query($conn, "SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC");

$result_new = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 4");
$result_featured = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.price DESC LIMIT 4");
$result_categories = mysqli_query($conn, "SELECT * FROM categories LIMIT 3");

$reviews_res = mysqli_query($conn, "SELECT r.rating, r.comment, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.is_approved = 1 AND r.comment != '' AND r.rating >= 4 ORDER BY r.created_at DESC LIMIT 3");
$reviews = mysqli_fetch_all($reviews_res, MYSQLI_ASSOC);
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

<div class="container my-5">
            <div class="section-title text-center mb-5">
                        <h2 class="display-5 fw-bold text-gradient">Produk Terbaru</h2>
                        <p class="lead text-muted">Temukan kelezatan tak terbatas dalam setiap petualangan kuliner</p>
            </div>
            <div class="row">
                        <?php mysqli_data_seek($result_new, 0);
                        while ($product = mysqli_fetch_assoc($result_new)) : ?>
                                    <?php include 'parts/product_card.php'; ?>
                        <?php endwhile; ?>
            </div>
</div>

<main>
            <div class="container my-5 py-5">
                        <div class="section-title text-center mb-5">
                                    <h2 class="display-5 fw-bold text-gradient">Jelajahi Kategori Kami</h2>
                                    <p class="lead text-muted">Temukan kelezatan tak terbatas dalam setiap petualangan kuliner</p>
                        </div>
                        <div class="row g-4 d-flex justify-content-center">
                                    <?php mysqli_data_seek($result_categories, 0);
                                    while ($category = mysqli_fetch_assoc($result_categories)): ?>
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <a href="<?= BASE_URL ?>produk?kategori=<?= $category['slug'] ?>" class="text-decoration-none">
                                                                        <div class="category-card" data-category="<?= htmlspecialchars($category['name']) ?>">
                                                                                    <div class="category-bg"></div>
                                                                                    <div class="category-content">
                                                                                                <div class="category-icon">
                                                                                                            <i class="fa-solid fa-cake-candles"></i>
                                                                                                </div>
                                                                                                <h4><?= htmlspecialchars($category['name']) ?></h4>
                                                                                                <span class="explore-btn">Jelajahi <i class="fas fa-arrow-right"></i></span>
                                                                                    </div>
                                                                        </div>
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
                                    <?php if (!empty($reviews)): ?>
                                                <?php foreach ($reviews as $review): ?>
                                                            <div class="col-md-4 text-center">
                                                                        <div class="mb-2">
                                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-secondary' ?>" style="font-size: 0.9rem;"></i>
                                                                                    <?php endfor; ?>
                                                                        </div>
                                                                        <p class="fst-italic">"<?= htmlspecialchars($review['comment']) ?>"</p>
                                                                        <strong class="text-muted">- <?= htmlspecialchars($review['user_name']) ?> -</strong>
                                                            </div>
                                                <?php endforeach; ?>
                                    <?php else: ?>
                                                <div class="col text-center">
                                                            <p>Belum ada ulasan untuk ditampilkan.</p>
                                                </div>
                                    <?php endif; ?>
                        </div>
            </div>

</main>
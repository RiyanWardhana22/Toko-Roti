<?php
$result_sliders = mysqli_query($conn, "SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC");

$result_new = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 4");
$result_featured = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.price DESC LIMIT 4");
$result_categories = mysqli_query($conn, "SELECT * FROM categories LIMIT 3");

$reviews_res = mysqli_query($conn, "SELECT r.rating, r.comment, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.is_approved = 1 AND r.comment != '' AND r.rating >= 4 ORDER BY r.created_at DESC LIMIT 3");
$reviews = mysqli_fetch_all($reviews_res, MYSQLI_ASSOC);
?>

<style>
            .primary-button {
                        font-family: 'Ropa Sans', sans-serif;
                        color: white;
                        text-transform: uppercase;
                        text-decoration: none;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: bold;
                        letter-spacing: 0.05rem;
                        border: 1px solid #0E1822;
                        padding: 0.8rem 2.1rem;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 531.28 200'%3E%3Cdefs%3E%3Cstyle%3E .shape %7B fill: %23a1623b fill: %230E1822; %7D %3C/style%3E%3C/defs%3E%3Cg id='Layer_2' data-name='Layer 2'%3E%3Cg id='Layer_1-2' data-name='Layer 1'%3E%3Cpolygon class='shape' points='415.81 200 0 200 115.47 0 531.28 0 415.81 200' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E%0A");
                        background-color: #0E1822;
                        background-size: 200%;
                        background-position: 200%;
                        background-repeat: no-repeat;
                        transition: 0.3s ease-in-out;
                        transition-property: background-position, border, color;
                        position: relative;
                        z-index: 1;
            }

            .primary-button:hover {
                        border: 1px solid #a1623b;
                        color: white;
                        background-position: 40%;
            }

            .primary-button:before {
                        content: "";
                        position: absolute;
                        background-color: #0E1822;
                        width: 0.2rem;
                        height: 0.2rem;
                        top: -1px;
                        left: -1px;
                        transition: background-color 0.15s ease-in-out;
            }

            .primary-button:hover:before {
                        background-color: white;
            }

            .primary-button:hover:after {
                        background-color: white;
            }

            .primary-button:after {
                        content: "";
                        position: absolute;
                        background-color: #a1623b;
                        width: 0.3rem;
                        height: 0.3rem;
                        bottom: -1px;
                        right: -1px;
                        transition: background-color 0.15s ease-in-out;
            }

            .button-borders {
                        position: relative;
                        width: fit-content;
                        height: fit-content;
            }

            .button-borders:before {
                        content: "";
                        position: absolute;
                        width: calc(100% + 0.5em);
                        height: 60%;
                        left: -0.3em;
                        top: -0.3em;
                        border: 1px solid #FFC300;
                        border-bottom: 0px;
                        /* opacity: 0.3; */
            }

            .button-borders:after {
                        content: "";
                        position: absolute;
                        width: calc(100% + 0.5em);
                        height: 60%;
                        left: -0.3em;
                        bottom: -0.3em;
                        border: 1px solid #FFC300;
                        border-top: 0px;
                        /* opacity: 0.3; */
                        z-index: 0;
            }

            .shape {
                        fill: #FFC300;
            }
</style>

<?php if (mysqli_num_rows($result_sliders) > 0): ?>
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="1000000">
                        <div class="carousel-inner">
                                    <?php $first = true;
                                    while ($slide = mysqli_fetch_assoc($result_sliders)): ?>
                                                <div class="carousel-item <?= $first ? 'active' : '' ?>" style="background-image: url('<?= BASE_URL ?>assets/images/sliders/<?= $slide['image_url'] ?>'); background-size: cover; background-position: center;">
                                                            <div class="container-fluid">
                                                                        <div class="carousel-caption-custom text-center bg-transparent d-flex flex-column justify-content-between" style="height: 100%;">
                                                                                    <div></div>
                                                                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                                                                                <div class="mb-5">
                                                                                                            <div class="container-fluid">
                                                                                                                        <div class="carousel-caption-custom text-center bg-transparent">
                                                                                                                                    <div class="button-borders">
                                                                                                                                                <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                                                                                                                                            <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="primary-button">
                                                                                                                                                                        <span class="btn-text"><?= htmlspecialchars($slide['button_text']) ?></span>
                                                                                                                                                            </a>
                                                                                                                                                <?php endif; ?>
                                                                                                                                    </div>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                </div>
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
                                                                                                            <i class='bx  bx-cake-slice'></i>
                                                                                                </div>
                                                                                                <p><?= htmlspecialchars($category['name']) ?></p>
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
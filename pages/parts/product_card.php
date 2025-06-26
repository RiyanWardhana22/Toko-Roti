<div class="col-lg-3 col-md-4 col-6 mb-4">
            <div class="card product-card h-100">
                        <a href="<?= BASE_URL ?>produk/detail/<?= $product['id'] ?>">
                                    <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body text-center d-flex flex-column">
                                    <h5 class="card-title h6"><a href="<?= BASE_URL ?>produk/detail/<?= $product['id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($product['name']) ?></a></h5>
                                    <p class="text-muted small mb-1"><?= htmlspecialchars($product['category_name']) ?></p>
                                    <h6 class="card-subtitle mt-2 mb-3 text-danger fw-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></h6>
                                    <div class="mt-auto">
                                                <a href="<?= BASE_URL ?>produk/detail/<?= $product['id'] ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
                                    </div>
                        </div>
            </div>
</div>
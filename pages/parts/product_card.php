<div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                        <a href="<?= BASE_URL ?>produk/detail/<?= $product['id'] ?>">
                                    <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-muted small"><?= htmlspecialchars($product['category_name']) ?></p>
                                    <h6 class="card-subtitle mb-2 text-danger">Rp <?= number_format($product['price'], 0, ',', '.') ?></h6>
                                    <div class="mt-auto">
                                                <a href="<?= BASE_URL ?>produk/detail/<?= $product['id'] ?>" class="btn btn-primary w-100">Lihat Detail</a>
                                    </div>
                        </div>
            </div>
</div>
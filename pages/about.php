<?php
$result = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'about_us_content'");
$about_content = 'Konten belum diatur.';

if ($row = mysqli_fetch_assoc($result)) {
            $about_content = nl2br($row['setting_value']);
}
?>

<style>
            :root {
                        --primary-color: #D4A76A;
                        --secondary-color: #F8F1E5;
                        --dark-color: #5C3A21;
                        --light-color: #FFFFFF;
            }

            body {
                        font-family: 'Poppins', sans-serif;
                        background-color: var(--secondary-color);
                        color: var(--dark-color);
            }

            .breadcrumb-bg {
                        background-image: url('https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
                        background-size: cover;
                        background-position: center;
                        height: 300px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
            }

            .breadcrumb-bg::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.4);
            }

            .breadcrumb-title {
                        position: relative;
                        color: white;
                        font-size: 3rem;
                        font-weight: 700;
                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            }

            .about-section {
                        background-color: var(--light-color);
                        border-radius: 10px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                        padding: 40px;
                        margin-top: -50px;
                        position: relative;
                        z-index: 2;
            }

            .section-title {
                        color: var(--primary-color);
                        font-weight: 700;
                        margin-bottom: 20px;
                        position: relative;
                        display: inline-block;
            }

            .section-title::after {
                        content: '';
                        position: absolute;
                        bottom: -10px;
                        left: 0;
                        width: 50px;
                        height: 3px;
                        background-color: var(--primary-color);
            }

            .about-content {
                        line-height: 1.8;
                        font-size: 1.1rem;
            }

            .feature-box {
                        text-align: center;
                        padding: 30px 20px;
                        border-radius: 10px;
                        background-color: var(--light-color);
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                        transition: transform 0.3s ease;
                        margin-bottom: 30px;
            }


            .feature-icon {
                        font-size: 2.5rem;
                        color: var(--primary-color);
                        margin-bottom: 20px;
            }

            .team-member {
                        text-align: center;
                        margin-bottom: 30px;
            }

            .team-img {
                        width: 150px;
                        height: 150px;
                        object-fit: cover;
                        border-radius: 50%;
                        border: 5px solid var(--primary-color);
                        margin-bottom: 15px;
            }

            @media (max-width: 768px) {
                        .breadcrumb-title {
                                    font-size: 2rem;
                        }

                        .about-section {
                                    padding: 25px;
                                    margin-top: -30px;
                        }
            }
</style>

<body>
            <div class="breadcrumb-bg">
                        <h1 class="breadcrumb-title">Tentang Kami</h1>
            </div>

            <div class="container mt-3 ">
                        <div class="row justify-content-center">
                                    <div class="col-lg-10">
                                                <div class="about-section">
                                                            <h2 class="section-title">Cerita Kami</h2>
                                                            <div class="about-content">
                                                                        <?php echo $about_content; ?>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <div class="row mt-5">
                                    <div class="col-12 text-center mb-5">
                                                <h2 class="section-title">Nilai Kami</h2>
                                    </div>

                                    <div class="col-md-4">
                                                <div class="feature-box">
                                                            <div class="feature-icon">
                                                                        <i class="fas fa-heart"></i>
                                                            </div>
                                                            <h3>Cinta pada Roti</h3>
                                                            <p>Setiap roti dibuat dengan cinta dan dedikasi untuk memberikan rasa terbaik.</p>
                                                </div>
                                    </div>

                                    <div class="col-md-4">
                                                <div class="feature-box">
                                                            <div class="feature-icon">
                                                                        <i class="fas fa-leaf"></i>
                                                            </div>
                                                            <h3>Bahan Alami</h3>
                                                            <p>Kami hanya menggunakan bahan-bahan alami berkualitas tinggi tanpa pengawet.</p>
                                                </div>
                                    </div>

                                    <div class="col-md-4">
                                                <div class="feature-box">
                                                            <div class="feature-icon">
                                                                        <i class="fas fa-award"></i>
                                                            </div>
                                                            <h3>Kualitas Terbaik</h3>
                                                            <p>Standar kualitas tinggi dalam setiap produk yang kami hasilkan.</p>
                                                </div>
                                    </div>
                        </div>

            </div>
</body>
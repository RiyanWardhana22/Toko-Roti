<?php
$result = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'about_us_content'");
$about_content = 'Konten belum diatur.';

if ($row = mysqli_fetch_assoc($result)) {
            $about_content = nl2br($row['setting_value']);
}
?>

<div class="container py-5">
            <div class="row">
                        <div class="col-lg-8 mx-auto">
                                    <h1 class="mb-4">Tentang Kami</h1>
                                    <hr class="mb-4">

                                    <div class="lead">
                                                <?php
                                                echo $about_content;
                                                ?>
                                    </div>
                        </div>
            </div>
</div>
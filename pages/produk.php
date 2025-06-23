<?php
global $url_parts;
if (isset($url_parts[1]) && $url_parts[1] == 'detail' && isset($url_parts[2])) {
            $product_id = (int)$url_parts[2];
            include 'product_detail.php';
} else {
            include 'catalog.php';
}

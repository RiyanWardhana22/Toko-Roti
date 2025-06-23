<?php
require_once 'config.php';
session_destroy();
// header('Location: ' . BASE_URL);
header('Location: index.php');
exit();

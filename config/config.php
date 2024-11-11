<?php
if (!defined('DB_HOST')) {
    define("DB_HOST", "localhost:3309");
}

if (!defined('DB_USER')) {
    define("DB_USER", "root");
}

if (!defined('DB_PASS')) {
    define("DB_PASS", "");
}

if (!defined('DB_NAME')) {
    define("DB_NAME", "armcuff");
}
try {
    // Tạo kết nối PDO với cơ sở dữ liệu
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Nếu kết nối thất bại, hiển thị thông báo lỗi
    die("Kết nối thất bại: " . $e->getMessage());
}
?>


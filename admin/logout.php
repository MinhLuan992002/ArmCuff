<?php
// Khởi tạo session
session_start();

// Hủy session
session_unset();
session_destroy();

// Điều hướng tới trang login (tải lại toàn bộ trang, không chứa sidebar)
header("Location: sign-in.php");
exit();
?>

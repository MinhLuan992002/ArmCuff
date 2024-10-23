<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Giả sử bạn có session người dùng đăng nhập
// $adminId = $_SESSION['adminId']; // hoặc lấy từ session

// Truy vấn để lấy thông tin của admin
$sql = "SELECT Code FROM users WHERE Code = 'Admin'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Lấy vai trò của admin
    $row = $result->fetch_assoc();
    $userType = $row['Code'];
} else {
    echo "Không tìm thấy vai trò cho admin này.";
}
// Đóng kết nối
$conn->close();
?>
<script src="fontawesome/fontawesome.js" crossorigin="anonymous"></script>
<link href="css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="css/style.css">

<style>
    
</style>
<div class="wrapper">

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="h-100">
            <div class="sidebar-logo">
                <a href="index_content.php">Đào tạo nhân viên</a>
            </div>
            <!-- Sidebar Navigation -->
            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Thông tin chi tiết
                </li>

                <?php if ($userType == 'Admin') { ?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#pages"
                            aria-expanded="false" aria-controls="pages">
                            <i class="fa-regular fa-file-lines pe-2"></i>
                            Trang
                        </a>
                        <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="commit_list.php" class="sidebar-link ajax-link">Cam kết</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="personal_info.php" class="sidebar-link">Đào tạo nội quy</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="interviewer_test.php" class="sidebar-link">Phỏng vấn</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="index.php" class="sidebar-link">Bài kiểm tra</a>
                            </li>
                        </ul>
                    </li>

                <?php } ?>
                <?php if ($userType == 'PCCC' || $userType == 'ISO') { ?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#dashboard"
                            aria-expanded="false" aria-controls="dashboard">
                            <i class="fa-solid fa-sliders pe-2"></i>
                            Bài Kiểm Tra
                        </a>
                        <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <?php if ($userType == 'ISO') { ?>
                                <li class="sidebar-item">
                                    <a href="iso_test.php" class="sidebar-link">ISO</a>
                                </li>
                            <?php } ?>
                            <?php if ($userType == 'PCCC') { ?>
                                <li class="sidebar-item">
                                    <a href="pccc_test.php" class="sidebar-link">PCCC</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($userType == 'Employee') { ?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#5STest"
                            aria-expanded="false" aria-controls="5STest">
                            <i class="fa-solid fa-sliders pe-2"></i>
                            Nhân sự & Tổng vụ
                        </a>
                        <ul id="5STest" class="collapse list-unstyled">
                            <li><a href="5S_test.php" class="sidebar-link">5S</a></li>
                            <li><a href="personal_info.php" class="sidebar-link">Đào tạo nội quy</a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($userType == 'HR') { ?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#hrTest"
                            aria-expanded="false" aria-controls="hrTest">
                            <i class="fa-solid fa-sliders pe-2"></i>
                            HR 
                        </a>
                        <ul id="hrTest" class="collapse list-unstyled">
                            <li><a href="interviewer_test.php" class="sidebar-link">Phỏng vấn</a></li>
                            <li><a href="hr_test.php" class="sidebar-link">Nhân viên mới</a></li>
                            <li><a href="commit_list.php" class="sidebar-link">Cam kết khi vào công ty</a></li>
                            <li><a href="commit_list_leave.php" class="sidebar-link">Cam kết khi thôi việc</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </aside>
    <nav class="border-bottom">
        <!-- Button for sidebar toggle -->
        <button class="btn" type="button" data-bs-theme="dark">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <!-- Main Component -->

    <script src="js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <script>
        const toggler = document.querySelector(".btn");
        toggler.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("collapsed");
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll(".ajax-link");

            links.forEach(link => {
                link.addEventListener("click", function(event) {
                    event.preventDefault();
                    const url = this.getAttribute("data-url");

                    // Gửi yêu cầu AJAX
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            // Cập nhật nội dung của phần chính
                            document.querySelector(".main").innerHTML = html;
                        })
                        .catch(error => console.error('Error loading content:', error));
                });
            });
        });
    </script>
</div>

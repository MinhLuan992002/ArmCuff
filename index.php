<?php include 'inc/header.php'; ?>
<?php include 'config/config.php'; ?>
<?php
// Kết nối cơ sở dữ liệu
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['manv']) || !isset($_SESSION['displayName'])) {
        header("Location: login.php");
        exit();
    }
    $employeeId = $_SESSION['manv'];
    $employeeName = $_SESSION['displayName'];

    // Lấy danh sách bài kiểm tra từ cơ sở dữ liệu
    $sqlManageTests = "
    SELECT mt.id, mt.name, d.name as department_name
    FROM manage_test mt
    JOIN department d ON mt.department_id = d.id
    ORDER BY mt.department_id ASC, mt.id ASC;
    ";
    $manageTests = $pdo->query($sqlManageTests)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // echo "Lỗi: " . $e->getMessage();
}

// Đóng kết nối PDO
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Trắc Nghiệm</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="apple-touch-icon" sizes="76x76" href="./admin/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./admin/assets/img/favicon.png">
    <style>
    body {
        background-color: #f4f9fd;
        font-family: 'Poppins', sans-serif;
        color: #333;
    }

    .container {
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
    h1.display-4 {
            font-weight: 900;
            color: #3498db;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        p.lead {
            font-size: 1.1rem;
            color: #95a5a6;
        }
    .title-header {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2980b9;
        text-align: center;
        margin-bottom: 20px;
    }
    .department-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.department-group {
    margin-bottom: 10px;
}

.department-btn {
    width: 100%;
}

.test-list {
    display: none;
    margin-top: 5px;
    padding: 15px;
    border-radius: 15px;
    background-color: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}


    .department-btn {
        flex: 1 1 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f4f4f4;
        color: #2980b9;
        padding: 10px;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        font-weight: bold;
        font-size: 1rem;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .department-btn:hover {
        background-color: #2980b9;
        color: #ffffff;
    }

    .department-btn .arrow {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background-color: #ffffff;
        color: #2980b9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .department-btn.open .arrow {
        transform: rotate(180deg);
    }

    .test-list {
        display: none;
        margin-top: 10px;
        padding: 15px;
        border-radius: 15px;
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .test-list.show {
        display: block;
    }

    .test-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .test-item:last-child {
        border-bottom: none;
    }

    .test-item .card-title {
        font-size: 1rem;
        color: #2980b9;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
        padding-right: 10px;
    }

    .btn-start {
        background-color: #3498db;
        color: #ffffff;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
        border: none;
        font-size: 0.9rem;
    }

    .btn-start:hover {
        background-color: #1abc9c;
    }

    /* Responsive design */
    @media (min-width: 768px) {
        .department-btn {
            flex: 1 1 calc(33.333% - 20px);
        }
    }

    @media (max-width: 480px) {
        .title-header {
            font-size: 1.5rem;
        }

        .btn-start {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        .test-item .card-title {
        white-space: normal; /* Cho phép xuống dòng */
        overflow: visible;   /* Bỏ giới hạn */
        text-overflow: clip; /* Bỏ dấu ba chấm */
    }
    }
    </style>
</head>

<body>
    <div class="container">
    <div class="text-center mb-4">
        <h1 class="display-4" style="font-family: 'Kaushan Script', cursive;">Armcuff Forms</h1>
        <p class="lead">Welcome to the Training Management System</p>
    </div>

        <?php if (empty($manageTests)) : ?>
            <div class="text-center">
                <p>Không có bài kiểm tra nào.</p>
            </div>
        <?php else : ?>
            <div class="department-container">
    <?php
    $currentDepartment = null;
    foreach ($manageTests as $test) {
        if ($currentDepartment !== $test['department_name']) {
            if ($currentDepartment !== null) {
                echo '</div></div>'; // Kết thúc nhóm bộ phận trước đó
            }
            $currentDepartment = $test['department_name'];
            echo '<div class="department-group">'; // Nhóm mới cho bộ phận
            echo '<div class="department-btn" onclick="toggleTests(\'' . htmlspecialchars($currentDepartment) . '\')">';
            echo '<span>' . htmlspecialchars($currentDepartment) . '</span>';
            echo '<div class="arrow">&#8593;</div>';
            echo '</div>';
            echo '<div class="test-list" id="' . htmlspecialchars($currentDepartment) . '">';
        }
    ?>
        <div class="test-item">
            <p class="card-title"><?php echo htmlspecialchars($test['name']); ?></p>
            <form action="test.php" method="post" style="margin: 0;">
                <input type="hidden" name="manage_test" value="<?php echo htmlspecialchars($test['id']); ?>">
                <input type="hidden" name="manv" value="<?php echo $employeeId; ?>">
                <input type="hidden" name="fullname" value="<?php echo $employeeName; ?>">
                <button type="submit" class="btn btn-start">Bắt đầu</button>
            </form>
        </div>
    <?php
    }
    echo '</div></div>'; // Đóng nhóm cuối cùng
    ?>
</div>

        <?php endif; ?>
    </div>

    <script>
        function toggleTests(departmentName) {
            var testList = document.getElementById(departmentName);
            var departmentTitle = testList.previousElementSibling;

            if (testList.classList.contains('show')) {
                testList.classList.remove('show');
                departmentTitle.classList.remove('open');
            } else {
                testList.classList.add('show');
                departmentTitle.classList.add('open');
            }
        }
    </script>
</body>

</html>

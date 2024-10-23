<?php include 'inc/header.php'; ?>
<?php include 'config/config.php'; ?>
<?php
// Kết nối cơ sở dữ liệu
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
    echo "Lỗi: " . $e->getMessage();
}

// Đóng kết nối PDO
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">
<link rel="icon" type="image/png" href="./admin/assets/img/favicon.png">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Trắc Nghiệm</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f9fd;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .container {
            padding: 40px 20px;
        }

        h1.display-4 {
            font-weight: 900;
            color: #2980b9;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .lead {
            font-size: 1.2rem;
            color: #7f8c8d;
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: #3498db;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1abc9c;
            transform: translateY(-4px);
        }

        .department-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #2c3e50;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-left: 5px solid #3498db;
            margin-bottom: 20px;
        }

        .department-title:hover {
            background-color: #3498db;
            color: #ffffff;
        }

        .department-title::before {
            content: url('folder-closed-icon.png');
            margin-right: 10px;
            transition: transform 0.3s ease-in-out;
        }

        .department-title.open::before {
            content: url('folder-open-icon.png');
        }

        .test-list {
            display: none;
            flex-wrap: wrap;
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .test-list.show {
            display: flex;
            opacity: 1;
        }

        .test-item {
            width: 100%;
            margin-bottom: 20px;
        }

        @media (min-width: 576px) {
            .test-item {
                width: 33.333%;
                padding: 15px;
            }
        }

        .card-title {
            font-size: 1.3rem;
            color: #2980b9;
            margin-bottom: 15px;
        }

        .text-center h1 {
            font-family: 'Kaushan Script', cursive;
            color: #2980b9;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        button.btn {
            background-color: #2980b9;
            color: #ffffff;
            font-weight: bold;
            border-radius: 30px;
            padding: 10px 20px;
        }

        button.btn:hover {
            background-color: #27ae60;
            color: #ffffff;
            transform: translateY(-2px);
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
            <?php
            $currentDepartment = null;
            foreach ($manageTests as $test) {
                if ($currentDepartment !== $test['department_name']) {
                    if ($currentDepartment !== null) {
                        echo '</div></div>';
                    }
                    echo '<div class="department-container">';
                    echo '<div class="department-title" onclick="toggleTests(\'' . htmlspecialchars($test['department_name']) . '\')">';

                    echo '<span>' . htmlspecialchars($test['department_name']) . '</span>';
                    echo '<span class="arrow">&#9660;</span>';
                    echo '</div>';
                    echo '<div class="test-list" id="' . htmlspecialchars($test['department_name']) . '">';
                    $currentDepartment = $test['department_name'];
                }
            ?>
                <div class="test-item">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($test['name']); ?></h4>
                            <form action="test.php" method="post">
                                <input type="hidden" name="manage_test" value="<?php echo htmlspecialchars($test['id']); ?>">
                                <input type="hidden" name="manv" value="<?php echo $employeeId; ?>">
                                <input type="hidden" name="fullname" value="<?php echo $employeeName; ?>">
                                <button type="submit" class="btn btn-primary">Bắt đầu</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
            }
            echo '</div></div>';
            ?>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTests(departmentName) {
            var testList = document.getElementById(departmentName);
            var departmentTitle = testList.previousElementSibling;

            if (testList.classList.contains('show')) {
                testList.classList.remove('show');
                departmentTitle.classList.remove('open'); // Thư mục đóng
            } else {
                testList.classList.add('show');
                departmentTitle.classList.add('open'); // Thư mục mở
            }
        }
    </script>
</body>
</html>

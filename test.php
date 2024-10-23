<?php include 'inc/header.php'; ?>
<?php

$exam = new Exam();
$questions = [];
$employeeId = $_SESSION['manv'];  // Lấy thông tin từ phiên
$employeeName = $_SESSION['displayName'];
$manageTestId = '';
$manageTestName = '';

// Kiểm tra nếu có dữ liệu POST (ví dụ khi người dùng chọn bài kiểm tra)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $manageTestId = $_POST['manage_test'] ?? '';

    if (!empty($manageTestId)) {
        try {
            // Lấy câu hỏi dựa trên bài kiểm tra
            $questions = $exam->getQuestionsByTest($manageTestId);
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// Lấy danh sách bài kiểm tra từ cơ sở dữ liệu
$manageTests = $exam->getManageTests();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <?php if (empty($questions)): ?>
                <div class="form-header">
                    <?php
                    // Xác định tên bài kiểm tra dựa trên ID
                    foreach ($manageTests as $test) {
                        if ($test['id'] == $manageTestId) {
                            $manageTestName = $test['name'];
                            break;
                        }
                    }
                    ?>
                    <h1 style="margin-top: 29px; color: seagreen; font-weight: bold; text-transform: uppercase;">
                        BÀI KIỂM TRA <?php echo htmlspecialchars($manageTestName); ?>
                    </h1>
                </div>
                <?php include 'form_ques.php'; ?>
            <?php else: ?>
                <div class="form-header">
                    <?php
                    // Xác định tên bài kiểm tra dựa trên ID
                    foreach ($manageTests as $test) {
                        if ($test['id'] == $manageTestId) {
                            $manageTestName = $test['name'];
                            break;
                        }
                    }
                    ?>
                    <h1 style="margin-top: 29px; color: seagreen; text-transform: uppercase; font-weight: bold;text-align: center;">
                        BÀI KIỂM TRA <?php echo htmlspecialchars($manageTestName); ?>
                    </h1>
                </div>
                <?php include 'form_ques.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

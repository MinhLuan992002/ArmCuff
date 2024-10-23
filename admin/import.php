<?php
require __DIR__ . '/../vendor/autoload.php'; // adjust as per the file structure
$filepath = realpath(dirname(__FILE__));
include_once realpath(dirname(__FILE__) . '/../classes/Main.php');
$main = new Main();

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import-file'])) {
    $file = $_FILES['import-file'];

    // Kiểm tra xem tệp có được tải lên thành công không
    if ($file['error'] === 0) {
        $filePath = $file['tmp_name']; // Đường dẫn tạm của file được upload

        try {
            // Đọc file Excel
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Bắt đầu xử lý từng dòng dữ liệu từ file Excel
            foreach ($data as $rowIndex => $row) {
                // Bỏ qua hàng tiêu đề nếu cần
                if ($rowIndex == 0) {
                    continue; // Bỏ qua hàng tiêu đề
                }
            
                // Lấy dữ liệu từ từng cột
                $testName = $row[0] ?? null; // Tên bài kiểm tra
                $departmentName = $row[1] ?? null; // Tên phòng ban
                $questionText = $row[2] ?? null; // Câu hỏi
                $correctAnswer = $row[3] ?? null; // Đáp án
                $isCorrect = (int)($row[4] ?? 0); // Đáp án đúng (1 hoặc 0)
            
                if ($testName && $departmentName && $questionText && $correctAnswer !== null) {
                    // Lấy ID bài kiểm tra và phòng ban dựa trên tên
                    $manageTestId = $main->getManageImp($testName); // Lấy ID bài kiểm tra từ tên
                    $departmentId = $main->getDepartmentImp($departmentName); // Lấy ID phòng ban từ tên
            
                    if ($manageTestId && $departmentId) {
                        // Kiểm tra xem câu hỏi đã tồn tại hay chưa
                        $questionId = $main->checkExistingQuestion($manageTestId, $departmentId, $questionText);
            
                        if (!$questionId) {
                            // Nếu câu hỏi chưa tồn tại, thêm mới
                            $questionId = $main->impQuestion($manageTestId, $departmentId, $questionText, null);
                        }
            
                        if ($questionId) {
                            // Thêm đáp án (bao gồm cả việc lưu đáp án đúng/sai)
                            $result = $main->addAll($questionId, [
                                'text' => $correctAnswer,
                                'image' => null,
                                'correct' => $isCorrect // Lưu giá trị 1 hoặc 0
                            ]);
            
                            if ($result) {
                                $successMessage .= "Thêm đáp án thành công cho câu hỏi tại hàng: " . ($rowIndex + 1) . ". ";
                            } else {
                                $errorMessage .= "Không thể thêm đáp án cho câu hỏi tại hàng: " . ($rowIndex + 1) . ". ";
                            }
                        } else {
                            $errorMessage .= "Không thể thêm câu hỏi tại hàng: " . ($rowIndex + 1) . ". ";
                        }
                    } else {
                        $errorMessage .= "Không tìm thấy bài kiểm tra hoặc phòng ban tại hàng: " . ($rowIndex + 1) . ". ";
                    }
                } else {
                    $errorMessage .= "Dữ liệu không hợp lệ tại hàng: " . ($rowIndex + 1) . ". ";
                }
            }
            
            if (empty($errorMessage)) {
                $successMessage = "Import dữ liệu thành công!";
            }
        } catch (Exception $e) {
            $errorMessage = "Lỗi khi xử lý file: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Có lỗi khi tải lên tệp.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả Import Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Kết quả Import Excel</h1>
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Không có thông báo cụ thể.
            </div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary mt-3">Quay lại</a>
    </div>
</body>
</html>

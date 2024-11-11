<?php
$filepath = realpath(dirname(__FILE__));
include_once $filepath . '/../classes/Main.php';
$main = new Main();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_name'], $_POST['department_id'])) {
    $testName = $_POST['test_name'];
    $departmentId = $_POST['department_id'];

    $testId = $main->addTest($testName, $departmentId);
    if ($testId) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Thêm bài kiểm tra thành công!',
            'new_test_id' => $testId
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể thêm bài kiểm tra.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
}
?>

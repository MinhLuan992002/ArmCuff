<?php
$filepath = realpath(dirname(__FILE__));
include_once $filepath . '/../classes/Main.php';
$main = new Main();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['department_name'])) {
    $departmentName = $_POST['department_name'];

    $departmentId = $main->addDepartment($departmentName);
    if ($departmentId) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Thêm phòng ban thành công!',
            'new_department_id' => $departmentId
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể thêm phòng ban.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
}
?>

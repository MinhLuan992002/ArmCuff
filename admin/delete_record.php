<?php
// Cấu hình kết nối cơ sở dữ liệu
include_once('../config/config.php');

header('Content-Type: application/json'); // Thiết lập tiêu đề trả về là JSON

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'] ?? null;

if ($code) {
    try {
        // Cập nhật trạng thái isDelete thành 1 và isActive thành 0
        $sql = "UPDATE user_answer SET isDeleted = 1, isActive = 0 WHERE code = :code";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':code', $code);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Cập nhật không thành công']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Mã không hợp lệ']);
}

exit();

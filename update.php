<?php include 'config/config.php'; ?>
<?php
// update.php

// Kết nối cơ sở dữ liệu
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8");


// Lấy dữ liệu JSON từ POST request
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra xem dữ liệu có phải là mảng không
if (!is_array($data)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
    exit;
}

// Kết nối đến cơ sở dữ liệu

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Chuẩn bị câu lệnh SQL để thêm thông tin mới
$stmt = $conn->prepare("INSERT INTO training_info (manv, category, content, trained, visited, signature) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($data as $item) {
    // Gán các giá trị từ dữ liệu JSON vào các tham số của câu lệnh SQL
    $manv = $item['manv'];
    $category = $item['category'];
    $content = $item['content'];
    $trained = $item['trained'] ? 1 : 0;
    $visited = $item['visited'] ? 1 : 0;
    $signature = $item['signature'];
    
    $stmt->bind_param("sssiss", $manv, $category, $content, $trained, $visited, $signature);
    
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $stmt->error]);
        exit;
    }
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
?>
<?php
include_once('../config/config.php');
include_once('../classes/Main.php');

// Tạo đối tượng Main (giả sử lớp này có các phương thức để làm việc với cơ sở dữ liệu)
$main = new Main();

// Lấy dữ liệu từ form POST
$code = $_POST['code']; // Lấy mã code từ form
$answers = $_POST['answers']; // Lấy các câu trả lời từ form

// Kết nối tới cơ sở dữ liệu
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Chuẩn bị câu lệnh gọi stored procedure
$stmt = $conn->prepare("CALL inset_update_answer(?, ?, ?)");

// Lặp qua từng câu trả lời của người dùng để gọi stored procedure
foreach ($answers as $question_id => $answer_id) {
    // Thực hiện binding các tham số cho stored procedure
    $stmt->bind_param(
        'sis', // Kiểu dữ liệu: s (string), i (integer), s (string)
        $code,        // p_code: Mã `code` đã lấy từ form
        $question_id, // p_ques_id: ID của câu hỏi
        $answer_id    // p_answer: Đáp án của người dùng
    );
    
    // Thực thi stored procedure
    if (!$stmt->execute()) {
        // Nếu có lỗi, thông báo
        echo "Có lỗi xảy ra khi cập nhật kết quả: " . $stmt->error;
        exit();
    }
}

// Đóng kết nối
$stmt->close();
$conn->close();

// Chuyển hướng người dùng sau khi cập nhật kết quả thành công
header("Location: user_answer.php?code=$code");
exit();
?>

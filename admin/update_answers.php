<?php
$filepath = realpath(dirname(__FILE__));
include_once('../config/config.php');
include_once('../classes/Exam.php');

// Lấy giá trị của code từ GET
$code = $_GET['code'];

// Tạo đối tượng Exam
$exam = new Exam();

// Sử dụng $code để lấy thông tin bài kiểm tra
$userAnswers = $exam->getUserAnswersByCode($code);
$userInfo = $exam->getUserInfoByCode($code);
$test_id = $userInfo['test_id']; // Lấy test_id từ thông tin người dùng
$testName = $exam->getTestName($test_id);
$testDetails = $exam->getQuestionsAndAnswers($test_id); // Lấy câu hỏi và câu trả lời cho bài kiểm tra
?>
<?php include './share/share_head.php'; ?>
<style>
    .question-label {
        font-size: 1.25rem;
        font-weight: 700;
        color: #343a40; /* Màu chữ cho câu hỏi */
        margin-bottom: 0.5rem;
    }
    .answer-label {
        font-size: 1rem;
        color: #495057; /* Màu chữ cho đáp án */
    }
    .card-header {
        position: relative;
        background-color: #2DCE89;
        color: #fff;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header h4 {
        font-size: 2rem; /* Tăng kích thước chữ cho tên bài test */
        font-weight: bold;
        /* margin: 0; */
        text-decoration: none; /* Gạch chân tên bài test */
        text-align: center;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }
    .close-icon {
        font-size: 1.5rem; /* Kích thước cho icon thoát */
        color: #fff;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Tạo bóng cho card */
    }
    .card-body {
        padding: 2rem;
        background-color: #f8f9fa; /* Màu nền nhạt cho phần nội dung */
    }
    .form-check {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background-color: #fff;
        margin-bottom: 0.75rem;
    }
    .form-check:hover {
        background-color: #f1f3f5; /* Màu nền khi hover */
    }
    .form-check-input:checked ~ .form-check-label {
        font-weight: bold;
    }
    .text-success {
        font-size: 1.2rem; /* Tăng kích thước dấu tích xanh */
    }
    .text-danger {
        font-size: 1.2rem; /* Tăng kích thước dấu x đỏ */
    }
    .btn-primary {
        background-color: #0069d9;
        border-color: #0062cc;
        padding: 0.75rem 1.25rem;
        font-size: 1.25rem;
        font-weight: 600;
    }
</style>
</head>
<div class="container mt-5">
    <div class="card">
        <div class="card-header" >
            <h4 style="text-align: center;"><?php echo htmlspecialchars($testName); ?></h4>
            <a href="javascript:history.back()" class="close-icon"><i class="fas fa-times"></i></a> <!-- Icon thoát -->
        </div>
        <div class="card-body">
            <form action="update_result.php" method="post">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">
                <?php
                // Tạo mảng để lưu thông tin câu hỏi và đáp án
                $questions = [];
                foreach ($testDetails as $detail) {
                    $questions[$detail['question_id']]['question'] = $detail['question_text'];
                    $questions[$detail['question_id']]['answers'][] = [
                        'id' => $detail['answer_id'],
                        'text' => $detail['answer_text'],
                        'is_correct' => $detail['is_correct']
                    ];
                }

                foreach ($questions as $question_id => $question_info): ?>
                    <div class="form-group mt-4">
                        <h5 class="question-label"> <?php echo $question_info['question']; ?></h5>
                        <?php foreach ($question_info['answers'] as $answer): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[<?php echo htmlspecialchars($question_id); ?>]" value="<?php echo htmlspecialchars($answer['is_correct']); ?>"
                                    <?php
                                    // Kiểm tra xem đáp án người dùng đã chọn có trùng với is_correct hay không
                                    if (isset($userAnswers[$question_id]) && $userAnswers[$question_id] == $answer['is_correct']) {
                                        echo 'checked'; // Đánh dấu đáp án mà người dùng đã chọn
                                    }
                                    ?> />
                                <label class="form-check-label answer-label">
                                    <?php echo htmlspecialchars($answer['text']); ?>
                                </label>
                                <?php
                                // Hiển thị dấu tích xanh cho đáp án đúng và dấu x đỏ cho đáp án sai
                                if ($answer['is_correct']) {
                                    echo '<span class="ml-2 text-success"><i class="fas fa-check-circle"></i></span>'; // Dấu tích xanh cho đáp án đúng
                                } elseif (isset($userAnswers[$question_id]) && $userAnswers[$question_id] == 0 && !$answer['is_correct']) {
                                    echo '<span class="ml-2 text-danger"><i class="fas fa-times-circle"></i></span>'; // Dấu x đỏ cho đáp án sai
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-primary btn-block mt-4">Cập nhật kết quả</button>
            </form>
        </div>
    </div>
</div>

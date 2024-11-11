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

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả bài kiểm tra</title>
<?php include './share/share_head.php'; ?>
    <style>
        body {
            background-color: #eef2f7;
            color: #343a40;
            font-family: 'Arial', sans-serif;
        }

        .card {
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-header { 
            background-color: #2DCE89;
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px 10px 0 0;
        }

        .card-header h3 {
            font-size: 1.75rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: white;
        }

        .export-pdf-btn {
            color: white;
            background-color: #dc3545;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .export-pdf-btn:hover {
            background-color: #c82333;
        }

        .list-group-item {
            background-color: transparent;
            border: none;
            padding: 0.5rem 0;
            font-size: 1.1rem;
        }

        .question-label {
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #2d2d2d;
        }

        .form-check-label {
            font-size: 1.1rem;
            color: #495057;
        }

        .correct-answer {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .selected-answer {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
        }

        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .success-icon {
            color: green;
            margin-left: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 20px;
            font-size: 1.2rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Bài kiểm tra: <?php echo htmlspecialchars($testName); ?></h3>
                <a href="export_pdf.php?code=<?php echo htmlspecialchars($code); ?>" class="btn export-pdf-btn">
    <i class="fas fa-file-pdf"></i> Xuất PDF
</a>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Họ tên:</strong> <?php echo htmlspecialchars($userInfo['fullname']); ?></li>
                        <li class="list-group-item"><strong>Mã nhân viên:</strong> <?php echo htmlspecialchars($userInfo['manv']); ?></li>
                        <li class="list-group-item"><strong>Ngày làm bài:</strong> <?php echo htmlspecialchars($userInfo['test_date']); ?></li>
                        <li class="list-group-item"><strong>Số câu làm đúng:</strong> <?php echo htmlspecialchars($exam->getCorrectAnswersCount($userInfo['manv'], $test_id)); ?></li>
                    </ul>
                </div>

                <form>
                    <?php
                    $questions = [];
                    foreach ($testDetails as $detail) {
                        $questions[$detail['question_id']]['question'] = $detail['question_text'];
                        $questions[$detail['question_id']]['question_image'] = $detail['question_image']; // Lưu hình ảnh câu hỏi
                        $questions[$detail['question_id']]['answers'][] = [
                            'id' => $detail['answer_id'],
                            'text' => $detail['answer_text'],
                            'is_correct' => $detail['is_correct'],
                            'question_image' => $detail['question_image'], // Hình ảnh câu hỏi
                            'answer_image' => $detail['answer_image'] // Lưu hình ảnh câu trả lời
                        ];
                    }

                    foreach ($questions as $question_id => $question_info): ?>
                        <div class="form-group mb-4">
                            <label class="question-label">
                            <?php echo $question_info['question']; ?>

                                <?php if (!empty($question_info['question_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($question_info['question_image']); ?>" alt="Question Image" style="max-width: 100%; height: auto;">
                                <?php endif; ?>
                                
                                <?php if (isset($userAnswers[$question_id])): ?>
                                    <?php foreach ($question_info['answers'] as $answer): ?>
                                        <?php if ($answer['is_correct'] == $userAnswers[$question_id] && $answer['is_correct']): ?>
                                            <span class="success-icon">✔</span>
                                            <?php break; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </label>
                            <div class="form-check">
                                <?php foreach ($question_info['answers'] as $answer): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?php echo htmlspecialchars($question_id); ?>]" value="<?php echo htmlspecialchars($answer['id']); ?>" disabled
                                            <?php
                                            if (isset($userAnswers[$question_id]) && $userAnswers[$question_id] == $answer['is_correct']) {
                                                echo 'checked';
                                            }
                                            ?>
                                               />
                                        <label class="form-check-label">
                                            <?php echo htmlspecialchars($answer['text']); ?>

                                            <?php if (!empty($answer['answer_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($answer['answer_image']); ?>" alt="Answer Image" style="max-width: 100%; height: auto;">
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

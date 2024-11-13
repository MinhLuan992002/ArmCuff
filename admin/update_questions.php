<?php
include '../config/config.php';

header('Content-Type: application/json'); // Đảm bảo phản hồi là JSON

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['questions']) || empty($data['questions'])) {
        echo json_encode(['status' => 'error', 'message' => 'No questions to update.']);
        exit;
    }

    $pdo->beginTransaction();

    foreach ($data['questions'] as $question) {
        if (!isset($question['id'], $question['text'])) {
            throw new Exception("Invalid question data");
        }

        $sqlQuestion = "UPDATE questions SET name = :text WHERE id = :id";
        $stmtQuestion = $pdo->prepare($sqlQuestion);
        $stmtQuestion->execute([
            ':text' => $question['text'],
            ':id' => $question['id']
        ]);

        foreach ($question['answers'] as $answer) {
            if (!isset($answer['id'], $answer['text'], $answer['correct'])) {
                throw new Exception("Invalid answer data");
            }

            $sqlAnswer = "UPDATE answers SET answer = :text, correct = :correct WHERE id = :id";
            $stmtAnswer = $pdo->prepare($sqlAnswer);
            $stmtAnswer->execute([
                ':text' => $answer['text'],
                ':correct' => $answer['correct'] ? 1 : 0,
                ':id' => $answer['id']
            ]);
        }
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'All changes saved successfully.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

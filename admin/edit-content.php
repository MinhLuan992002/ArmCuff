<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Câu Hỏi và Trả Lời</title>
    <style>
        /* CSS đơn giản cho bảng */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Danh sách Câu Hỏi và Trả Lời</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Câu Hỏi</th>
                <th>Trả Lời</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td><?= htmlspecialchars($question['id']); ?></td>
                    <td><?= htmlspecialchars($question['question_text']); ?></td>
                    <td><?= htmlspecialchars($question['answer_text']); ?></td>
                    <td>
                        <a href="edit_question.php?id=<?= $question['id']; ?>">Sửa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

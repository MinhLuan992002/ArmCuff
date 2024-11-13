<?php
$filepath = realpath(dirname(__FILE__));
include_once realpath(dirname(__FILE__) . '/../classes/Main.php');
session_start();
$main = new Main();
$users = $main->getAllUsers();
$department_name = $_SESSION['department'];
$results = $main->getResults($manv = '', $day = '', $month = '', $year = '', $test_name = '', $department_name, $code = '');
?>
<style>
    form {
        max-width: 100%;
        padding: 10px;
    }

    .form-control-sm {
        min-width: 120px;
        /* Điều chỉnh kích thước trường nhập liệu */
    }

    button {
        padding: 6px 12px;
        /* Điều chỉnh padding nếu cần */
    }
</style>
<!DOCTYPE html>
<html lang="en">

<?php include './share/share_head.php'; ?>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>
    <?php include './assets/common/sidebar.php'; ?>
    <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
        <?php
        $pageTitle = "User Answer";
        include('./assets/common/nav_main.php') ?>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Edit Content</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">


<?php
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlManageTests = "
    SELECT mt.id, mt.name, d.name as department_name
    FROM manage_test mt
    JOIN department d ON mt.department_id = d.id
    ORDER BY mt.department_id ASC, mt.id ASC;
    ";
    $manageTests = $pdo->query($sqlManageTests)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>



 
    <style>
        .test-list { display: none; margin-top: 10px; }
        .test-list.show { display: block; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 style="text-align: center;">QUẢN LÍ BÀI KIỂM TRA</h2>
    <div class="container department-container">
    <?php
    $currentDepartment = null;
    foreach ($manageTests as $test) {
        if ($currentDepartment !== $test['department_name']) {
            if ($currentDepartment !== null) echo '</div></div>';
            $currentDepartment = $test['department_name'];
            echo '<div class="department-group mb-3">';
            echo '<div class="department-btn btn btn-primary d-flex justify-content-between align-items-center" onclick="toggleTests(\'' . htmlspecialchars($currentDepartment) . '\')">';
            echo '<span>' . htmlspecialchars($currentDepartment) . '</span>';
            echo '<span class="arrow">&#8593;</span>';
            echo '</div>';
            echo '<div class="test-list collapse" id="' . htmlspecialchars($currentDepartment) . '">';
        }
        echo '<div class="test-item card mt-2">';
        echo '<div class="card-body d-flex justify-content-between align-items-center">';
        echo '<p class="card-title mb-0">' . htmlspecialchars($test['name']) . '</p>';
        echo '<button class="btn btn-sm btn-outline-primary" onclick="loadQuestions(' . $test['id'] . ')">Chỉnh sửa</button>';
        echo '</div></div>';
    }
    echo '</div></div>';
    ?>
</div>


    <!-- Form chỉnh sửa câu hỏi -->
    <div id="questions-container" class="mt-4" style="display: none;">
        <h3 style="text-align: center;">CHỈNH SỬA CÂU HỎI</h3>
        <form id="questions-form">
            <div id="questions-list"></div>
            <button type="button" class="btn btn-success mt-3" onclick="confirmSaveChanges()">Lưu Thay Đổi</button>
        </form>
    </div>
</div>

<script src="./assets/js/core/jquery-3.6.0.min.js"></script>
<script>
// Hàm hiển thị danh sách câu hỏi của một bài kiểm tra
function loadQuestions(testId) {
    $.get(`get_questions.php?test_id=${testId}`, function(response) {
        if (response.questions.length === 0) {
            alert('Không có câu hỏi nào trong bài kiểm tra này.');
            return;
        }

        $('#questions-container').show();
        $('#questions-list').empty();

        response.questions.forEach((question, index) => {
            const questionHtml = `
                <div class="question-item mb-4" data-question-id="${question.id}">
                    <label>Câu hỏi ${index + 1}</label>
                    <textarea class="form-control mb-2" name="question_text_${question.id}">${question.text}</textarea>
                    <div class="answers-container">
                        ${question.answers.map(answer => `
                            <div class="answer-item mb-2" data-answer-id="${answer.id}">
                                <input type="text" class="form-control mb-1" name="answer_text_${answer.id}" value="${answer.text}">
                                <div class="form-check form-check-info text-start">
                                <input type="checkbox"  class="form-check-input" name="correct_answer_${answer.id}" ${answer.correct ? 'checked' : ''}>
                                <label>Đáp án đúng</label>
</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            $('#questions-list').append(questionHtml);
        });
    }, 'json');
}

// Xác nhận lưu thay đổi
function confirmSaveChanges() {
    if (confirm("Bạn có chắc muốn lưu các thay đổi?")) {
        saveAllQuestions();
    }
}

// Hàm lưu lại các thay đổi
function saveAllQuestions() {
    const questionsData = [];
    $('#questions-list .question-item').each(function() {
        const questionId = $(this).data('question-id');
        const questionText = $(this).find(`textarea[name="question_text_${questionId}"]`).val();
        const answers = [];

        $(this).find('.answer-item').each(function() {
            const answerId = $(this).data('answer-id');
            const answerText = $(this).find(`input[name="answer_text_${answerId}"]`).val();
            const correct = $(this).find(`input[name="correct_answer_${answerId}"]`).is(':checked');
            answers.push({ id: answerId, text: answerText, correct });
        });

        questionsData.push({ id: questionId, text: questionText, answers });
    });

    $.post('update_questions.php', { questions: questionsData }, function(response) {
        if (response.status === 'success') {
            alert('Đã lưu thành công!');
        } else {
            alert('Có lỗi xảy ra: ' + response.message);
        }
    }, 'json');
}

// Mở và đóng danh sách câu hỏi theo từng bộ phận
function toggleTests(departmentName) {
    var testList = document.getElementById(departmentName);
    document.querySelectorAll('.test-list').forEach(list => {
        if (list !== testList) list.classList.remove('show');
    });
    testList.classList.toggle('show');
}
</script>
<script>
function loadQuestions(testId) {
    console.log("Loading questions for test ID:", testId); // Debugging

    $.get(`get_questions.php?test_id=${testId}`, function(response) {
        console.log("Response received:", response); // Debugging: xem phản hồi từ server

        if (!response || !response.questions || response.questions.length === 0) {
            alert('Không có câu hỏi nào trong bài kiểm tra này hoặc dữ liệu không hợp lệ.');
            return;
        }

        $('#questions-container').show();
        $('#questions-list').empty();

        response.questions.forEach((question, index) => {
            const questionHtml = `
                <div class="question-item mb-4" data-question-id="${question.id}">
                    <label>Câu hỏi ${index + 1}</label>
                    <textarea class="form-control mb-2" name="question_text_${question.id}">${question.text}</textarea>
                    <div class="answers-container">
                        ${question.answers.map(answer => `
                            <div class="answer-item mb-2" data-answer-id="${answer.id}">
                                <input type="text" class="form-control mb-1" name="answer_text_${answer.id}" value="${answer.text}">
                                <div class="form-check form-check-info text-start">
                                    <input type="checkbox" class="form-check-input" name="correct_answer_${answer.id}" ${answer.correct ? 'checked' : ''}>
                                    <label>Đáp án đúng</label>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            $('#questions-list').append(questionHtml);
        });
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed: ", textStatus, errorThrown); // Debugging lỗi yêu cầu
        alert("Lỗi khi tải câu hỏi. Vui lòng kiểm tra lại.");
    });
}

</script>
<script>
    function confirmSaveChanges() {
    if (confirm("Bạn có chắc muốn lưu các thay đổi?")) {
        saveAllQuestions();
    }
}
function saveAllQuestions() {
    const questionsData = [];
    $('#questions-list .question-item').each(function() {
        const questionId = $(this).data('question-id');
        const questionText = $(this).find(`textarea[name="question_text_${questionId}"]`).val();
        const answers = [];

        $(this).find('.answer-item').each(function() {
            const answerId = $(this).data('answer-id');
            const answerText = $(this).find(`input[name="answer_text_${answerId}"]`).val();
            const correct = $(this).find(`input[name="correct_answer_${answerId}"]`).is(':checked');
            answers.push({ id: answerId, text: answerText, correct });
        });

        questionsData.push({ id: questionId, text: questionText, answers });
    });

    $.ajax({
        url: 'update_questions.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ questions: questionsData }),
        success: function(response) {
            if (response && response.status === 'success') {
                alert(response.message || 'Đã lưu thành công!');
            } else {
                alert('Có lỗi xảy ra: ' + (response.message || 'Không xác định'));
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Lỗi khi gửi yêu cầu: ' + textStatus);
        }
    });
}
</script>
</body>
</html>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer pt-3  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>,
                                Matsuya R&D Việt Nam
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ul class="nav nav-footer justify-content-center justify-content-lg-end">

                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted" target="_blank">About
                                        Us</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link text-muted" target="_blank">Blog</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="fa fa-cog py-2"> </i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3 ">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">ArmCuff Forms</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0 overflow-auto">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 active me-2" data-class="bg-white"
                        onclick="sidebarType(this)">White</button>
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2" data-class="bg-default"
                        onclick="sidebarType(this)">Dark</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="d-flex my-3">
                    <h6 class="mb-0">Navbar Fixed</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-sm-4">
                <div class="mt-2 mb-5 d-flex">
                    <h6 class="mb-0">Light / Dark</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--   Core JS Files   -->
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="./assets/js/core/jquery-3.6.0.min.js"></script>
    <script>
        function loadResult(code) {
            // Gửi yêu cầu AJAX tới server để lấy kết quả
            fetch(`get_test_result.php?code=${code}`)
                .then(response => response.text())
                .then(data => {
                    // Cập nhật nội dung của modal với dữ liệu trả về
                    document.getElementById('modal-body-content').innerHTML = data;
                    // Hiển thị modal
                    $('#resultModal').modal('show');
                })
                .catch(error => console.error('Có lỗi xảy ra:', error));
        }

        function editResult(code) {
            // Gửi yêu cầu AJAX tới server để lấy kết quả
            fetch(`update_answers.php?code=${code}`)
                .then(response => response.text())
                .then(data => {
                    // Cập nhật nội dung của modal với dữ liệu trả về
                    document.getElementById('modal-body-content').innerHTML = data;
                    // Hiển thị modal
                    $('#resultModal').modal('show');
                })
                .catch(error => console.error('Có lỗi xảy ra:', error));
        }

        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

    <!-- Github buttons -->
    <!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="./assets/js/argon-dashboard.min.js?v=2.0.4"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 
 


</body>

</html>
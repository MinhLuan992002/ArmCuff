<?php
$filepath = realpath(dirname(__FILE__));
include_once realpath(dirname(__FILE__) . '/../classes/Main.php');
$main = new Main();
$manageTests = $main->getManageTests(); // Lấy danh sách các bài kiểm tra
$departments = $main->getDepartments(); // Lấy danh sách các phòng ban

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Lấy manage_test_id và department_id từ dropdown
  $manageTestId = $_POST['manage-test-id'];
  $departmentId = $_POST['department-id'];

  // Lấy thông tin câu hỏi và xử lý upload hình ảnh câu hỏi
  $questionText = $_POST['question-text'];
  $questionImage = null;
  if (isset($_FILES['question-image']) && $_FILES['question-image']['error'] == 0) {
    $questionImage = $main->uploadImage($_FILES['question-image'], 'questions');
    if (!$questionImage) {
      $errorMessage = 'Không thể tải lên hình ảnh cho câu hỏi.';
    }
  }

  // Xử lý đáp án và upload hình ảnh cho mỗi đáp án
  $answers = [];
  if (isset($_POST['answers']) && is_array($_POST['answers'])) {
    foreach ($_POST['answers'] as $key => $answerText) {
      $answerImage = null;

      // Tái cấu trúc lại $_FILES để truy cập đúng cách
      if (isset($_FILES['answer_images']) && $_FILES['answer_images']['error'][$key] == 0) {
        // Tạo một mảng file tạm để dễ xử lý hơn
        $file = [
          'name' => $_FILES['answer_images']['name'][$key],
          'type' => $_FILES['answer_images']['type'][$key],
          'tmp_name' => $_FILES['answer_images']['tmp_name'][$key],
          'error' => $_FILES['answer_images']['error'][$key],
          'size' => $_FILES['answer_images']['size'][$key]
        ];

        // Upload ảnh của đáp án
        $answerImage = $main->uploadImage($file, 'answers');
        if (!$answerImage) {
          $errorMessage = 'Không thể tải lên hình ảnh cho đáp án.';
        }
      }

      // Kiểm tra xem đáp án nào là đáp án đúng
      $correct = (isset($_POST['correct-answer']) && $_POST['correct-answer'] == $key) ? true : false;

      // Thêm đáp án vào mảng để xử lý sau
      $answers[] = [
        'text' => $answerText,
        'image' => $answerImage,
        'correct' => $correct
      ];
    }
  }

  // Gọi stored procedure để lưu câu hỏi và đáp án
  if (empty($errorMessage)) {
    try {
      // Bước 1: Thêm câu hỏi và lấy question_id
      $questionId = $main->addQuestion($manageTestId, $departmentId, $questionText, $questionImage);

      if ($questionId) {
        // Bước 2: Thêm các đáp án với question_id
        foreach ($answers as $answer) {
          $main->addAnswerToQuestion($questionId, $answer);
        }
        $successMessage = 'Thêm câu hỏi và đáp án thành công!';
      } else {
        $errorMessage = 'Có lỗi xảy ra khi lưu câu hỏi.';
      }
    } catch (PDOException $e) {
      $errorMessage = 'Error: ' . $e->getMessage();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<?php include './share/share_head.php'; ?>
<!-- Bootstrap CSS -->
<style>
  .file-upload-area {
    border: 2px dashed #007bff;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    color: #007bff;
    background-color: #f8f9fa;
    margin-bottom: 20px;
  }

  .file-upload-area:hover {
    background-color: #e2e6ea;
  }
</style>


<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <?php include './assets/common/sidebar.php'; ?>
  <!-- Bootstrap JS and Popper.js -->

  <main class="main-content position-relative border-radius-lg">
    <!-- Navbar -->
    <?php
    $pageTitle = "Thêm câu hỏi và đáp án";
    include('./assets/common/nav_main.php');
    ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Thêm câu hỏi và đáp án</h6>
            </div>
            <div class="card-body">
              <!-- Hiển thị thông báo -->
              <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
              <?php elseif (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
              <?php endif; ?>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fileUploadModal">
                Import dữ liệu từ Excel
              </button>

              <!-- Modal chọn file Excel -->
              <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="fileUploadModalLabel">Chọn tệp Excel để Import</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <!-- Khu vực kéo thả hoặc chọn file -->
                      <div class="file-upload-area" id="file-upload-area">
                        <h4 id="selected-file-text">Kéo thả hoặc click vào đây để chọn tệp Excel</h4>
                      </div>

                      <!-- Input file ẩn -->
                      <input type="file" class="d-none" id="import-file" name="import-file" accept=".xlsx, .xls">
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                      <!-- Nút xác nhận sẽ submit form -->
                      <button type="button" class="btn btn-primary" id="confirm-upload-btn">Xác nhận Import</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Form ẩn để submit file -->
              <form id="import-form" action="import.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="import-file" id="hidden-import-file" class="d-none">
              </form>
              <!-- Form để thêm câu hỏi -->
              <form id="add-question-form" action="add-question.php" method="post" enctype="multipart/form-data">


                <script src="./assets/js/core/jquery-3.6.0.min.js"></script>
                <!-- Notification Alert -->
                <div id="notification" class="alert d-none" role="alert"></div>
                <div class=" mt-3">
                  <div class="row">
                    <!-- Dropdown chọn bài kiểm tra với nút thêm mới bên cạnh -->
                    <div class="col-6 mb-3 d-flex align-items-end">
                      <div class="w-100">
                        <label for="manage-test-id" class="form-label">Chọn bài kiểm tra</label>
                        <select class="form-control" id="manage-test-id" name="manage-test-id" required>
                          <option value="">Chọn bài kiểm tra</option>
                          <?php if (!empty($manageTests)): ?>
                            <?php foreach ($manageTests as $test): ?>
                              <option value="<?php echo $test['id']; ?>"><?php echo $test['name']; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                      <div class="ms-2">
                        <i style="font-size: 29px;" data-bs-toggle="modal" data-bs-target="#addTestModal" class="fa-regular fa-square-plus text-primary"></i>
                      </div>
                    </div>

                    <!-- Dropdown chọn phòng ban với nút thêm mới bên cạnh -->
                    <div class="col-6 mb-3 d-flex align-items-end">
                      <div class="w-100">
                        <label for="department-id" class="form-label">Chọn phòng ban</label>
                        <select class="form-control" id="department-id" name="department-id" required>
                          <option value="">Chọn phòng ban</option>
                          <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                              <option value="<?php echo $department['id']; ?>"><?php echo $department['name']; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                      <div class=" btn-outline-primary ms-2">
                        <i style="font-size: 29px;" data-bs-toggle="modal" data-bs-target="#addDepartmentModal" class="fa-regular fa-square-plus text-primary"></i>
                      </div>
                    </div>
                  </div>



                  <!-- Modal thêm bài kiểm tra mới -->
                  <div class="modal fade" id="addTestModal" tabindex="-1" aria-labelledby="addTestModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Thêm bài kiểm tra mới</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="text" id="new-test-name" class="form-control" placeholder="Nhập tên bài kiểm tra">
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                          <button type="button" class="btn btn-primary" id="save-new-test">Lưu</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modal thêm phòng ban mới -->
                  <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Thêm phòng ban mới</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="text" id="new-department-name" class="form-control" placeholder="Nhập tên phòng ban">
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                          <button type="button" class="btn btn-primary" id="save-new-department">Lưu</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>




                <!-- Câu hỏi -->
                <div class="mb-3">
                  <label for="question-text" class="form-label">Câu hỏi</label>
                  <textarea class="form-control" id="question-text" name="question-text" rows="3" placeholder="Nhập câu hỏi" required></textarea>
                </div>

                <!-- Tải lên hình ảnh cho câu hỏi -->
                <div class="mb-3">
                  <label for="question-image" class="form-label">Tải lên hình ảnh cho câu hỏi (không bắt buộc)</label>
                  <input type="file" class="form-control" id="question-image" name="question-image" accept="image/*">
                </div>

                <!-- Đáp án -->
                <div id="answers-container">
                  <div class="mb-3 answer-block">
                    <label for="answer-text-1" class="form-label">Đáp án 1</label>
                    <textarea class="form-control" id="answer-text-1" name="answers[]" rows="2" placeholder="Nhập đáp án"></textarea>

                    <!-- Tải lên hình ảnh cho đáp án -->
                    <label for="answer-image-1" class="form-label mt-2">Tải lên hình ảnh cho đáp án (không bắt buộc)</label>
                    <input type="file" class="form-control" id="answer-image-1" name="answer_images[]" accept="image/*">

                    <!-- Chọn đáp án đúng -->
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="radio" name="correct-answer" id="correct-answer-1" value="0">
                      <label class="form-check-label" for="correct-answer-1">Đáp án đúng</label>
                    </div>
                  </div>
                </div>

                <!-- Nút thêm đáp án -->
                <button type="button" class="btn btn-outline-primary" id="add-answer-btn">
                  <i class="fa fa-plus"></i> Thêm đáp án
                </button>

                <!-- Nút submit -->
                <div class="mt-4">
                  <button type="submit" class="btn btn-primary">Lưu câu hỏi và đáp án</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>

    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
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
    <script>
      // Khi click vào khu vực chọn file, mở file dialog
      document.getElementById('file-upload-area').addEventListener('click', function() {
        document.getElementById('import-file').click();
      });

      // Cập nhật tên file sau khi chọn file
      document.getElementById('import-file').addEventListener('change', function() {
        const fileName = this.files[0].name;
        document.getElementById('selected-file-text').textContent = `File đã chọn: ${fileName}`;
      });

      // Khi nhấn "Xác nhận Import", submit form
      document.getElementById('confirm-upload-btn').addEventListener('click', function() {
        const fileInput = document.getElementById('import-file');
        if (fileInput.files.length > 0) {
          document.getElementById('hidden-import-file').files = fileInput.files; // Gán file vào form ẩn
          document.getElementById('import-form').submit(); // Submit form
        } else {
          alert("Vui lòng chọn một tệp Excel.");
        }
      });
    </script>
    <script>
      document.getElementById('add-answer-btn').addEventListener('click', function() {
        const answerCount = document.querySelectorAll('.answer-block').length + 1;
        const newAnswerBlock = document.createElement('div');
        newAnswerBlock.classList.add('mb-3', 'answer-block');

        newAnswerBlock.innerHTML = `
      <label for="answer-text-${answerCount}" class="form-label">Đáp án ${answerCount}</label>
      <textarea class="form-control" id="answer-text-${answerCount}" name="answers[]" rows="2" placeholder="Nhập đáp án"></textarea>

      <label for="answer-image-${answerCount}" class="form-label mt-2">Tải lên hình ảnh cho đáp án (không bắt buộc)</label>
      <input type="file" class="form-control" id="answer-image-${answerCount}" name="answer_images[]" accept="image/*">

      <div class="form-check mt-2">
        <input class="form-check-input" type="radio" name="correct-answer" id="correct-answer-${answerCount}" value="${answerCount - 1}">
        <label class="form-check-label" for="correct-answer-${answerCount}">Đáp án đúng</label>
      </div>
      `;

        document.getElementById('answers-container').appendChild(newAnswerBlock);
      });
    </script>
                    <script>
                  $(document).ready(function() {
                    function showNotification(message, isSuccess) {
                      const notification = $('#notification');
                      notification.removeClass('d-none alert-success alert-danger');
                      notification.addClass(isSuccess ? 'alert-success' : 'alert-danger').text(message).fadeIn();
                      setTimeout(() => notification.fadeOut(), 3000); // Auto-hide after 3 seconds
                    }

                    // Lưu bài kiểm tra mới
                    $('#save-new-test').on('click', function() {
                      const testName = $('#new-test-name').val();
                      const departmentId = $('#department-id').val();
                      if (testName && departmentId) {
                        $.post('add_test.php', {
                          test_name: testName,
                          department_id: departmentId
                        }, function(response) {
                          if (response.status === 'success') {
                            $('#manage-test-id').append(new Option(testName, response.new_test_id, true, true));
                            $('#addTestModal').modal('hide');
                            $('#new-test-name').val('');
                            showNotification('Thêm bài kiểm tra thành công!', true);
                          } else {
                            showNotification(response.message || 'Không thể thêm bài kiểm tra.', false);
                          }
                        }, 'json').fail(() => showNotification('Lỗi khi thêm bài kiểm tra.', false));
                      } else {
                        showNotification("Vui lòng nhập tên bài kiểm tra và chọn phòng ban.", false);
                      }
                    });

                    // Lưu phòng ban mới
                    $('#save-new-department').on('click', function() {
                      const departmentName = $('#new-department-name').val();
                      if (departmentName) {
                        $.post('add_department.php', {
                          department_name: departmentName
                        }, function(response) {
                          if (response.status === 'success') {
                            $('#department-id').append(new Option(departmentName, response.new_department_id, true, true));
                            $('#addDepartmentModal').modal('hide');
                            $('#new-department-name').val('');
                            showNotification('Thêm phòng ban thành công!', true);
                          } else {
                            showNotification(response.message || 'Không thể thêm phòng ban.', false);
                          }
                        }, 'json').fail(() => showNotification('Lỗi khi thêm phòng ban.', false));
                      } else {
                        showNotification("Vui lòng nhập tên phòng ban.", false);
                      }
                    });

                    // Ensure page scrolls after modals close
                    $('#addTestModal, #addDepartmentModal').on('hidden.bs.modal', function() {
                      $('body').removeClass('modal-open');
                      $('.modal-backdrop').remove(); // Remove lingering backdrop
                    });
                  });
                </script>

                <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
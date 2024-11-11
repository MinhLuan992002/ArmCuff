<?php
$filepath = realpath(dirname(__FILE__));
include_once realpath(dirname(__FILE__) . '/../classes/Main.php');
session_start();
$main = new Main();
$users = $main->getAllUsers();
$department_name=$_SESSION['department'];
$results = $main->getResults($manv = '', $day = '', $month = '', $year = '', $test_name = '',$department_name);
?>
<!DOCTYPE html>
<html lang="en">

<?php include './share/share_head.php'; ?>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <?php include './assets/common/sidebar.php'; ?>
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <?php
    $pageTitle = "Users";
    include('./assets/common/nav_main.php') ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Users table</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Author</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Function</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employed</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($users): ?>
                      <?php foreach ($users as $user): ?>
                        <tr>
                          <td>
                            <div class="d-flex px-2 py-1">
                              <div>
                                <img src="./assets/img/user.png" class="avatar avatar-sm me-3" alt="user">
                              </div>
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?= htmlspecialchars($user['fullname']); ?></h6>
                                <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($user['username']); ?></p>
                              </div>
                            </div>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($user['Code']); ?></p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm <?= $user['IsActive'] ? 'bg-gradient-success' : 'bg-gradient-secondary'; ?>">
                              <?= $user['IsActive'] ? 'Online' : 'Offline'; ?>
                            </span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?= date('d/m/y', strtotime($user['created_at'])); ?></span>
                          </td>
                          <td class="align-middle">
                            <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                              Edit
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Projects table</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã nv</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tên bài test</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số câu đúng</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kết quả</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Hoàn thành</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thời gian</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($results as $row): ?>
                      <tr>
                        <td>
                          <p style="margin-left: 22px;" class="text-sm font-weight-bold mb-0"><?= $row['STT'] ?></p>
                        </td>
                        <td>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= htmlspecialchars($row['fullname']); ?></h6>
                            <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($row['manv']); ?></p>
                          </div>
                        </td>
                        <td>
                          <div class="d-flex flex-column justify-content-center">
                            <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($row['test_name']); ?></p>

                          </div>
                        </td>
                        <td>

                          <div class="d-flex flex-column justify-content-center ">
                            <p class="mb-0 text-sm"><?= htmlspecialchars($row['correct_answers']); ?></p>

                          </div>

                        <td>
                          <span class="text-xs font-weight-bold 
                      <?php if ($row['result_status'] == 'Đạt') {
                        echo 'text-success';
                      } else {
                        echo 'text-danger';
                      } ?>">
                            <?= htmlspecialchars($row['result_status']) ?>
                          </span>

                        </td>
                        <td class="align-middle text-center">
                          <div class="d-flex align-items-center justify-content-center">
                            <span class="me-2 text-xs font-weight-bold"><?= $row['score'] ?>%</span>
                            <div>
                              <div class="progress">
                                <div class="progress-bar bg-gradient-<?= $row['score'] >= 100 ? 'success' : 'danger' ?>" role="progressbar" aria-valuenow="<?= $row['score'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $row['score'] ?>%;"></div>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="d-flex flex-column justify-content-center ">
                            <p class="mb-0 text-sm"><?= date('d/m/y', strtotime($row['test_date'])); ?></p>

                          </div>
                        </td>
                        <td class="align-middle">
                          <button class="btn btn-link text-secondary mb-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v text-xs"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" onclick="editResult('<?php echo htmlspecialchars($row['code']); ?>')" data-bs-toggle="modal" data-bs-target="#resultModal" href="#">Edit</a></li>
                            <li><a class="dropdown-item" onclick="loadResult('<?php echo htmlspecialchars($row['code']); ?>')" data-bs-toggle="modal" data-bs-target="#resultModal" href="#">View</a></li>

                            <!-- Kết thúc ul ở đây -->

                        </td>



                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <!-- Modal -->
                <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">

                      </div>
                      <div class="modal-body" id="modal-body-content">

                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
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
          <h5 class="mt-3 mb-0">Argon Configurator</h5>
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
  </script>
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
</body>

</html>
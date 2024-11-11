<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
<link rel="stylesheet" href="./css/sweetalert2.min.css">
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="./js/sweetalert2_nof.js"></script>


<script>
// Hàm hiển thị thông báo thành công
function showSuccessNotification(data) {
    let tableContent = `
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">Thông tin</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Giá trị</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Thời gian</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">${new Date().toLocaleString()}</td>
                </tr>
            </tbody>
        </table>
    `;

    Swal.fire({
        title: 'Thành công!',
        html: tableContent,
        icon: 'success',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 3000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}

// Hàm hiển thị thông báo lỗi
function showErrorNotification(message) {
    return Swal.fire({
        title: 'Lỗi!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 3000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}


// Hàm hiển thị thông báo hướng dẫn
function showGuideNotification(guideText) {
    Swal.fire({
        title: 'Thông báo!',
        html: `<p>${guideText}</p>`,
        icon: 'info',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 3000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}


</script>
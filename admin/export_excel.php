<?php
require '../vendor/autoload.php';
include_once('../config/config.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Kết nối đến cơ sở dữ liệu
$filepath = realpath(dirname(__FILE__));
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra và lấy danh sách `codes` từ URL
$codes = isset($_GET['codes']) ? explode(',', $_GET['codes']) : [];
if (empty($codes)) {
    die("Không có mã code nào được chọn!");
}

// Khởi tạo đối tượng Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề lớn cho bảng điểm
$sheet->mergeCells('A1:K1');  // Gộp các ô từ A1 đến K1
$sheet->setCellValue('A1', 'BẢNG ĐIỂM - THÔNG TIN CHI TIẾT');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);  // Đặt font chữ đậm và kích thước lớn cho tiêu đề
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);  // Căn giữa tiêu đề
$sheet->getRowDimension('1')->setRowHeight(30); // Tăng chiều cao hàng tiêu đề

// Thêm tiêu đề cho các cột
$sheet->setCellValue('A3', 'STT');
$sheet->setCellValue('B3', 'Mã NV');
$sheet->setCellValue('C3', 'Họ tên');
$sheet->setCellValue('D3', 'Phòng ban');
$sheet->setCellValue('E3', 'Tên bài test');
$sheet->setCellValue('F3', 'Code');
$sheet->setCellValue('G3', 'Số câu đúng');
$sheet->setCellValue('H3', 'Tổng câu hỏi');
$sheet->setCellValue('I3', 'Ngày test');
$sheet->setCellValue('J3', 'Điểm (%)');
$sheet->setCellValue('K3', 'Kết quả');

// Thiết lập kiểu cho tiêu đề bảng
$headerStyleArray = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A3:K3')->applyFromArray($headerStyleArray);
$sheet->getRowDimension('3')->setRowHeight(20);  // Đặt chiều cao hàng cho tiêu đề

// Chuẩn bị và gọi thủ tục cho từng `code` trong danh sách
$row = 4;  // Bắt đầu từ dòng 4, dưới tiêu đề
foreach ($codes as $code) {
    $stmt = $conn->prepare("CALL getCheckAnswerUser(NULL, NULL, NULL, NULL, NULL, NULL, ?)");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    // Đổ dữ liệu từ kết quả truy vấn vào Excel
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue("A{$row}", $data['STT']);
        $sheet->setCellValue("B{$row}", $data['manv']);
        $sheet->setCellValue("C{$row}", $data['fullname']);
        $sheet->setCellValue("D{$row}", $data['department_name']);
        $sheet->setCellValue("E{$row}", $data['test_name']);
        $sheet->setCellValue("F{$row}", $data['code']);
        $sheet->setCellValue("G{$row}", $data['correct_answers']);
        $sheet->setCellValue("H{$row}", $data['total_questions']);
        $sheet->setCellValue("I{$row}", $data['test_date']);
        $sheet->setCellValue("J{$row}", $data['score'] . '%');
        $sheet->setCellValue("K{$row}", $data['result_status']);
        
        // Áp dụng đường viền cho từng dòng dữ liệu
        $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Căn giữa cho các cột điểm và kết quả
        $sheet->getStyle("A{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
    }
    $stmt->close();
}

// Đặt kích thước cột tự động
foreach (range('A', 'K') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Tạo đối tượng writer để xuất file Excel
$writer = new Xlsx($spreadsheet);
$fileName = 'bang_diem_user_answers.xlsx';

// Thiết lập header để tải file về
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");
header('Cache-Control: max-age=0');

// Xuất file Excel
$writer->save('php://output');
exit;

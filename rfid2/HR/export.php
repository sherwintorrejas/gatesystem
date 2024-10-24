<?php
require __DIR__ . '/../phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['data'])) {
    // Decode the JSON data sent via AJAX
    $data = json_decode($_POST['data'], true);

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header row
    $sheet->setCellValue('A1', 'UID');
    $sheet->setCellValue('B1', 'ID');
    $sheet->setCellValue('C1', 'Name');
    $sheet->setCellValue('D1', 'Department');
    $sheet->setCellValue('E1', 'Date');
    $sheet->setCellValue('F1', 'Status');

    // Fill data into spreadsheet
    $row = 2; // Start from the second row
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['uid']);
        $sheet->setCellValue('B' . $row, $item['schoolid']);
        $sheet->setCellValue('C' . $row, $item['name']);
        $sheet->setCellValue('D' . $row, $item['department']);
        $sheet->setCellValue('E' . $row, $item['formatted_datetime']);
        $sheet->setCellValue('F' . $row, $item['category']);
        $row++;
    }

    // Create a writer instance
    $writer = new Xlsx($spreadsheet);

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Student_dailylogs.xlsx"');
    header('Cache-Control: max-age=0');

    // Save to output stream
    $writer->save('php://output');
    exit;
}
?>

<?php
session_start();
include 'account.php';
$account = new account();
$accountId = $account->getCurrentUserId();

if(!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit;
}

// Fetch items again (same as form)
$itemdata = $account->get_create_ItemData();

// Load PhpSpreadsheet
require 'vendor/autoload.php';  // make sure you installed phpoffice/phpspreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header row
$sheet->setCellValue('A1', 'Item Code')
      ->setCellValue('B1', 'Name of the Item')
      ->setCellValue('C1', 'Purchased Qty')
      ->setCellValue('D1', 'Purchased Price per unit');
     // ->setCellValue('E1', 'Unit')
      //->setCellValue('F1', 'Total Cost');

$row = 2;
$index = 1;
foreach ($itemdata as $item) {
    $sheet->setCellValue('A' . $row, $item['id_no']);
    $sheet->setCellValue('B' . $row, $item['name'] . '/' . $item['kannada_name']);
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
  //  $sheet->setCellValue('E' . $row, 'kg'); // default
  //  $sheet->setCellValue('F' . $row, '');
    $row++;
    $index++;
}

// Set header bold
$sheet->getStyle("A1:F1")->getFont()->setBold(true);

// Auto size columns
foreach (range('A','F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output as download
$writer = new Xlsx($spreadsheet);
$filename = "Purchase_Form_" . date('Y-m-d') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save("php://output");
exit;

<?php
session_start();

include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 
$pid = $_GET['id'];
$items = $account->getGoodsReceive_noteByID($accountId,$pid);
date_default_timezone_set('Asia/Kolkata');
$cdate = date('d/m/Y');
//print_r($items); exit;


// Include the Composer autoload file
require 'vendor/autoload.php';

// Use PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);


// Set the column headers
$sheet->setCellValue('A1', 'Vendors Name');
$sheet->setCellValue('A2', 'Bill Number');
$sheet->setCellValue('A3', 'Collection Center');
$sheet->setCellValue('A4', 'current Date');


// Set the column headers
$sheet->setCellValue('A6', 'Item Name');
$sheet->setCellValue('B6', 'Quantity');
$sheet->setCellValue('C6', 'Price');
$sheet->setCellValue('D6', 'Total Amount');

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(22);

// Make the header row bold
$headerStyleArray = [
    'font' => [
        'bold' => true,
    ],
];
$sheet->getStyle('A1:A4')->applyFromArray($headerStyleArray);
$sheet->getStyle('A6:D6')->applyFromArray($headerStyleArray);

// Fill the data 
$sheet->setCellValue('B1', $items[0]['farmers_name']);
$sheet->setCellValue('B2', $items[0]['bill_number']);
$sheet->setCellValue('B3', $items[0]['collection_center']);
$sheet->setCellValue('B4', $cdate);

// Fill in the data starting from row 7
$row = 7;
foreach ($items as $item) {
    $sheet->setCellValue('A' . $row, $item['item_name']);
    $sheet->setCellValue('B' . $row, $item['quantity']);
    $sheet->setCellValue('C' . $row, $item['price']);
    $sheet->setCellValue('D' . $row,$item['quantity']*$item['price']);
    $row++;
}

// Add transportation, other expenses, and total amount rows
// Transportation row
$sheet->setCellValue('A' . $row, 'Transportation');
$sheet->mergeCells("A{$row}:C{$row}"); // Merge cells from A to C
$sheet->setCellValue('D' . $row, $item['transportation']);
$sheet->getStyle('A'. $row)->applyFromArray($headerStyleArray);
$sheet->getStyle('A'. $row)->getAlignment()->setHorizontal('center');
$row++;
// Other expenses row
$sheet->setCellValue('A' . $row, 'Other Expenses');
$sheet->mergeCells("A{$row}:C{$row}");
$sheet->setCellValue('D' . $row, $item['other_expenses']);
$sheet->getStyle('A'. $row)->applyFromArray($headerStyleArray);
$sheet->getStyle('A'. $row)->getAlignment()->setHorizontal('center');

$row++;

// Total amount row
$sheet->setCellValue('A' . $row, 'Total Amount');
$sheet->mergeCells("A{$row}:C{$row}");
$sheet->setCellValue('D' . $row, $item['totamt']);

// Make the summary rows bold
$sheet->getStyle("A{$row}:D{$row}")->applyFromArray($headerStyleArray);
$sheet->getStyle('A'. $row)->getAlignment()->setHorizontal('center');



// Set headers for the file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="GRN.xlsx"');
header('Cache-Control: max-age=0');

// Create a writer instance and save to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

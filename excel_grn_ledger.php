<?php
session_start();

include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 
//print_r($items); exit;
if(!empty($_POST))
{
	//print_r($_POST);
	$vendors_list = isset($_POST['vendors_list'])? $_POST['vendors_list'] : NULL;  
	$names_list = isset($_POST['names_list'])? $_POST['names_list'] : NULL;  
	$itemdata = $account->grnsearch_ledger_excel($vendors_list,$names_list);
	//print_r($itemdata); exit;
}


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
if(!empty($itemdata))
{


// Set the column headers
$sheet->setCellValue('A1', 'Party Name');
$sheet->setCellValue('A2', 'Party Contact');
$sheet->setCellValue('A3', 'Date');


// Set the column headers
$sheet->setCellValue('A5', 'Date');
$sheet->setCellValue('B5', 'Invoice/Bill No');
$sheet->setCellValue('C5', 'Total Amount');
$sheet->setCellValue('D5', 'TXn Type');
$sheet->setCellValue('E5', 'Received');
$sheet->setCellValue('F5', 'Paid');

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(22);

// Make the header row bold
$headerStyleArray = [
    'font' => [
        'bold' => true,
    ],
];
$sheet->getStyle('A1:A3')->applyFromArray($headerStyleArray);
$sheet->getStyle('A5:F5')->applyFromArray($headerStyleArray);


// Fill the data 
$sheet->setCellValue('B1', $itemdata[0]['clients_name']);
$sheet->setCellValue('B2', $itemdata[0]['contact']);
$sheet->setCellValue('B3', $itemdata[0]['regdate']);

//// Fill in the data starting from row 7
$row = 6;
$first_i = $row;

foreach ($itemdata as $item) {
    $sheet->setCellValue('A' . $row, $item['regdate']);
    $sheet->setCellValue('B' . $row, $item['billnumber']);
    $sheet->setCellValue('C' . $row, $item['totamt']);
    $row++;
}

// Total row
$last_i = $row - 1;
$sumrange = 'C' . $first_i . ':C' . $last_i;

$sheet->setCellValue('B' . $row, 'Final Amount');
$sheet->setCellValue('C' . $row, '=SUM(' . $sumrange . ')');
$sheet->getStyle('B'. $row)->applyFromArray($headerStyleArray);
$sheet->getStyle('B'. $row)->getAlignment()->setHorizontal('center');

}else
{
$sheet->setCellValue('A1', 'No data found');
}
//$row++;

// Set headers for the file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="GRN_ledger.xlsx"');
header('Cache-Control: max-age=0');

// Create a writer instance and save to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

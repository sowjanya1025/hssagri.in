<?php
session_start();

include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 
//$pid = $_GET['id'];
//$items = $account->getGoods_SupplyBillByID($accountId,$pid);
date_default_timezone_set('Asia/Kolkata');
$cdate = date('d/m/Y');
//print_r($items); exit;
if(!empty($_POST))
{
	//print_r($_POST);
	$fromdate = isset($_POST['fromdate'])? $_POST['fromdate'] : NULL;  
	$todate = isset($_POST['todate'])? $_POST['todate'] : NULL;  
	$itemdata = $account->gsbsearch_datesearch_excel($fromdate,$todate);
//	foreach ($itemdata as $item) {
//		echo  $item['clients_name']."<br>";
//		echo $item['billnumber']."<br>";
//		echo $item['collection_center']."<br>";
//		echo $item['transportation']."<br>";
//		echo $item['otherexpenses']."<br>";
//		
//		
//					//$itemrow = $row;
//					if(isset($item['itemlist']))
//					 {  
//							foreach($item['itemlist'] as $keyy=>$valy)
//							{
//									echo  $valy['item_name']."<br>";
//							}
//						//$itemrow++;
//					  }
//		//$row = $itemrow;
//		//$row++;
	//}
	
	
	//exit;
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
$spreadsheet->getDefaultStyle()->getFont()->setSize(9);


// Set the column headers
$sheet->setCellValue('A1', 'Clients Name');
$sheet->setCellValue('B1', 'Bill Number');
$sheet->setCellValue('C1', 'Collection Center');
$sheet->setCellValue('D1', 'Transportation');
$sheet->setCellValue('E1', 'Other Expenses');
$sheet->setCellValue('F1', 'Item name');
$sheet->setCellValue('G1', 'Quantity');
$sheet->setCellValue('H1', 'price');
$sheet->setCellValue('I1', 'qty*prc');
$sheet->setCellValue('J1', 'Final Amount');

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);

// Make the header row bold
$headerStyleArray = [
    'font' => [
        'bold' => true,
    ],
];
$sheet->getStyle('A1:J1')->applyFromArray($headerStyleArray);
//$sheet->getStyle('A6:D6')->applyFromArray($headerStyleArray);
// Fill in the data starting from row 7
	$row = 2;$itemrow = '';
	foreach ($itemdata as $item) {
		$sheet->setCellValue('A' . $row, $item['clients_name']);
		$sheet->setCellValue('B' . $row, $item['billnumber']);
		$sheet->setCellValue('C' . $row, $item['collection_center']);
		$sheet->setCellValue('D' . $row, $item['transportation']);
		$sheet->setCellValue('E' . $row, $item['otherexpenses']);
		$sheet->setCellValue('J' . $row, $item['totamt']);
		
		
					$itemrow = $row;
					if(isset($item['itemlist']))
					 {  
							foreach($item['itemlist'] as $keyy=>$valy)
							{
									$sheet->setCellValue('F' . $itemrow, $valy['item_name']);
									$sheet->setCellValue('G' . $itemrow, $valy['quantity']);
									$sheet->setCellValue('H' . $itemrow, $valy['price']);
									$sheet->setCellValue('I' . $itemrow, $valy['price']*$valy['quantity']);
									//$sheet->setCellValue('J' . $itemrow, $valy['totamt']);
									$itemrow++;
							}
						
					  }
		$row = $itemrow;
		$row++;
	}



// Set headers for the file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="gsb_datesearch.xlsx"');
header('Cache-Control: max-age=0');
//
////// Create a writer instance and save to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>

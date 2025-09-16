<?php
include'account.php';
$account =  new account();

if(isset($_GET['item_name'])) 
{
	   $itemname = $_GET['item_name'];
	   $res = $account->get_item_codes($itemname);
	  // print_r($res);
	  echo json_encode($res);
}


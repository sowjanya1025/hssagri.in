<?php
include'account.php';
$account =  new account();

if(isset($_POST['account_id'])) 
{
	// Fetch purchase items
	$items = $account->get_purchase_items_forrecovery();
	//print_r($items);

	echo json_encode($items);
}


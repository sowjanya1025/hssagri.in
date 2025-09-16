<?php
include'account.php';
$account =  new account();

if(isset($_GET['term'])) 
{
	   $term = $_GET['term'];
	   $res = $account->get_item_names($term);
	  echo json_encode($res);
}


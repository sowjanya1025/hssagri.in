<?php
include'account.php';
$account =  new account();

if(isset($_POST['vendorid'])) 
{
	  // echo "saketh";
	   $id = $_POST['vendorid'];
	   $res = $account->getFarmerOrSupplier_list($id);
	   echo $res;
	   
}

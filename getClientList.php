<?php
include'account.php';
$account =  new account();

if(isset($_POST['clientid'])) 
{
	  // echo "saketh";
	   $id = $_POST['clientid'];
	   $res = $account->getClientList($id);
	   echo $res;
	   
}

<?php
include'account.php';
$account =  new account();

if(isset($_POST['aptid'])) 
{
       // $mailid =  $_POST['mail'];
        //echo "data is valied";
        //$validate_email = $account->checkemail_availability($mailid);
       // echo "data is valied";
        $aptid = $_POST['aptid'];
        $acctid = $_POST['acctid'];
        $res = $account->getDataForSales($acctid,$aptid);

        echo $res;
}

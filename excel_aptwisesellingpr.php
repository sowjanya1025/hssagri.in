<?php
session_start();

include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 
$aptid = $_GET['aptid'];
$date = $_GET['date'];

$items = $account->fetch_dataforexcel($aptid,$date,"excel");

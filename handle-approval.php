<?php
include'account.php';
$account =  new account();

// Get action and user ID from query string
    $action = $_GET['action'];
    $user_id = $_GET['user_id'];

    // Update database based on action
    if ($action === 'approve') {
		$status = 1;
       $account->approvalstatus_GoodsReceive_note($status,$user_id);
    } elseif ($action === 'reject') {
		$status = 0;
        $account->approvalstatus_GoodsReceive_note($status,$user_id);
    } else {
        echo "Invalid action.";
        exit;
    }
	
	echo "Action processed successfully.";
	exit;
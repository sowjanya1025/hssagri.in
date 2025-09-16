<?php
include'account.php';
$account =  new account();

if(isset($_POST['mail'])) 
{
        $mailid =  $_POST['mail'];
        //echo "data is valied";
        $validate_email = $account->checkemail_availability($mailid);
       // echo "data is valied";
        echo $validate_email;
}
//return "all is well";


/*git branch bugFix
git checkout bugFix
git commit
git rebase main
git commit 
git checkout bugFix
git rebase main

git branch -f main HEAD~3


git chekout main^
git checkout c3
git checkout Head^
git checkout Head^
git checkout Head^


git checkout HEAD~4
git branch -f main HEAD~3
moves (by force) the main branch to three parents behind HEAD.

*/
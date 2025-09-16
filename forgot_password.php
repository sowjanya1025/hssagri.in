<?php
//session_start();
include'account.php';
$account =  new account();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if(!empty($_POST))
{
            $femail = isset($_POST['email'])? $_POST['email'] : '';
			$check = $account->checkemail_availability($femail);
			//echo $check;
			//exit;
			if($check == 1)
			{
				// Generate a unique reset token
				$token = bin2hex(random_bytes(50));
		
				// Set token expiration time (e.g., 1 hour)
				//$expires = time() + 3600;
				date_default_timezone_set('Asia/Kolkata');
				$expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expires in 1 hour
		
				// Insert token into the database
				$account->update_resetToken($token,$expires,$femail);
// Include the PHPMailer classes (adjust the path if necessary)
require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server settings

$mail->SMTPDebug = 0; // Enable detailed debug output
$mail->isSMTP(); // Use SMTP
$mail->Host = 'mail.hssagri.in'; // Set the SMTP server to send through
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'admin@hssagri.in'; // SMTP username
$mail->Password = '}RNxK^pq$NNc';  // SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;


    // Recipients
    $mail->setFrom('admin@hssagri.in', 'Hssagri');
	$mail->addAddress($femail, 'Hssagri'); // Add a recipient
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Password Reset Request from  Hssagri.in';
   // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	// Buttons with links to approval/rejection script
					//$resetUrl = "http://localhost/greenbasket/reset_password.php?token=" . $token;
					$resetUrl = "https://hssagri.in/reset_password.php?token=" . $token;
	
					$bodyContent = "<p>Hi,</p>
					<p><b>Warm greetings from Hssagri.in!</b></p>

					<p>Click the following link to reset your password:</p> ".$resetUrl."<br/>
   
					<p>Best Regards,<br/>Hssagri.in Team</p>";

				//	$mail->Subject = 'Password reset link from MySportsArena';
					$mail->Body    = $bodyContent;

    // Send the email
    $mail->send();
   // echo 'Message has been sent';
} catch (Exception $e) {
    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
//exit;
	////////////////////end php mail //////////
				
				header("Location:forgot_password.php?act=1");
				exit;

			}elseif(($check == 0))
			{
				//echo "No account found with that email address.";
				header("Location:forgot_password.php?act=2");
				exit;
			}else
			{
				header("Location:forgot_password.php?act=3");
				exit;
			}

}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn and Register page</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style>  body {
    padding-top: 15px;
    font-size: 12px
    margin-bottom: 15px;
    background-color: grey;
  }
  .main {
    max-width: 320px;
    margin: 0 auto;
  }
  .login-or {
    position: relative;
    font-size: 18px;
    color: #aaa;
    margin-top: 10px;
            margin-bottom: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
  }
  .span-or {
    display: block;
    position: absolute;
    left: 50%;
    top: -2px;
    margin-left: -25px;
    background-color: #fff;
    width: 50px;
    text-align: center;
  }
  .hr-or {
    background-color: #cdcdcd;
    height: 1px;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }
  h3 {
    text-align: center;
    line-height: 300%;
  }
  footer{
      margin: 15px;
  }
  .redtext{ color: red;} 
  .greentext{ color: green;} 
  </style>
</head>
<body>

<div class="container">
	<div class="col-md-12">
	    <div class="card">
	        <div class="card-body">
			
	              <div class="row">
		<div class="col-md-4">
		     <h3>Forgot Password!!</h3>
			 	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:green;">A password reset link has been sent to your email address.</span></b></div>
	  <?php }
	  	 elseif($_GET['act']==2)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:red;"> No account found with this email address. Invalid Email Address</span></b></div>
	  <?php }
	  elseif($_GET['act']==2)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:red;">Invalid Email Address</span></b></div>
	  <?php }
	  
	  
	  
	   } ?>
      <form id="login-form" action="" role="form" method="post">
      <input type="hidden" name="formtype" value="loginform">
        <div class="form-group">
          <label for="email">Please enter you Registered Email Address</label>
          <input type="text" class="form-control" id="email" name="email" required>
		  <p id="loginmail_err"></p>
        </div>
        <button type="submit" class="btn btn btn-primary" id="loginsubmitbtn">
          Submit
        </button>
		<button type="button" class="btn btn btn-primary" id="loginsubmitbtn" onClick="window.location.href='http://localhost/greenbasket/index.php';">
          Back
        </button>
      </form>
		</div>
		<div class="col-md-8">
		    <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100" src="fruitsimg.jpg" alt="First slide">
    </div>
  </div>
</div>
		</div>
	</div>
	        </div>
	    </div>
	</div>
</div>

<footer></footer>

    
</body>
</html>
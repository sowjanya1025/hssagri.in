<?php 
error_reporting(1);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Include the PHPMailer classes (adjust the path if necessary)
require 'vendor/autoload.php';

$mail = new PHPMailer(true);
//echo "here"; exit;

try {
    // Server settings

$mail->SMTPDebug = 0; // Enable detailed debug output
$mail->isSMTP(); // Use SMTP
$mail->Host = 'mail.hssagri.in'; // Set the SMTP server to send through
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'admin@hssagri.in'; // SMTP username
$mail->Password = '}RNxK^pq$NNc';  // SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;



    // Recipients
    $mail->setFrom('admin@hssagri.in', 'Hssagri');
    $mail->addAddress('swjnambati@gmail.com', 'Hssagri'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Notification from Hssagri.in';

	
	
					$bodyContent = "<p>Hi,</p>";

				//	$mail->Subject = 'Password reset link from MySportsArena';
					$mail->Body    = $bodyContent;

    // Send the email
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
//exit;
	////////////////////end php mail //////////  ?>
<?php
error_reporting(0);
session_start();
include'account.php';
$account =  new account();


if(!empty($_POST))
{
    //print_r($_POST);
    if(isset($_POST['formtype']))
    {
        if($_POST['formtype']=='loginform')
        {
            $lemail = isset($_POST['loginemail'])? $_POST['loginemail'] : '';
            $lpassword = isset($_POST['loginpassword'])? md5($_POST['loginpassword']) : '';
            $login_response = $account->login($lemail,$lpassword);
			//print_r($login_response);
			//exit;
			if ($login_response['error'] === false)
			{
				//header("Location:design4.php?login=success");
				$account->setSession($login_response['accountID'],$login_response['email']);
				header("Location:home.php");
				exit;
			}else
			{
				header("Location:dcmanagement.php?loginerr=fail");
				exit;
			}
			

        }
        elseif($_POST['formtype']=='registerform')
        {
            $email = isset($_POST['email'])? $_POST['email'] : '';
            $password = isset($_POST['password'])? md5($_POST['password']) : '';
            $account->signup($username,$password,$mobile,$email);
            header("Location:dcmanagement.php?register=success");
            exit;
        }
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
		     <h3>Please Log In, or <a href="#">Sign Up</a></h3>
			 
      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
          <a href="#" class="btn btn-lg btn-primary btn-block active" id="login-form-link">Log In</a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
          <a href="#" class="btn btn-lg btn-info btn-block" id="register-form-link">Sign Up</a>
        </div>
      </div>
      <div class="login-or">
        <hr class="hr-or">
        <span class="span-or">or</span>
      </div>
	  <?php if(!empty($_GET['register']))
	   {
	  	 if($_GET['register']=='success')
		 {			 ?>
	  		<div class="text-center greentext" style="border:0px solid green"><b>Registered Successfully..pls login</b></div>
	  <?php } } ?>
	  <?php if(!empty($_GET['loginerr']))
	   {
	  	 if($_GET['loginerr']=='fail')
		 {			 ?>
	  		<div class="text-center redtext" style="border:0px solid red"><b>Invalid Username/Password</b></div>
	  <?php } } ?>
      <form id="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" role="form" style="display:block;"  method="post">
      <input type="hidden" name="formtype" value="loginform">
        <div class="form-group">
          <label for="loginemail">Email Address</label>
          <input type="text" class="form-control" id="loginemail" name="loginemail">
		  <p id="loginmail_err"></p>
        </div>
        <div class="form-group">
          <a class="pull-right" href="forgot_password.php">Forgot password?</a>
          <label for="loginpassword">Password</label>
          <input type="password" class="form-control" id="loginpassword" name="loginpassword">
		  <p id="loginpass_err"></p>
        </div>
        <button type="button" class="btn btn btn-primary" id="loginsubmitbtn">
          Log In
        </button>
        <div class="form-group"><p class="text-center">Not a member?<a href="#" id="register-form-link1">Register</a></p></div>
      </form>
      <form   id="registerform" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" role="form" style="display:none;" method="post" >
        <input type="hidden" name="formtype" value="registerform">
        <div class="form-group">
          <label for="inputUserEmail">Email Address</label>
          <input type="text" class="form-control" id="inputUserEmail" name="email" required>
          <p class="red-text" id="email_err"></p>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
          <p id="pass_err"></p>
        </div>
        <div class="form-group">
          <label for="confirmpassword">Confirm Password</label>
          <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
          <p id="cfmpass_err"></p>
        </div>
        <button type="button" class="btn btn btn-primary" id="btnSubmit">
          Sign Up
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
<script>
    //onSubmit="return validate()"
// function validate(){
// 	var pass=document.getElementById("password").value;
// 	var conf_pass=document.getElementById("confirmpassword").value;
// 	if(conf_pass!=pass){
// 		document.getElementById("pass_err").innerHTML="Password doesnot match";
// 		return false;
// 	}
// }
$(function()
{

$('#login-form-link').click(function(e) {
    $("#login-form").delay(100).fadeIn(100);
     $("#registerform").fadeOut(100);
    $('#register-form-link').removeClass('active');
    $(this).addClass('active');
    e.preventDefault();
});
$('#register-form-link').click(function(e) {
    //alert("here");
    $("#registerform").delay(100).fadeIn(100);
     $("#login-form").fadeOut(100);
    $('#login-form-link').removeClass('active');
    $(this).addClass('active');
    e.preventDefault();
});
$('#register-form-link1').click(function(e) {
    //alert("here1");
    $("#registerform").delay(100).fadeIn(100);
     $("#login-form").fadeOut(100);
    $('#login-form-link').removeClass('active');
    $('#register-form-link').addClass('active');
    e.preventDefault();
});


$("#btnSubmit").click(function(e) {
                var emailvalidator;
                var pswdvalidator;
                var password = $("#password").val();
                var confirmPassword = $("#confirmpassword").val();
                var email =  $('#inputUserEmail').val();

                if(email=="")
                {
                  $('#email_err').html('<span class="redtext">Email is required</span>');
                  emailvalidator = 1;
                }
                if(password=="")
                {
                  $('#pass_err').html('<span class="redtext">Password is required</span>');
                  pswdvalidator = 1;
                }else
                {
                  $('#pass_err').html('');
                  pswdvalidator = 0;
                }
                if(confirmPassword=="")
                {
                  $('#cfmpass_err').html('<span class="redtext">confirmPassword is required</span>');
                  pswdvalidator = 1;
                }

                if(password!="" && confirmPassword!="")
                {
                    if (password != confirmPassword) {
                      $('#pass_err').html('');
                      $('#cfmpass_err').html('');
                      $('#cfmpass_err').html('<span class="redtext">Passwords do not match</span>');
                      pswdvalidator = 1;
                    }else
                    {
                        $('#cfmpass_err').html('');
                        pswdvalidator = 0;
                    }
              }
                //return true;
            
            //alert(email);
            if(email!="")
			{
				$.ajax({
				type:"post",
				url:"checkemail_availability.php",
				data:{mail:email},
				success:function(response)
				{ 
				   // alert(response);
						$('#email_err').html(response);
						if (response == '1')
					{
						//alert("mail exists");
						$('#email_err').html('<span class="redtext">Email already exists</span>');
						emailvalidator = 1;
						e.preventDefault(); 
		
					}else if(response == '0')
					{
					  $('#email_err').html('');
						emailvalidator = 0;
						//alert(emailvalidator);
						//alert(pswdvalidator);
						if(emailvalidator === 0 && pswdvalidator === 0 )
						 {
							$('#registerform').submit();
						 }
					 }
                 }
		             }); //end ajax
                }
            }); // clck function
			
			
 $('#loginsubmitbtn').click(function(e)
  {
  	//alert("here in login function");
 		var loginmail_id = $('#loginemail').val();
		var loginpswd_id = $('#loginpassword').val();
		var loginpwsdvalidator ;
		var loginmailvalidator;
			if(loginmail_id=="")
			{
			  $('#loginmail_err').html('<span class="redtext">Email is required</span>');
			  loginmailvalidator = 1;
			}else
			{
			  $('#loginmail_err').html('');
			  loginmailvalidator = 0;
			} 
			if(loginpswd_id=="")
			{
			  $('#loginpass_err').html('<span class="redtext">Password is required</span>');
			  loginpwsdvalidator = 1;
			}else
			{
			  $('#loginpass_err').html('');
			  loginpwsdvalidator = 0;
			} 
				// submit the form only when loginpwsdvalidator and  loginmailvalidator are 0
				if(loginmailvalidator === 0 && loginpwsdvalidator === 0 )
				 {
					$('#login-form').submit();
				 } 
 
 
  }); // click function 


}); // end
</script>
    
</body>
</html>
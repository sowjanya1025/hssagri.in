<?php
include'account.php';
$account =  new account();


if(!empty($_POST))
{
    //print_r($_POST);
    if(isset($_POST['formtype']))
    {
        if($_POST['formtype']=='loginform')
        {

        }
        elseif($_POST['formtype']=='registerform')
        {
            $email = isset($_POST['email'])? $_POST['email'] : '';
            $password = isset($_POST['pswd'])? md5($_POST['pswd']) : '';
            $account->signup($username,$password,$mobile,$email);
            header("Location:signup2.php");
            exit;
        }
    }
  
    
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In/Register page</title>
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

      <form id="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" role="form" style="display:block;"  method="post">
      <input type="hidden" name="formtype" value="loginform">
        <div class="form-group">
          <label for="inputUsernameEmail">Email Address</label>
          <input type="text" class="form-control" id="inputUsernameEmail">
        </div>
        <div class="form-group">
          <a class="pull-right" href="#">Forgot password?</a>
          <label for="inputPassword">Password</label>
          <input type="password" class="form-control" id="inputPassword">
        </div>
        <button type="submit" class="btn btn btn-primary">
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
          <input type="text" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="confirmpassword">Confirm Password</label>
          <input type="text" class="form-control" id="confirmpassword" name="confirmpassword" required>
          <p id="pass_err"></p>
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
                var pswdvalidator = 0;
                var password = $("#password").val();
                var confirmPassword = $("#confirmpassword").val();
                if (password != confirmPassword) {
                    //alert("Passwords do not match.");
                    $('#pass_err').html('<span class="redtext">Passwords do not match</span>');
                    pswdvalidator = 1;
                    //return false;
                }else
                {
                    $('#pass_err').html('');
                    pswdvalidator = 0;

                }
                //return true;
            var email =  $('#inputUserEmail').val();
            //alert(email);
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
              //alert("reponse = 0");  
              $('#email_err').html('');
                emailvalidator = 0;
               // alert(emailvalidator);
               // alert(pswdvalidator);
                if(emailvalidator === 0 && pswdvalidator === 0 )
            {
  	          //alert("submitform");      
              $('#registerform').submit();
            }
            }
           // alert(emailvalidator);
           
            }
		    });
           // alert("validerr="+emailvalidator)
           // if(emailvalidator === 0 && pswdvalidator === 0 )
           // {
  	        //        ('#register-form').submit();
            //}else
           // {
  		      //      e.preventDefault(); // prevent from submitting
            //}
            });


});
</script>
    
</body>
</html>
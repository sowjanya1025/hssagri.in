<?php
session_start();
include'account.php';
$account =  new account();

// reset_password.php
if (isset($_GET['token'])) {
    $token = $_GET['token'];
	}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password)
	 {
		$account->update_resetPassword($token,$password);
		header("Location:reset_password.php?act=1");
		exit;
	} else {
		header("Location:reset_password.php?act=2");
		exit;
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
		     <h3>Reset Password!!</h3>
		<?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:green;">Password Updated Successfully..<a href="index.php">Click Here</a> to return to Index Page</span></b></div>
	  <?php }
	  	 elseif($_GET['act']==2)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:red;">Password and Confirm Password Mismatch</span></b></div>
	  <?php }
	   } ?>

			 <?php
//if (isset($_GET['token'])) {
   // $token = $_GET['token'];
    ?>

      <form id="login-form" action="" role="form" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group">
          <label for="password">New Password:</label>
          <input type="text" class="form-control" id="password" name="password" required>
		  <p id="loginmail_err"></p>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password:</label>
          <input type="text" class="form-control" id="confirm_password" name="confirm_password" required>
		  <p id="loginmail_err"></p>
        </div>
        <button type="submit" class="btn btn btn-primary" id="loginsubmitbtn">
          Reset Password
        </button>
      </form>
	  <?php
//} else {}
?>
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
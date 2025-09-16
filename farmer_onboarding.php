<?php
session_start();
//print_r($_SESSION);
include'account.php';
$account =  new account();

$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}


if(!empty($_POST))
{
	if(isset($_POST['farmerboarding'])=='farmerboarding')
	{
		$fname = isset($_POST['fname'])? $_POST['fname'] : NULL;
		$contact = isset($_POST['contact'])? $_POST['contact'] : NULL;
		$email = isset($_POST['email'])? $_POST['email'] : NULL;
		$pan = isset($_POST['pan'])? $_POST['pan'] : NULL;
		$adhar = isset($_POST['adhar'])? $_POST['adhar'] : NULL;
		$image=isset($_FILES['image']['name']) ? $_FILES['image']['name'] : NULL;
		
		$acctname = isset($_POST['acctname'])? $_POST['acctname'] : NULL;
		$acctnumber = isset($_POST['acctnumber'])? $_POST['acctnumber'] : NULL;
		$ifsccode = isset($_POST['ifsccode'])? $_POST['ifsccode'] : NULL;
		$branchname = isset($_POST['branchname'])? $_POST['branchname'] : NULL;
		$cancelcheq = isset($_FILES['cancelcheq']['name']) ? $_FILES['cancelcheq']['name'] : NULL;
		
		// image upload
		$newfilename="";
		if($image !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif");
			$extension = pathinfo($image, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $image);
				$newfilename = 'fr_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["image"]["tmp_name"],"images/" . $newfilename);
			}
		}
		
		// end image upload///
		
		//$insertID  = $account->setfarmer_Onboarding($fname,$contact,$email,$pan,$adhar,$newfilename);  // insert into db
		//$lastinsert = $insertID['insert_last_id'];
		
		// kyc multiple upload doc //
	// Checks if user sent an empty form 

   $kycFiles = [];
	if(!empty(array_filter($_FILES['kyc']['name'])))
	 {
		// Loop through each file in files[] array
		$kycfilename = "";
		foreach ($_FILES['kyc']['tmp_name'] as $key => $value)
			 {
						$fileName = basename($_FILES['kyc']['name'][$key]);
						$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
						$extension = pathinfo($_FILES["kyc"]["name"][$key], PATHINFO_EXTENSION);
						if(in_array($extension, $allowedExts))
						{
							$temp = explode(".", $_FILES["kyc"]["name"][$key]);
							$kycfilename = 'fr_kyc'.$accountId.'_'.rand().'.'.end($temp);
							move_uploaded_file($_FILES["kyc"]["tmp_name"][$key],"images/" . $kycfilename);
							$kycFiles[] = $kycfilename;
							//$account->setfarmer_Onboarding_kyc($kycfilename,$lastinsert,$accountId);  // insert into db
					   }	 
			}
	  } 
	 $kycFilesSerialized = serialize($kycFiles);
	 ////////////////// cancel cheque ////////////
		$cancelcheqnewfilename="";
		if($cancelcheq !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
			$extension = pathinfo($cancelcheq, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $cancelcheq);
				$cancelcheqnewfilename = 'fr_calche_'.date("dmyhis").'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["cancelcheq"]["tmp_name"],"images/" . $cancelcheqnewfilename);
			}
		}
	 //////////////////end cancel cheque //////////
	 $account->setfarmer_Onboarding($fname,$contact,$email,$pan,$adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename,$accountId);  // insert into db
	 header("Location:farmer_onboarding.php?act=1");
	 
	 
	}
}



?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <title>Farmer Onboarding form</title>
  <style>
    body { background-color: #fafafa;   .redtext{ color: red; .greentext{ color: green;} 
 }
  </style>
</head>

<body>
  <!-- start wrapper -->
  <div class="wrapper">
    <?php require_once('side_bar.php'); ?>
    <div id="content">
      <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
          <button type="button" id="sidebarCollapse" class="btn btn-dark">
            <i class="fas fa-bars"></i><span> Toggle Sidebar</span>
          </button>
        </div>
      </nav>
      <br><br>
      <h2>Farmer Onboarding Form</h2>
	  <button class="btn btn-dark"><a href="fview.php">View Farmers List</a></button>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Registered Successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="#" id="farmerboardingform" method="post" enctype="multipart/form-data">
		<input type="hidden" name="farmerboarding" value="farmerboarding">
            <div class="form-group">
                <label for="fname" class="control-label required">Name:</label>
                <input type="text" class="form-control" id="fname"
                    placeholder="Enter Name" name="fname" >
					<p id="name_err"></p>
            </div>
			<div class="form-group">
                <label for="contact" class="control-label required">Mobile No:</label>
                <input type="text" class="form-control" id="contact"
                    placeholder="Enter Contact Number" name="contact" maxlength="10" >
					<p id="contact_err"></p>
            </div>
            <div class="form-group">
                <label for="email" >Email Id:</label>
                <input type="email" class="form-control" id="email"
                    placeholder="Enter Email Id" name="email" >
					<p id="email_err"></p>
            </div>
			<div class="form-group">
                <label for="pan">PAN details:</label>
                <input type="text" class="form-control" id="pan"
                    placeholder="Enter PAN Id" name="pan" >
					<p id="pan_err"></p>
            </div>
			<div class="form-group">
                <label for="adhar" class="control-label required">Adhar details:</label>
                <input type="text" class="form-control" id="adhar"
                    placeholder="Enter Adhar Id" name="adhar" >
					<p id="adhar_err"></p>
            </div>  
			<div class="form-group">
                <label for="image">Image Upload:</label>
                <input type="file" class="form-control" id="image"
                    placeholder="Enter Adhar Id" name="image" >
					<p id="image_err"></p>
            </div>          
			<div class="form-group">
                <label for="kyc">KYC documents:</label>
                <input type="file" class="form-control" id="kyc"
                    placeholder="Enter Adhar Id" name="kyc[]" multiple>
					<p id="kyc_err"></p>
					<ul id="fileNames"></ul>
            </div>
			<div class="form-group" style="border:0px solid #999999;">
			  <div><h6><strong>Bank Details:</strong></h6></div>
  <div class="form-group row" >
    <label for="acctname" class="col-sm-3 col-form-label">Account Holder Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctname" name="acctname">
    </div>
  </div>
  <div class="form-group row">
    <label for="acctnumber" class="col-sm-3 col-form-label">Account Number:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctnumber" name="acctnumber">
    </div>
  </div>
<div class="form-group row">
    <label for="ifsccode" class="col-sm-3 col-form-label">IFSC Code:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="ifsccode" name="ifsccode">
    </div>
  </div>
<div class="form-group row">
    <label for="branchname" class="col-sm-3 col-form-label">Branch Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="branchname" name="branchname">
    </div>
  </div>  <div class="form-group row">
    <label for="cancelcheq" class="col-sm-3 col-form-label">Cancel Cheque:</label>
    <div class="col-sm-9">
      <input type="file" class="form-control" id="cancelcheq" name="cancelcheq">
    </div>
  </div>
			</div>          
		<input type="button" id="submitbtn" value="Submit">
        </form>
    </div>
    </div>

  </div>
<?php require_once('footer.php'); ?>
<!--https://www.geeksforgeeks.org/form-validation-using-jquery/--> <!--// jquery validation code download-->
  <script>
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
    });
	
	$(document).ready(function() {
	$('#submitbtn').click(function(e)
	{
	
		let f_name = $('#fname').val();
		let contact = $('#contact').val();
		let email = $('#email').val();
		let pan = $('#pan').val();
		let adhar = $('#adhar').val();
		let image = $('#image').val();
		let kyc = $('#kyc').val();
		//alert(f_name);
		let usernameError = true; 
		let contactError = true; 
		let adharError = true; 
		
		if(f_name.length == "")
		{
			$('#name_err').html('<span class="redtext">Name is required</span>');
			usernameError = false; 
			//return false;
		}
		if(contact.length == "")
		{
			$('#contact_err').html('<span class="redtext">Contact number is required</span>');
			contactError = false; 
			//return false;
		}
//		if(email.length == "")
//		{
//			$('#email_err').html('<span class="redtext">Email is required</span>');
//			EmailError = false; 
//			//return false;
//		}
//		if(pan.length == "")
//		{
//			$('#pan_err').html('<span class="redtext">PAN number is required</span>');
//			PanError = false; 
//			//return false;
//		}
		if(adhar.length == "")
		{
			$('#adhar_err').html('<span class="redtext">Adhar number is required</span>');
			adharError = false; 
			//return false;
		}
//		if(image.length == "")
//		{
//			$('#image_err').html('<span class="redtext">Image is required</span>');
//			imageError = false; 
//			//return false;
//		}
//		if(kyc.length == "")
//		{
//			$('#kyc_err').html('<span class="redtext">KYC is required</span>');
//			kycError = false; 
//			//return false;
//		}




		//validateUsername();
		if( usernameError == true && contactError == true && adharError == true  )
		{
			$('#farmerboardingform').submit();
		}
		else
		{
			return false;
		}
	
	});
	
	 $('#kyc').on('change',function()
	 {
		const fileNamesList = document.getElementById('fileNames');
        fileNamesList.innerHTML = ''; // Clear the list before adding new items
        const files = event.target.files;

        // Loop through the selected files and display their names
        for (let i = 0; i < files.length; i++) {
            const li = document.createElement('li');
            li.textContent = files[i].name;
            fileNamesList.appendChild(li);
        }
	
	   });

});

  </script>
</body>
</html>

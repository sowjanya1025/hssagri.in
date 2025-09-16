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
	if(isset($_POST['companyboarding'])=='companyboarding')
	{
		$c_name = isset($_POST['cname'])? $_POST['cname'] : NULL;
		$c_pan = isset($_POST['pan'])? $_POST['pan'] : NULL;
		$c_reg = isset($_POST['reg'])? $_POST['reg'] : NULL;
		$c_gst= isset($_POST['gst'])? $_POST['gst'] : NULL;
		$c_adhar = isset($_POST['adhar'])? $_POST['adhar'] : NULL;
		$c_cheque=isset($_FILES['cheque']['name']) ? $_FILES['cheque']['name'] : NULL;
		//$kyc = isset($_POST['kyc'])? $_POST['kyc'] : NULL;
		print_r($_POST);
		$acctname = isset($_POST['acctname'])? $_POST['acctname'] : NULL;
		$acctnumber = isset($_POST['acctnumber'])? $_POST['acctnumber'] : NULL;
		$ifsccode = isset($_POST['ifsccode'])? $_POST['ifsccode'] : NULL;
		$branchname = isset($_POST['branchname'])? $_POST['branchname'] : NULL;
		//$cancelcheq = isset($_FILES['cancelcheq']['name']) ? $_FILES['cancelcheq']['name'] : NULL;
		// image upload
		$newfilename="";
		if($c_cheque !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
			$extension = pathinfo($c_cheque, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $c_cheque);
				$newfilename = 'cm_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["cheque"]["tmp_name"],"images/" . $newfilename);
			}
		}
		
		// end image upload///
		
		
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
							$kycfilename = 'cm_kyc'.$accountId.'_'.rand().'.'.end($temp);
							move_uploaded_file($_FILES["kyc"]["tmp_name"][$key],"images/" . $kycfilename);
							$kycFiles[] = $kycfilename;
							//$account->setfarmer_Onboarding_kyc($kycfilename,$lastinsert,$accountId);  // insert into db
					   }	 
			}
	  } 
	 $kycFilesSerialized = serialize($kycFiles);
	 $insertID  = $account->setCompany_Onboarding($c_name,$c_pan,$c_reg,$c_gst,$c_adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$accountId);  // insert into db
	 header("Location:company_onboarding.php?act=1");
	}
	
}


?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <title>Supplier Onboarding form</title>
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
      <h2>Supplier Onboarding Form</h2>
	   <button class="btn btn-dark"><a href="cview.php">View Suppliers List</a></button>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Registered Successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="#" id="companyboardingform" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="companyboarding" value="companyboarding">
            <div class="form-group">
                <label for="fname" class="control-label required">Name:</label>
                <input type="text" class="form-control" id="cname"
                    placeholder="Enter Name" name="cname" >
					<p id="name_err"></p>
            </div>
			<div class="form-group">
                <label for="pan" class="control-label required">PAN details:</label>
                <input type="text" class="form-control" id="pan"
                    placeholder="Enter PAN Id" name="pan" >
					<p id="pan_err"></p>
            </div>
						<div class="form-group">
                <label for="pan" class="control-label required">Registration details:</label>
                <input type="text" class="form-control" id="reg"
                    placeholder="Enter Registration details" name="reg" >
					<p id="reg_err"></p>
            </div>

			<div class="form-group">
                <label for="pan" class="control-label required">GST:</label>
                <input type="text" class="form-control" id="gst"
                    placeholder="Enter GST" name="gst" >
					<p id="gst_err"></p>
            </div>

			<div class="form-group">
                <label for="adhar" class="control-label required">Adhar details:</label>
                <input type="text" class="form-control" id="adhar"
                    placeholder="Enter Adhar Id" name="adhar" >
					<p id="adhar_err"></p>
            </div>  
				<!--<div class="form-group">
					<label for="image" >Cancel Cheque Image Upload:</label>
					<input type="file" class="form-control" id="cheque"
						 name="cheque" >
						<p id="cheque_err"></p>
				</div>     -->     
			<div class="form-group">
                <label for="kyc" >KYC documents:</label>
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
      <input type="file" class="form-control" id="cheque" name="cheque">
    </div>
  </div>
			</div>        
		<input type="button" id="submitbtn" value="Submit">
        </form>
    </div>
    </div>

  </div>
<?php require_once('footer.php'); ?>
  <script>
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
    });
	
	$(document).ready(function() {
	$('#submitbtn').click(function(e)
	{
	
		let c_name = $('#cname').val();
		let pan = $('#pan').val();
		let reg = $('#reg').val();
		let gst = $('#gst').val();
		let adhar = $('#adhar').val();
		let cheque = $('#cheque').val();
		let kyc = $('#kyc').val();
		//alert(f_name);
		let usernameError = true; 
		let panError = true; 
		let regError = true; 
		let gstError = true; 
		let adharError = true; 
		let chequeError = true; 
		let kycError = true; 
		
		
		if(c_name.length == "")
		{
			$('#name_err').html('<span class="redtext">Name is required</span>');
			usernameError = false; 
			//return false;
		}
		if(pan.length == "")
		{
			$('#pan_err').html('<span class="redtext">PAN number is required</span>');
			panError = false; 
			//return false;
		}
		if(reg.length == "")
		{
			$('#reg_err').html('<span class="redtext">Registration details are required</span>');
			regError = false; 
			//return false;
		}
		if(gst.length == "")
		{
			$('#gst_err').html('<span class="redtext">GST details are required</span>');
			gstError = false; 
			//return false;
		}
		if(adhar.length == "")
		{
			$('#adhar_err').html('<span class="redtext">Adhar number is required</span>');
			adharError = false; 
			//return false;
		}
		/*if(cheque.length == "")
		{
			$('#cheque_err').html('<span class="redtext">cancel cheque is required</span>');
			chequeError = false; 
			//return false;
		}

		if(kyc.length == "")
		{
			$('#kyc_err').html('<span class="redtext">KYC is required</span>');
			kycError = false; 
			//return false;
		}*/

		//validateUsername();
		//if( usernameError == true && panError == true && regError == true &&  gstError == true && adharError == true && chequeError == true && kycError == true)
		if( usernameError == true && panError == true && regError == true &&  gstError == true && adharError == true)
		{
			$('#companyboardingform').submit();
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

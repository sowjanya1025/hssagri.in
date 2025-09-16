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
	if(isset($_POST['clientboardingform'])=='clientboardingform')
	{
		$clienttype = isset($_POST['clienttype'])? $_POST['clienttype'] : NULL;
		$clname = isset($_POST['clname'])? $_POST['clname'] : NULL;
		$contact = isset($_POST['contact'])? $_POST['contact'] : NULL;
		$email = isset($_POST['email'])? $_POST['email'] : NULL;
		$Agreementcopy=isset($_FILES['Agreementcopy']['name']) ? $_FILES['Agreementcopy']['name'] : NULL;
		$acctname = isset($_POST['acctname'])? $_POST['acctname'] : NULL;
		$acctnumber = isset($_POST['acctnumber'])? $_POST['acctnumber'] : NULL;
		$ifsccode = isset($_POST['ifsccode'])? $_POST['ifsccode'] : NULL;
		$branchname = isset($_POST['branchname'])? $_POST['branchname'] : NULL;
		$cancelcheq = isset($_FILES['cancelcheq']['name']) ? $_FILES['cancelcheq']['name'] : NULL;
		
		// Agreementcopy upload
		$agreementcopynewfilename="";
		if($Agreementcopy !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
			$extension = pathinfo($Agreementcopy, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $Agreementcopy);
				$agreementcopynewfilename = 'mt_agrcopy_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["Agreementcopy"]["tmp_name"],"images/" . $agreementcopynewfilename);
			}
		}
		// end Agreementcopy upload///
		
		//$insertID  = $account->setfarmer_Onboarding($fname,$contact,$email,$pan,$adhar,$newfilename);  // insert into db
		//$lastinsert = $insertID['insert_last_id'];
		
		// kyc multiple upload doc //
	// Checks if user sent an empty form 
	if(!empty(array_filter($_FILES['kyc']['name'])))
	 {
		// Loop through each file in files[] array
		$kycfilename = "";
		$kycFiles = [];
		foreach ($_FILES['kyc']['tmp_name'] as $key => $value)
			 {
						$fileName = basename($_FILES['kyc']['name'][$key]);
						$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
						$extension = pathinfo($_FILES["kyc"]["name"][$key], PATHINFO_EXTENSION);
						if(in_array($extension, $allowedExts))
						{
							$temp = explode(".", $_FILES["kyc"]["name"][$key]);
							$kycfilename = 'mt_kyc'.$accountId.'_'.rand().'.'.end($temp);
							move_uploaded_file($_FILES["kyc"]["tmp_name"][$key],"images/" . $kycfilename);
							$kycFiles[] = $kycfilename;
							//$account->setfarmer_Onboarding_kyc($kycfilename,$lastinsert,$accountId);  // insert into db
					   }	 
			}
	  } 
	 $kycFilesSerialized = serialize($kycFiles);
	 // end kyc ////////////
	 
	 ////////////////// cancel cheque ////////////
		$cancelcheqnewfilename="";
		if($cancelcheq !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
			$extension = pathinfo($Agreementcopy, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $cancelcheq);
				$cancelcheqnewfilename = 'mt_calche_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["cancelcheq"]["tmp_name"],"images/" . $cancelcheqnewfilename);
			}
		}
	 //////////////////end cancel cheque //////////
	 $account->setClient_Onboarding($clienttype,$clname,$contact,$email,$agreementcopynewfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename,$accountId);  // insert into db
	 header("Location:moderntraders_onboarding.php?act=1");
	 
	 
	}
}



?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <title>ModernTraders Onboarding form</title>
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
      <h2>ModernTraders Onboarding Form</h2>
	   <button class="btn btn-dark"><a href="view_moderntraders.php">View ModernTraders</a></button>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Registered Successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="" id="clientboardingform" method="post" enctype="multipart/form-data">
		<input type="hidden" name="clientboardingform" value="clientboardingform">
		<input type="hidden" name="clienttype" value="1">
            <div class="form-group">
                <label for="fname" class="control-label required">Client name:</label>
                <input type="text" class="form-control" id="clname"
                    placeholder="Enter Name" name="clname" required >
					<p id="name_err"></p>
            </div>
			<div class="form-group">
                <label for="contact" class="control-label required">Mobile No:</label>
                <input type="text" class="form-control" id="contact"
                    placeholder="Enter Contact Number" name="contact" maxlength="10" required >
					<p id="contact_err"></p>
            </div>
            <div class="form-group">
                <label for="email" class="control-label required">Email Id:</label>
                <input type="email" class="form-control" id="email"
                    placeholder="Enter Email Id" name="email" required >
					<p id="email_err"></p>
            </div>
			<div class="form-group">
                <label for="kyc" class="control-label required">KYC documents:<i>(Adhar,Pan and Company details)</i></label>
                <input type="file" class="form-control" id="kyc"
                    placeholder="Enter KYC" name="kyc[]" multiple required>
					<p id="kyc_err"></p>
					<ul id="fileNames"></ul>
            </div>  
			<div class="form-group">
                <label for="Agreementcopy" class="control-label required">Agreement Copy:</label>
                <input type="file" class="form-control" id="Agreementcopy"   name="Agreementcopy" required >
					<p id="image_err"></p>
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
			<input type="submit" id="submitbtn" value="Submit">
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

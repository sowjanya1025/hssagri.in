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

if(isset($_GET['id']))
{
	$id = $_GET['id'];
	$individual_client_data = $account->getClient_OnBoardingData_ById($id);
}

//print_r($individual_farmer_data);
if(!empty($_POST))
{
	if(isset($_POST['clientboardingform'])=='clientboardingform')
	{
		$id = isset($_POST['clientid'])? $_POST['clientid'] : NULL;
		$clname = isset($_POST['clname'])? $_POST['clname'] : NULL;
		$contact = isset($_POST['contact'])? $_POST['contact'] : NULL;
		$email = isset($_POST['email'])? $_POST['email'] : NULL;
		//$Agreementcopy=isset($_FILES['Agreementcopy']['name']) ? $_FILES['Agreementcopy']['name'] : NULL;
		$acctname = isset($_POST['acctname'])? $_POST['acctname'] : NULL;
		$acctnumber = isset($_POST['acctnumber'])? $_POST['acctnumber'] : NULL;
		$ifsccode = isset($_POST['ifsccode'])? $_POST['ifsccode'] : NULL;
		$branchname = isset($_POST['branchname'])? $_POST['branchname'] : NULL;

		$agreementcopynewfilename = $individual_client_data['cl_agreementcopy'];
		$kycFiles = unserialize($individual_client_data['cl_kyc']);
		$cancelcheqnewfilename = $individual_client_data['cl_bank_cancelcheq']; 

		// agreement copy upload
		if (!empty($_FILES["Agreementcopy"]["name"]))
		{
			$allowedExts = array("jpg", "jpeg", "png","gif");
			$extension = pathinfo($_FILES["Agreementcopy"]["name"], PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $_FILES["Agreementcopy"]["name"]);
				$agreementcopynewfilename = 'cl_agrcopy_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["Agreementcopy"]["tmp_name"],"images/" . $agreementcopynewfilename);
			}
		}
		  
		// end agreement copy upload///

	// Checks if user sent an empty form 
	if(!empty(array_filter($_FILES['kyc']['name'])))
	 {
		// Loop through each file in files[] array
		$kycFiles = [];
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
	  // end kyc upload///////////////////
	 $kycFilesSerialized = serialize($kycFiles);
	 ////////////////// cancel cheque ////////////
		if (!empty($_FILES["cancelcheq"]["name"]))
		{
			$allowedExts = array("jpg", "jpeg", "png","gif");
			$extension = pathinfo($_FILES["cancelcheq"]["name"], PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".",$_FILES["cancelcheq"]["name"]);
				$cancelcheqnewfilename = 'cl_calche_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["cancelcheq"]["tmp_name"],"images/" . $cancelcheqnewfilename);
			}
		}
	 //////////////////end cancel cheque //////////

		$updateID  = $account->updateClient_Onboarding($id,$clname,$contact,$email,$agreementcopynewfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename);    // update into db
		header("Location:edit_client_onboarding.php?act=1&id=$id");
	}
}
?>
<!doctype html>
<html lang="en">
<head>
<?php require_once('header.php'); ?>
  <title>Edit Client Onboarding form</title>
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
      <h2>Edit Page</h2>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Saved changes successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="" id="clientboardingform" method="post" enctype="multipart/form-data">
		<input type="hidden" name="clientboardingform" value="clientboardingform">
		<input type="hidden" name="clientid" value="<?php echo $individual_client_data['id']; ?>">
		<input type="hidden" name="clienttype" value="1">
            <div class="form-group">
                <label for="fname">Client name:</label>
                <input type="text" class="form-control" id="clname"
                    placeholder="Enter Name" name="clname" value="<?php echo $individual_client_data['cl_name']; ?>" required >
					<p id="name_err"></p>
            </div>
			<div class="form-group">
                <label for="contact">Mobile No:</label>
                <input type="text" class="form-control" id="contact"
                    placeholder="Enter Contact Number" name="contact" maxlength="10" value="<?php echo $individual_client_data['cl_mobile']; ?>" required >
					<p id="contact_err"></p>
            </div>
            <div class="form-group">
                <label for="email">Email Id:</label>
                <input type="email" class="form-control" id="email"
                    placeholder="Enter Email Id" name="email" required value="<?php echo $individual_client_data['cl_email']; ?>" >
					<p id="email_err"></p>
            </div>
			<div class="form-group">
                <label for="kyc">KYC documents:<i>(Adhar,Pan and Company details)</i></label>
				<?php
					// $kyc_doc = $individual_farmer_data['fr_kyc'];
					 // $kyc_explode = explode(',', $kyc_doc);
					  $kycFiles = unserialize($individual_client_data['cl_kyc']);
					  foreach($kycFiles as $kyc)
					  { 
							$extension = pathinfo($kyc, PATHINFO_EXTENSION);
						if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
							echo "<a href='images/".htmlspecialchars($kyc)."' target='_blank' id='fileLink'><img src='images/".htmlspecialchars($kyc)."' alt='' onclick='window.open(this.src, '_blank');' width='50' height='50' style='border:1px solid black'>Open Image</a>";
						} elseif ($extension == 'pdf') {
							echo "<embed src='images/".htmlspecialchars($kyc)."' width='150' height='150' type='application/pdf'><a href='images/".htmlspecialchars($kyc)."' target='_blank' id='fileLink'>click to open</a>";
						} else {
							echo "Unsupported file type.";
						}
						//echo "<br>";
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					} ?>
					  
                <input type="file" class="form-control" id="kyc"
                    placeholder="Enter KYC" name="kyc[]" multiple>
					<p id="kyc_err"></p>
            </div>  
			<div class="form-group">
                <label for="Agreementcopy">Agreement Copy:</label>
						<?php if(!empty($individual_client_data['cl_agreementcopy'])) {
 
							$extension1 = pathinfo($individual_client_data['cl_agreementcopy'], PATHINFO_EXTENSION);
						if (in_array($extension1, ['jpg', 'jpeg', 'png', 'gif'])) {
							echo "<a href='images/".htmlspecialchars($individual_client_data['cl_agreementcopy'])."' target='_blank' id='fileLink'><img src='images/".htmlspecialchars($individual_client_data['cl_agreementcopy'])."' alt='' onclick='window.open(this.src, '_blank');' width='50' height='50' style='border:1px solid black'>Open Image</a>";
						} elseif ($extension == 'pdf') {
							echo "<embed src='images/".htmlspecialchars($individual_client_data['cl_agreementcopy'])."' width='150' height='150' type='application/pdf'><a href='images/".htmlspecialchars($individual_client_data['cl_agreementcopy'])."' target='_blank' id='fileLink'>click to open</a>";
						} else {
							echo "Unsupported file type.";
						}
						//echo "<br>";
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
											
						
						  ?>
					<?php } ?>
                <input type="file" class="form-control" id="Agreementcopy"   name="Agreementcopy">
					<p id="image_err"></p>
            </div>	
			<div class="form-group" style="border:0px solid #999999;">
			  <div><h6><strong>Bank Details:</strong></h6></div>
  <div class="form-group row" >
    <label for="acctname" class="col-sm-3 col-form-label">Account Holder Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctname" name="acctname" value="<?php echo $individual_client_data['cl_bank_acctholdername']; ?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="acctnumber" class="col-sm-3 col-form-label">Account Number:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctnumber" name="acctnumber" value="<?php echo $individual_client_data['cl_bank_acctnumber']; ?>">
    </div>
  </div>
<div class="form-group row">
    <label for="ifsccode" class="col-sm-3 col-form-label">IFSC Code:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="ifsccode" name="ifsccode" value="<?php echo $individual_client_data['cl_bank_ifsccode']; ?>">
    </div>
  </div>
<div class="form-group row">
    <label for="branchname" class="col-sm-3 col-form-label">Branch Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="branchname" name="branchname" value="<?php echo $individual_client_data['cl_bank_branchname']; ?>">
    </div>
  </div>  <div class="form-group row">
    <label for="cancelcheq" class="col-sm-3 col-form-label">Cancel Cheque:</label>
    <div class="col-sm-9">
							<?php if(!empty($individual_client_data['cl_bank_cancelcheq'])) {  ?>
				<img src="images/<?php echo $individual_client_data['cl_bank_cancelcheq']; ?>" style="border:1px solid black"  alt="Image" height="42" width="42">
					<?php } ?>

      <input type="file" class="form-control" id="cancelcheq" name="cancelcheq" value="<?php echo $individual_client_data['cl_bank_cancelcheq']; ?>">
    </div>
  </div>
			</div>
			<input type="submit" id="submitbtn" value="Submit">&nbsp;
			<input type="button" value="Back"  onclick="history.back()">
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
	
  </script>
</body>
</html>

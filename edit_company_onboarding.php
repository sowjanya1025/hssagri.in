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
	$individual_company_data = $account->getCompany_OnBoardingData_ById($id);
}

//print_r($individual_farmer_data);
if(!empty($_POST))
{
	if(isset($_POST['companyboarding'])=='companyboarding')
	{
		$c_name = isset($_POST['cname'])? $_POST['cname'] : NULL;
		$c_pan = isset($_POST['pan'])? $_POST['pan'] : NULL;
		$c_reg = isset($_POST['reg'])? $_POST['reg'] : NULL;
		$c_gst= isset($_POST['gst'])? $_POST['gst'] : NULL;
		$c_adhar = isset($_POST['adhar'])? $_POST['adhar'] : NULL;
		//$c_cheque=isset($_FILES['cheque']['name']) ? $_FILES['cheque']['name'] : NULL;
		//$kyc = isset($_POST['kyc'])? $_POST['kyc'] : NULL;
		$newfilename = $individual_company_data['cm_cheque'];
		$kycFiles = unserialize($individual_company_data['cm_kyc']);
		////////bank details ////
		$acctname = isset($_POST['acctname'])? $_POST['acctname'] : NULL;
		$acctnumber = isset($_POST['acctnumber'])? $_POST['acctnumber'] : NULL;
		$ifsccode = isset($_POST['ifsccode'])? $_POST['ifsccode'] : NULL;
		$branchname = isset($_POST['branchname'])? $_POST['branchname'] : NULL;

		// image upload
		if (!empty($_FILES["cheque"]["name"]))
		{
			$allowedExts = array("jpg", "jpeg", "png","gif");
			$extension = pathinfo($_FILES["cheque"]["name"], PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $_FILES["cheque"]["name"]);
				$newfilename = 'cm_'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["cheque"]["tmp_name"],"images/" . $newfilename);
			}
		}
		  
		// end image upload///

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
							$kycfilename = 'cm_kyc'.$accountId.'_'.rand().'.'.end($temp);
							move_uploaded_file($_FILES["kyc"]["tmp_name"][$key],"images/" . $kycfilename);
							$kycFiles[] = $kycfilename;
							//$account->setfarmer_Onboarding_kyc($kycfilename,$lastinsert,$accountId);  // insert into db
					   }	 
			}
	  } 
	  // end kyc upload///////////////////
	 $kycFilesSerialized = serialize($kycFiles);
		$updateID  = $account->updateCompany_Onboarding($id,$c_name,$c_pan,$c_reg,$c_gst,$c_adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname); // update into db
		header("Location:edit_company_onboarding.php?id=$id&act=1");
	}
}
?>
<!doctype html>
<html lang="en">
<head>
<?php require_once('header.php'); ?>
  <title>Edit Supplier details</title>
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
      <h2>Edit Supplier details</h2>
	 <button class="btn btn-dark"><a href="cview.php">Back</a></button>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Saved changes Successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="#" id="companyboardingform" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="companyboarding" value="companyboarding">
            <div class="form-group">
                <label for="fname" class="control-label required">Name:</label>
                <input type="text" class="form-control" id="cname"
                    placeholder="Enter Name" name="cname" value="<?php echo $individual_company_data['cm_name']; ?>" >
					<p id="name_err"></p>
            </div>
			<div class="form-group">
                <label for="pan" class="control-label required">PAN details:</label>
                <input type="text" class="form-control" id="pan"
                    placeholder="Enter PAN Id" name="pan" value="<?php echo $individual_company_data['cm_pan']; ?>" >
					<p id="pan_err"></p>
            </div>
						<div class="form-group">
                <label for="pan" class="control-label required">Registration details:</label>
                <input type="text" class="form-control" id="reg"
                    placeholder="Enter Registration details" name="reg" value="<?php echo $individual_company_data['cm_reg']; ?>" >
					<p id="reg_err"></p>
            </div>

			<div class="form-group">
                <label for="pan" class="control-label required">GST:</label>
                <input type="text" class="form-control" id="gst"
                    placeholder="Enter GST" name="gst" value="<?php echo $individual_company_data['cm_gst']; ?>" >
					<p id="gst_err"></p>
            </div>

			<div class="form-group">
                <label for="adhar" class="control-label required">Adhar details:</label>
                <input type="text" class="form-control" id="adhar"
                    placeholder="Enter Adhar Id" name="adhar" value="<?php echo $individual_company_data['cm_adhar']; ?>" >
					<p id="adhar_err"></p>
            </div> 
						<?php /*?><div class="form-group">
                <label for="cheque" >Cancel Cheque Image Upload:</label>
						<?php if(!empty($individual_company_data['cm_cheque'])) {  ?>
				<a href="images/<?php echo $individual_company_data['cm_cheque']; ?>" target='_blank' id='fileLink'><img src="images/<?php echo $individual_company_data['cm_cheque']; ?>" style="border:1px solid black" height="42" width="42" alt='image'>Open Image</a>
					<?php } ?>
		<input type="hidden" id="" name="image1" value="<?php if(isset($individual_company_data['cm_cheque'])) { echo $individual_company_data['cm_cheque'];  }  ?>" />
                <input type="file" class="form-control" id="cheque" name="cheque"  >
					<p id="cheque_err"></p>
            </div><?php */?>          
			<div class="form-group">
                <label for="kyc" >KYC documents:</label>
				<?php
					  $kycFiles = unserialize($individual_company_data['cm_kyc']);
					  if(!empty($kycFiles))
					  {
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
					}  } ?>
					  
					  
	                <input type="file" class="form-control" id="kyc"
                    name="kyc[]" multiple>
					<p id="kyc_err"></p>
					<ul id="fileNames"></ul>
            </div>
			<div class="form-group" style="border:0px solid #999999;">
			  <div><h6><strong>Bank Details:</strong></h6></div>
  <div class="form-group row" >
    <label for="acctname" class="col-sm-3 col-form-label">Account Holder Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctname" name="acctname" value="<?php echo $individual_company_data['cm_bank_acctholdername']; ?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="acctnumber" class="col-sm-3 col-form-label">Account Number:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="acctnumber" name="acctnumber" value="<?php echo $individual_company_data['cm_bank_acctnumber']; ?>">
    </div>
  </div>
<div class="form-group row">
    <label for="ifsccode" class="col-sm-3 col-form-label">IFSC Code:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="ifsccode" name="ifsccode" value="<?php echo $individual_company_data['cm_bank_ifsccode']; ?>">
    </div>
  </div>
<div class="form-group row">
    <label for="branchname" class="col-sm-3 col-form-label">Branch Name:</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="branchname" name="branchname" value="<?php echo $individual_company_data['cm_bank_branchname']; ?>">
    </div>
  </div> 
   <div class="form-group row">
    <label for="cancelcheq" class="col-sm-3 col-form-label">Cancel Cheque:</label>
    <div class="col-sm-9">
							<?php if(!empty($individual_company_data['cm_cheque'])) {  ?>
				<img src="images/<?php echo $individual_company_data['cm_cheque']; ?>" style="border:1px solid black"  alt="Image" height="42" width="42">
					<?php } ?>

     <input type="hidden" id="" name="image1" value="<?php if(isset($individual_company_data['cm_cheque'])) { echo $individual_company_data['cm_cheque'];  }  ?>" />
                <input type="file" class="form-control" id="cheque" name="cheque"  >
    </div>
  </div>
			</div>          
		<input type="button" id="submitbtn" value="Submit">
        </form>
    </div>
    </div>

  </div>
  <!-- wrapper and -->


  <!-- Option 2: jQuery, Popper.js, and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
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
		}
*/
		//validateUsername();
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

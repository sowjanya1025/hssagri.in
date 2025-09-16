<?php
session_start();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



//print_r($_SESSION);
include'account.php';
$account =  new account();

$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}


/// GRN -  default value is 2(pending) ,  1(approved) , 0(rejected)///
$farmerdata = $account->getAllfarmers_OnBoardingData();

if(!empty($_POST))
{
	if(isset($_POST['goodsreceivenote'])=='goodsreceivenote')
	{
	
	$farmer_id = isset($_POST['farmers_list'])? $_POST['farmers_list'] : NULL;  
	$collection_center =  isset($_POST['ccenter'])? $_POST['ccenter'] : NULL;  
	$farmersname = isset($_POST['farmersname'])? $_POST['farmersname'] : NULL; 
	$itemname = isset($_POST['itemname'])? $_POST['itemname'] : NULL;  
	$code = isset($_POST['itemcode'])? $_POST['itemcode'] : NULL; 
	$itemcodeid = isset($_POST['itemcodeid'])? $_POST['itemcodeid'] : NULL; 
	$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;
	$total =  isset($_POST['totalamt']) ? intval($_POST['totalamt']) : 0;
	
	$lastid = $account->setGoodsReceive_note($accountId,$farmer_id,$collection_center,$itemname,$code,$itemcodeid,$price,$quantity,$total);
	$insertedid =  $lastid['insert_last_id'];
	///////////////php mail///////////
	

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
	$mail->addAddress('msatest200@gmail.com', 'Hssagri'); // Add a recipient
   // $mail->addAddress('swjnambati@gmail.com', 'Hssagri'); // Add a recipient
    // $mail->addReplyTo('info@example.com', 'Information'); // Optional reply-to address
    // $mail->addCC('cc@example.com'); // Optional CC
    // $mail->addBCC('bcc@example.com'); // Optional BCC

    // Attachments (optional)
    // $mail->addAttachment('/path/to/file.pdf'); // Add attachments
    // $mail->addAttachment('/path/to/image.jpg', 'new.jpg'); // Optional name for attachment

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Notification from Hssagri.in';
   // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	// Buttons with links to approval/rejection script
    $approveLink = 'https://hssagri.in/handle-approval.php?action=approve&user_id='.$insertedid.'';
    $rejectLink = 'https://hssagri.in/handle-approval.php?action=reject&user_id='.$insertedid.'';
	
					$bodyContent = "<p>Hi,</p>
					<p><b>Warm greetings from Hssagri.in!</b></p>
					<p><b>Below are the details from Goods Receive Note. </b></p>
					
					Farmer Name : ".$farmersname."<br/>
					Item Code : ".$code."<br/>
					Item Quantity : ".$quantity."<br/>
					Item Price : &#8377;".$price."<br/>
					Total Amount : &#8377;".$total."<br/>

					<p>Please approve or reject this request:</p>
					<a href='$approveLink' style='padding:10px;background-color:green;color:white;text-decoration:none;'>Approve</a>
					<a href='$rejectLink' style='padding:10px;background-color:red;color:white;text-decoration:none;'>Reject</a>
   
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
	
	
	// $account->setClient_Onboarding($clienttype,$clname,$contact,$email,$agreementcopynewfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename);  // insert into db
	 header("Location:goodsreceivenote.php?act=1");
	 exit;
	 
	 
	}
}



?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <title>GRN</title>
      <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
      <h2>GRN(Goods Receive Note)</h2>
      <div id="carbon-block" class="my-3"></div>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Registered Successfully</span></b></div>
	  <?php } } ?>
    <div class="container">
        <form action="" id="goodsreceivenote" method="post" enctype="multipart/form-data">
		<input type="hidden" name="goodsreceivenote" value="goodsreceivenote">
		<input type="hidden" name="clienttype" value="4">
            <div class="form-group">
                <label for="farmers_list" class="control-label required">Onboarded Farmers List:</label>
					<select class="browser-default custom-select" name="farmers_list" id="farmers_list" required oninput="setfarmersname()">
						<option value="" disabled="disabled" selected="selected">-Select Farmers-</option>
					  	<?php foreach($farmerdata as $fdata) 
						{  ?>
					<option value="<?php echo $fdata['id'] ?>"><?php echo $fdata['fname'] ?></option>
					 <?php } ?>
				</select>
				<input type="hidden" name="farmersname" id="farmersname" value="">
            </div>
			<div class="form-group">
                <label for="quantity" class="control-label">Enter Collection Center:</label>
                <input type="text" class="form-control" id="ccenter"
                    placeholder="Enter Collection center" name="ccenter">
            </div>
			<div class="form-group">
                <label for="itemcode" class="control-label required">Enter Item Name:</label>
                <input type="text" class="form-control" id="itemname"
                    placeholder="Enter Item Name" name="itemname" maxlength="10" required autocomplete="off" ><i>[To create new items <a href="create_item.php" style="color:#009933">Click here</a>]</i>
					<p id="item_nameerr"></p>
            </div>	
			<div class="form-group">
                <label for="itemcode" class="control-label required">Enter Item Code:</label>
                <input type="text" class="form-control" id="itemcode"
                    placeholder="Enter Item Code" name="itemcode" maxlength="10" required readonly>
					<p id="item_codeerr"></p>
					<input type="hidden" name="code_check" id="code_check" value="">
					<input type="hidden" name="itemcodeid" id="itemcodeid" value="">
            </div>
            <div class="form-group">
                <label for="quantity" class="control-label required">Enter Quantity:</label>
                <input type="number" class="form-control" id="quantity"
                    placeholder="Enter Quantity" name="quantity" step="0.01" required oninput="calculateTotal()" ><i>(in kgs)</i>
            </div>
            <div class="form-group">
                <label for="price" class="control-label required">Enter Price:</label>
                <input type="number" class="form-control" id="price"
                    placeholder="Enter Price" name="price"  step="0.01" required oninput="calculateTotal()" ><i>(per kg)</i>
            </div>
			<!--<div class="form-group">
                <label for="itemimage">Upload Image:</label>
                <input type="file" class="form-control" id="itemimage"   name="itemimage" required >
            </div>	-->
			<div class="form-group">
                <label for="itemimage">Total Amount:</label>
                <input type="text" class="form-control" id="totalamt"   name="totalamt" readonly >
            </div>
			<input type="submit" value="Submit" id="btnSubmit">
        </form>
    </div>
    </div>

  </div>
<?php require_once('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>

        function calculateTotal() {
            let price = parseFloat(document.getElementById('price').value) || 0;
            let quantity = parseFloat(document.getElementById('quantity').value) || 0;
            let total = price * quantity;
           // document.getElementById('totalamt').value = total.toFixed(2);
			document.getElementById('totalamt').value = Math.round(total);;
        }
		
		function setfarmersname()
		{
				    var selectElement = document.getElementById("farmers_list");
					var selectedText = selectElement.options[selectElement.selectedIndex].text;
					document.getElementById("farmersname").value = selectedText;
		}
    </script>
  <script>
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	  
	      // Autocomplete functionality for item names
    var availableItems = []; // This will store all the available item names from the autocomplete
    $('#itemname').autocomplete({
        source: function(request, response) {
            // AJAX call to fetch item names based on user input
            $.ajax({
                url: 'get_item_names.php', // PHP script to fetch item names
                method: 'GET',
                data: { term: request.term }, // Send user input as the term
                success: function(data) {
				//alert(data);
				 availableItems = JSON.parse(data).map(item => item.value); // Store the item names
                   response(JSON.parse(data)); // Parse and send the response to the autocomplete widget
					//response(data);
                }
            });
        },
        minLength: 1, // Minimum characters before triggering autocomplete
        select: function(event, ui) {
            // When an item name is selected, auto-fetch the corresponding item code
            var selectedItem = ui.item.value;
            $.ajax({
                url: 'get_item_code.php', // PHP script to fetch the item code
                method: 'GET',
                data: { item_name: selectedItem },
                success: function(data) {
				//alert("hereitemcode");
			//	alert(data);
                    var result = JSON.parse(data);
					//alert(result.item_code);
                    $('#itemcode').val(result.item_code); // Set the item code in the input field
					$('#itemcodeid').val(result.id); // Set the item code in the input field
                },
				//error: function() { alert("error"); }
            });
        }
    });

    // Validate if the input is from the autocomplete list only
    $('#itemname').change(function() {
        var enteredValue = $(this).val();
        if (!availableItems.includes(enteredValue)) {
            // Clear the input field or show an error
            alert('Please select item name from the list.');
            $(this).val(''); // Clear the field
            $('#itemcode').val(''); // Clear the item code field
        }
    });



/*	  $('#itemcode').on('blur',function()
	  {
		let itm_code = $(this).val();
		if(itm_code!=='')
		{
		$.ajax({
			type:"post",
			url:"check_itemAvailability.php",
			data:{id:itm_code},
			dataType: 'json',
			success:function(response)
			{
			//alert(response[0]);
			//alert(response[1]);
				if(response[0] == 1)
				{
					//alert("Code already exists"); 
					$('#item_codeerr').html('<i class="bi bi-check-circle" style="color:green" ></i>');
					$('#code_check').val(parseInt(response[0])); 
					$('#itemcodeid').val(parseInt(response[1])); 
					
				}else if(response[0] == 0)
				{
					$('#item_codeerr').html('<i class="bi bi-x-circle" style="color:red">This item code does not exist..To create new item </i><button type="button" class="btn btn-dark"><a href="http://localhost/greenbasket/create_item.php">click here</a></button> ');
					$('#code_check').val(parseInt(response[1]));
					$('#itemcodeid').val(response[1]);  
				}
			}
		});
		}

	  }); // onblur
	  
	  
	  $('#goodsreceivenote').on('submit',function(e)
	   {
	   // alert("submit");
		e.preventDefault();
			let codevar = parseInt($('#code_check').val());
			//alert(jQuery.type(codevar))
			if(codevar===1)
			{
				$( this ).unbind( 'submit' ).submit(); // Release  prevent default
			}
			
		});
	  
*/	  /*$('#price,#quantity').on('blur',function(){
	  
	  alert("price");
	  let qty = $('#quantity').val();
	  let prc = $('#price').val();
	  let totalamount = '';
	  
	  if(qty !== '' && prc !== '')
	  {
	  	totalamount = qty*prc;
		$('#totalamt').val(totalamount); 
	  }
	  });*/
	  
	  
    });
	

  </script>
</body>
</html>

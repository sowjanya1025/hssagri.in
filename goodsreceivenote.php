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
	
	$vendors_list = isset($_POST['vendors_list'])? $_POST['vendors_list'] : NULL;   //  1 - farmers , 2- suppliers
	$collection_center =  isset($_POST['cctype'])? $_POST['cctype'] : NULL;  // 1-dc,2-cc,3-mkt,4-gt
	$farmersname = isset($_POST['names_list'])? $_POST['names_list'] : NULL; 
  	$billnumber = isset($_POST['billnumber']) ? $_POST['billnumber'] : 0;
  	$transportation =  isset($_POST['transportation']) ? floatval($_POST['transportation']) : 0;
	$otherExpenses =  isset($_POST['otherExpenses']) ? floatval($_POST['otherExpenses']) : 0;
	$total =  isset($_POST['totalAmount']) ? intval($_POST['totalAmount']) : 0;
	
	$clientsname = isset($_POST['clientsname'])? $_POST['clientsname'] : NULL; 
	$item_codes = isset($_POST['item-codes'])? $_POST['item-codes'] : NULL; 

	$uploadbill=isset($_FILES['uploadbill']['name']) ? $_FILES['uploadbill']['name'] : NULL;
		// image upload
		$newfilename="";
		if($uploadbill !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","pdf");
			$extension = pathinfo($uploadbill, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $uploadbill);
				$newfilename = 'grn_'.date("dmyhis").'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["uploadbill"]["tmp_name"],"images/" . $newfilename);
			}
		}
		// end image upload///

	$lastid = $account->setGoodsReceive_note($accountId,$collection_center,$vendors_list,$farmersname,$billnumber,$newfilename,$transportation,$otherExpenses,$total);
	$insertedid =  $lastid['insert_last_id']; // inserted id 
	
	// Prepare data(now insert items, quantity and price in grn_items table)
	$items = $_POST['items'];
	$quantities = $_POST['quantity'];
	$prices = $_POST['price'];

// Insert each item into the database
	foreach ($items as $itemId)
	 {
			$quantity = isset($quantities[$itemId]) ? $quantities[$itemId] : 0;
			$price = isset($prices[$itemId]) ? $prices[$itemId] : 0;
			$account->setGRN_items($insertedid,$itemId, $quantity, $price);
	}
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
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;


    // Recipients
    $mail->setFrom('admin@hssagri.in', 'Hssagri');
    $mail->addAddress('hssagrifarms@gmail.com', 'Hssagri'); // Add a recipient
//	$mail->addAddress('msatest400@gmail.com', 'Hssagri'); // Add a recipient
  //  $mail->addAddress('swjnambati@gmail.com', 'Hssagri'); // Add a recipient
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
					
					Vendors Name : ".$clientsname."<br/>
					Item Codes : ".$item_codes."<br/>
					Bill Number : ".$billnumber."<br/>
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
	      <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
  <style>
    body { background-color: #fafafa;   .redtext{ color: red; .greentext{ color: green;} 
 }
        .item-fields {
            display: flex;
            align-items: center; /* Center align items vertically */
            margin-bottom: 10px; /* Spacing between each item field */
        }
        .item-fields label {
            width: 150px; /* Fixed width for labels */
            margin-right: 10px; /* Spacing between label and input */
        }
        .item-fields input {
            margin-right: 10px; /* Spacing between inputs */
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
            <!--<div class="form-group">
                <label for="farmers_list" class="control-label required">Onboarded Vendors List:</label>
					<select class="browser-default custom-select" name="farmers_list" id="farmers_list" required oninput="setfarmersname()">
						<option value="" disabled="disabled" selected="selected">-Select Vendors-</option>
							 <option value="1">Farmers</option>
							 <option value="2">Suppliers</option>
				   </select>
				<input type="hidden" name="farmersname" id="farmersname" value="">
            </div>-->
			<div class="form-row">
            <div class="form-group col-md-6">
                <label for="farmers_list" class="control-label required">Onboarded Vendors List:</label>
					<select class="browser-default custom-select" name="vendors_list" id="vendors_list" required oninput="setclientstype()">
						<option value="" disabled="disabled" selected="selected">-Select Vendors-</option>
							 <option value="1">Farmers</option>
							 <option value="2">Suppliers</option>
				   </select>
            </div>
			<div class="form-group col-md-6">
			<label for="clientlist">Select Name:</label>
			<select class="browser-default custom-select" name="names_list" id="names_list" required oninput="setclientsname()">
			</select>
		<input type="hidden" name="clientsname" id="clientsname" value="">
		<input type="hidden" name="clienttype" id="clienttype" value="">
		 </div>
		 </div>
		 <div class="form-group">
					<label for="quantity" class="control-label">Enter Collection Center:</label>
	<div class="form-check form-check-inline">
	  <input class="form-check-input" type="radio" name="cctype" id="inlineRadio1" value="1">
	  <label class="form-check-label" for="inlineRadio1">DC</label>
	</div>
	<div class="form-check form-check-inline">
	  <input class="form-check-input" type="radio" name="cctype" id="inlineRadio2" value="2">
	  <label class="form-check-label" for="inlineRadio2">CC</label>
	</div>
	<div class="form-check form-check-inline">
	  <input class="form-check-input" type="radio" name="cctype" id="inlineRadio3" value="3" >
	  <label class="form-check-label" for="inlineRadio3">MKT</label>
	</div>
	<div class="form-check form-check-inline">
	  <input class="form-check-input" type="radio" name="cctype" id="inlineRadio4" value="4" >
	  <label class="form-check-label" for="inlineRadio4">GT</label>
	</div>
	</div>

			
			<div class="form-group">
                <label for="quantity" class="control-label">Bill Number</label>
                <input type="text" class="form-control" id="billnumber"
                    placeholder="Enter Bill Number" name="billnumber">
            </div>
			<div class="form-group">
                <label for="quantity" class="control-label">Upload Bill</label>
                <input type="file" class="form-control" id="uploadbill"
                    placeholder="Enter Collection center" name="uploadbill">
            </div>
			<div class="form-group"  style="width: 300px;">
                <label for="itemcode" class="control-label required">Enter Item Name(Multiple select):</label>
                <select id="autocomplete-select" name="items[]" multiple="multiple" style="width: 100%;" required>
    </select><i>[To create new items <a href="create_item.php" style="color:#009933">Click here</a>]</i>
					<p id="item_nameerr"></p>
            </div>	
			
			<div id="fieldsContainer">
        <!-- Quantity and price fields will be added here -->
    </div>

			<div class="form-group">
                <label for="itemcode" class="control-label">Enter Item Code:</label>
                <input type="text" class="form-control" id="item-codes"
                    placeholder="Enter Item Code" name="item-codes" maxlength="10" readonly>
					<p id="item_codeerr"></p>
					<input type="hidden" name="item-codesid" id="item-codesid" value="">
            </div>
		
			<div class="form-group">
                <label for="transportation" class="control-label">Enter Transportation:</label>
				<input type="number" class="form-control" id="transportation" name="transportation" min="0" step="0.01" value="0" >
            </div>
			<div class="form-group">
                <label for="otherExpenses" class="control-label">Other Expenses:</label>
   				 <input type="number" class="form-control" id="otherExpenses" name="otherExpenses" min="0" step="0.01" value="0" >
            </div>
				<div class="form-group">
                <label for="itemimage">Total Amount:</label>
                    <input type="number" class="form-control"  id="totalAmount" name="totalAmount" readonly>
            </div>
			<input type="submit" value="Submit" id="btnSubmit">
        </form>
    </div>
    </div>

  </div>
<?php require_once('footer.php'); ?>
<!--    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
-->	    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

  <script>
  		function setclientsname()
		{
				    var selectElement = document.getElementById("names_list");
					var selectedText = selectElement.options[selectElement.selectedIndex].text;
					document.getElementById("clientsname").value = selectedText;
		} 
		function setclientstype()
		{
				    var selectElement = document.getElementById("vendors_list");
					var selectedText = selectElement.options[selectElement.selectedIndex].text;
					document.getElementById("clienttype").value = selectedText;
		} 

  $(document).ready(function() {
    $('#autocomplete-select').select2({
        placeholder: 'Search for items...',
        minimumInputLength: 0, // Show all items by default
        ajax: {
            url: 'search-items.php',
            dataType: 'json',
            delay: 250, 
            data: function (params) {
                return {
                    search: params.term || '' // If no term is entered, fetch all items
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name,
                            code: item.code 
                        };
                    })
                };
            },
            cache: true
        }
    });
	
	
   // $('#autocomplete-select').select2();

    // Listen for changes in the Select2 dropdown
   // $('#autocomplete-select').on('change', updateFields);
	 $('#autocomplete-select').on('select2:select select2:unselect', updateFields);

    // Listen for changes in quantity, price, transportation, or other expenses fields
    $(document).on('input', '.quantity, .price, #transportation, #otherExpenses', calculateTotal);

    // Function to add quantity and price fields for selected items
function updateFields() {
    var selectedItems = $('#autocomplete-select').select2('data'); // Get selected item data
    var fieldsContainer = $('#fieldsContainer');
    var existingData = {};
	 var selectedCodes = []; // item code

    // Save the current values for quantity and price for each item
    fieldsContainer.find('.item-fields').each(function() {
        var itemId = $(this).data('item-id');
        var quantity = $(this).find('.quantity').val();
        var price = $(this).find('.price').val();
        existingData[itemId] = { quantity: quantity, price: price };
    });

    // Clear the container to redraw fields
    fieldsContainer.empty();

    // Loop through selected items and add their respective fields
    selectedItems.forEach(function(item) {
        var itemId = item.id;
        var itemName = item.text; // Use the text property for item names
		
		selectedCodes.push(item.code);  // Add item codes to the array


        // Use existing values if they exist, or set default values
        var quantityValue = existingData[itemId] ? existingData[itemId].quantity : 1;
        var priceValue = existingData[itemId] ? existingData[itemId].price : 0;

        var quantityLabel = '<label for="quantity-' + itemId + '">Quantity for ' + itemName + ':</label>';
        var quantityInput = '<input type="number" class="quantity" name="quantity[' + itemId + ']" id="quantity-' + itemId + '" step="0.01"  value="' + quantityValue + '" required>';

        var priceLabel = '<label for="price-' + itemId + '">Price for ' + itemName + ':</label>';
        var priceInput = '<input type="number" class="price" name="price[' + itemId + ']" id="price-' + itemId + '" step="0.01" value="' + priceValue + '" required>';

        fieldsContainer.append('<div class="item-fields" id="fields-' + itemId + '" data-item-id="' + itemId + '">' + quantityLabel + quantityInput + priceLabel + priceInput + '</div>');
    });
	
	$('#item-codes').val(selectedCodes.join(', ')); // Display codes in the text box
    calculateTotal(); // Recalculate total after updating fields
}

    // Function to calculate the total amount
    function calculateTotal() {
        var total = 0;

        // Calculate the sum of (quantity * price) for each selected item
        $('.item-fields').each(function() {
            var quantity = parseFloat($(this).find('.quantity').val()) || 0;
            var price = parseFloat($(this).find('.price').val()) || 0;
            total += quantity * price;
        });

        // Add transportation and other expenses
        var transportation = parseFloat($('#transportation').val()) || 0;
        var otherExpenses = parseFloat($('#otherExpenses').val()) || 0;
        total += transportation + otherExpenses;

        // Display the total amount in the read-only field
        $('#totalAmount').val(total.toFixed(2));
    }




/*    // Event listener to handle selection/deselection
    $('#autocomplete-select').on('select2:select select2:unselect', function (e) {
        var selectedItems = $('#autocomplete-select').select2('data');
/*        var selectedCodes = [];
		 var selectedCodesid = [];

        selectedItems.forEach(function(item) {
            selectedCodes.push(item.code);  // Add item codes to the array
			selectedCodesid.push(item.id);  // Add item codes to the array
        });

        $('#item-codes').val(selectedCodes.join(', ')); // Display codes in the text box
		$('#item-codesid').val(selectedCodesid.join(', ')); // Display codes in the text box
*/		
      //  var selectedItems = $('#autocomplete-select').val(); // Array of selected item IDs
/*        $('#fieldsContainer').empty();

        // Loop through selected items and use data-name attribute for item names
        for (var i = 0; i < selectedItems.length; i++) {
            var itemId = selectedItems[i];
			alert(itemId);
            var itemName = $('#autocomplete-select').find('option[value="' + itemId + '"]').attr('name')

            var quantityLabel = '<label for="quantity-' + itemId + '">Quantity for ' + itemName + ':</label>';
            var quantityInput = '<input type="number" class="quantity" name="quantity[' + itemId + ']" id="quantity-' + itemId + '" min="1" value="1" required>';

            var priceLabel = '<label for="price-' + itemId + '">Price for ' + itemName + ':</label>';
            var priceInput = '<input type="number" class="price" name="price[' + itemId + ']" id="price-' + itemId + '" min="0.01" step="0.01" value="0" required>';

            $('#fieldsContainer').append('<div class="item-fields" id="fields-' + itemId + '">' + quantityLabel + quantityInput + priceLabel + priceInput + '</div>');
        }

        calculateTotal(); // Recalculate total after adding new fields
    });
	
    // Function to calculate the total amount
    function calculateTotal() {
        var total = 0;

        // Calculate the sum of (quantity * price) for each selected item
        $('.item-fields').each(function() {
            var quantity = parseFloat($(this).find('.quantity').val()) || 0;
            var price = parseFloat($(this).find('.price').val()) || 0;
            total += quantity * price;
        });

        // Add transportation and other expenses
        var transportation = parseFloat($('#transportation').val()) || 0;
        var otherExpenses = parseFloat($('#otherExpenses').val()) || 0;
        total += transportation + otherExpenses;

        // Display the total amount in the read-only field
        $('#totalAmount').val(total.toFixed(2));
    }
*/	
	 $('#vendors_list').on('change',function()
	  {
		let vid = $(this).val();
		$.ajax({
			type:"post",
			url:"getFarmerOrSupplier_list.php",
			data:{vendorid:vid},
			success:function(response)
			{ // alert(response);
				 $('#names_list').empty();
				$('#names_list').append(response);
			   }
		});
	  });
	
});

  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	  
    });
	

  </script>
</body>
</html>

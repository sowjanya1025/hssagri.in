<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$today = date('Y-m-d');


if(!empty($_POST))
{
   // print_r($_POST); exit;
   $date = date('Y-m-d');
   $misc = NULL;

    $total_quantity = $_POST['totalQty']; 
    $total_amount = $_POST['totalAmount']; 

    $lastid = $account->create_purchase($accountId,$date,$misc,$total_quantity,$total_amount); // insert into db
    $insertedid =  $lastid['insert_last_id']; // inserted id 
   // echo $insertedid;
   // exit;




    foreach($_POST['items'] as $item)
    {
        if($item['quantity'] > 0 && $item['price'] > 0 && $item['id'] > 0  )
        {
             $qty = (float) $item['quantity'];
             $price = (float) $item['price'];
             $total = $qty * $price;

             $account->create_purchase_item($insertedid,$item['id'],$qty,$price,$total); // insert into db
        }
        
    }

    header("Location:purchase_form.php?act=1");
	   exit;

}
$itemdata = $account->get_create_ItemData();


?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

  <title>Purchase Form</title>
  <style>
	body { background-color: #fafafa; }
	.redtext { color: red; }
	.greentext { color: green; }
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
      <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Added Successfully</span></b></div>
	  <?php }  } ?>
	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='del')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Item Deleted</span></b></div>
	  <?php }  } ?>
    <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='edited')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Edited Successfully</span></b></div>
	  <?php }  } ?>
	  <br><br>
      <?php 
      // Check if today's purchase already exists
$existing_purchase = $account->get_purchase_by_date($accountId, $today);
//exit;

if ($existing_purchase) {
    echo "<h3 style='color:green;'>Today's purchase form is filled.</h3>";
    echo "<a style='color:blue;' href='purchase_editform.php?id=".$existing_purchase['id']."'>Click here to edit today's purchase</a>";
   // exit; // Stop the rest of the form from displaying
} else {  ?>

      <div id="carbon-block" class="my-3"></div>
	  	  
    <div class="container">
	<button id="recoverYesterday" type="button" class="btn btn-warning">Recover from Yesterday</button>
<form id="yesterdayForm">
</form>
	
	<div class="table-responsive" style=" border:3px solid #099114ff; padding:10px; background-color:#fff;">
    <form action="" method="post" enctype="multipart/form-data" id="itemform" >
        <table class="table hover" id="purchaseTable">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name of the item</th>
	  <th scope="col">Purchased Qty in kg/piece/kattu</th>
	  <th scope="col">Purchased price per kg/piece/kattu</th>
	  <th scope="col">Total cost</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
	 $current_category = "";
	 $colors = ['#00FFCC', '#FF0033', '#99FF33', '#CC9999', '#993366']; // Add more colors
		$colorIndex = 0;
  	foreach($itemdata as $item) 
	{
	 $inc++;
	 $color = $colors[$colorIndex % count($colors)];
	  
	?>
	   
		<?php
		if ($current_category != $item['category_name']) 
		{
            $current_category = $item['category_name'];
            echo "<th colspan='5'  style='background-color:$color; font-size:18px  '>" . htmlspecialchars($current_category) . "</th>";
			$colorIndex++;
		}
		 ?> 
		<tr>
      <th scope="row"   ><?php echo $inc; ?></th>
	  <td><?php echo htmlspecialchars($item['name']); ?>/<?php echo htmlspecialchars($item['kannada_name']); ?>
      <input type="hidden" class="itemId" name="items[<?php echo $inc ?>][id]" value="<?php echo  $item['id'] ?>"></td>
      <td><input style="width:80px;" step="any" class="todayQty"   type="number" name="items[<?php echo $inc ?>][quantity]"  value=""></td>
	  <td><input style="width:80px;" step="any" class="todayPrice"  type="number" name="items[<?php echo $inc ?>][price]"  value=""></td>
	   <td><input style="width:80px;" disabled  type="number" name="items[<?php echo $inc ?>][allcost]" class="allcost" value="" ></td>
    </tr>
	<?php }
  ?>
  </tbody>
</table><br>
    <strong>Todays Total No of items :</strong><input readonly type="number" name="totalitms" id="totalitms"><br>
    <strong>Todays Total Quantity (kg):</strong><input readonly type="number" name="totalQty" id="totalQty"><br>
    <strong>Todays Total Purchased Amount (₹):</strong><input readonly type="number" name="totalAmount" id="totalAmount"><br><br>

    Date: <input type="text" readonly name="date" value="<?php echo date('d-m-Y') ?>"><br><br>

    <div id="hiddenFields"></div>

    <button id="combineAndSubmit" type="button">Submit Purchase</button>


</form></div>

<div class="modal fade " id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
								</div>
								<div class="modal-body">
									<img src="" id="imagepreview" style="width: 100%; height: 100%;" class="img-fluid">
								</div>
							</div>
						</div>
				</div>
				
				
<div class="modal fade" id="delete_item" tabindex="-1" role="dialog" aria-labelledby="delete_item" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content" >
							<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
								<div class="modal-body">
									<input autocomplete="off" type="hidden" id="del_item" name="del_item">
									<input autocomplete="off" type="text" id="del_cmp_id" name="del_cmp_id">
									<h5 class="h5-responsive text-center">Are you sure that you want to delete this??</h5>
								</div>	
								<div class="modal-footer">
									<div class="row">
										<div class="col-md-12">
												<button type="button" class="btn btn-secondary btn-sm clear" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary btn-sm">Delete</button>
										</div>
									</div>	
								</div>
							</form>
						</div>
					</div>	
				</div>
    </div>
    </div>
  </div> <?php } ?>
<?php require_once('footer.php'); ?>
  <script>
  $(function(){
	//$('#table').DataTable({
    //"pageLength": 10,
	//order: []
//});
	$("#table_wrapper select").addClass("browser-default custom-select");
	$(".custom-select").css("width", "40%" );
});
    $(document).ready(function() {
	
	
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	 
	  
    });
	
	
	$(document).on('click','.deleteitemdata',function(e)
	{
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
		
	});
    $(document).on('input','.todayQty,.todayPrice',function(e)
	{
		recalculate();
		
	});

    function recalculate()
    {
        let totalitems = 0;
        let totalQty = 0;
        let totalAmount = 0;
        $('#purchaseTable tbody tr').each(function() { 
            const qty = parseFloat($(this).find('.todayQty').val()) || 0;
            const pric = parseFloat($(this).find('.todayPrice').val()) || 0;
            const total = qty * pric;
          //  $(this).find('.allcost').val(total);
            if (!isNaN(total)) {
                $(this).find('.allcost').val(total.toFixed(2));
                totalQty += qty;
                totalAmount += total;
               // totalitems += 1;
            }
            if (qty > 0 || pric > 0) {
                 totalitems += 1;
            } 
            });
            
                 //alert(totalQty);  
                 $('#totalitms').val(totalitems);
                $('#totalQty').val(totalQty.toFixed(2));
                   $('#totalAmount').val(totalAmount.toFixed(2));  
    }
	
	
$(document).on('click', '#recoverYesterday', function() {
    $.ajax({
        url: 'get_yesterday_purchase.php',
        type: 'POST',
        dataType: 'json',
        data: { account_id: <?php echo $accountId; ?> }, // Pass the account ID
        success: function(res) { 

            let form = $('#yesterdayForm');
            form.empty(); // Clear old table if any

            // Start building the table HTML
            let tableHtml = `
                <h4>Remaining items from Yesterday's purchase:</h4>
                <table id="yesterdayTable" border="5" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Append rows dynamically
            res.forEach(function(item) {
                tableHtml += `
                    <tr style="background-color: #f3d369ff">
                        <td>${item.item_id}</td>
                        <td>${item.item_name}/${item.kannada_name}
                            <input type="hidden" class="yesterdayItemId" value="${item.item_id}">
                        </td>
                        <td>
                            <input type="number" readonly class="yesterdayQty" value="${item.remaining_stock}">
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdayPrice" value="${item.price_per_kg}" readonly>
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdayPrice" value="${item.selling_price}" readonly>
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdaytotcost" value="${(item.price_per_kg * item.remaining_stock).toFixed(2)}" readonly>
                        </td>
                    </tr>
                `;
            });

            // Close the table
            tableHtml += `
                    </tbody>
                </table>
            `;

            // Append table to form
            form.append(tableHtml);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching yesterday's purchase:", error); 
        }
    });
});

$(document).on('click', '#combineAndSubmit', function() {

//e.preventDefault(); // stop immediate submit

    let combinedData = [];

    // Get yesterday's data
    $('#yesterdayTable tbody tr').each(function () {
        //alert ("Processing yesterday's data...");
       // alert($(this).find('.yesterdayItemId').val() + " " + $(this).find('.yesterdayQty').val() + " " + $(this).find('.yesterdayPrice').val());
        combinedData.push({
            id: $(this).find('.yesterdayItemId').val(),
            qty: parseFloat($(this).find('.yesterdayQty').val()) || 0,
            price: parseFloat($(this).find('.yesterdayPrice').val()) || 0
        });
    });
   //  Merge today's data
    $('#purchaseTable tbody tr').each(function () {
        let id = $(this).find('.itemId').val();
        let todayQty = parseFloat($(this).find('.todayQty').val()) || 0;
        let todayPrice = parseFloat($(this).find('.todayPrice').val()) || 0;
       // alert(todayQty + " " + todayPrice + " " + id);

        let existing = combinedData.find(item => item.id == id && item.qty > 0);
        if (existing) {
            let totalQty = existing.qty + todayQty;
            if (todayPrice > 0) {
                existing.price = todayPrice; // Average price
            } else {
                existing.price = existing.price; // Keep existing price if no new price
            }
            existing.qty = totalQty;
            
        } else {
           // alert("New Item: " + id + " " + todayQty + " " + todayPrice);
            combinedData.push({
                id: id,
                qty: todayQty,
                price: todayPrice
            });


        }
    });   
    
    // alert(combinedData.length);
     //  Clear old hidden fields
    $('#hiddenFields').empty();

     //  Create hidden inputs for each item
    combinedData.forEach((item, index) => {
        $('#hiddenFields').append(`
            <input type="text" name="items[${index}][id]" value="${item.id}">
            <input type="text" name="items[${index}][quantity]" value="${item.qty}">
            <input type="text" name="items[${index}][price]" value="${item.price}">
            <br>
        `);
    });

    // Calculate totals
   // let totalItems = combinedData.length;  // Number of unique items
    let validItems = combinedData.filter(item => item.qty > 0 && item.price > 0);
    let totalItems = validItems.length; // Number of items with qty and price
    let totalQty = 0;
    let totalCost = 0;

    combinedData.forEach(item => {
        totalQty += item.qty;
        totalCost += item.qty * item.price; // qty × price for each item
    });

    //alert("Total Items: " + totalItems + "\nTotal Quantity: " + totalQty.toFixed(2) + "\nTotal Cost: " + totalCost.toFixed(2));
            // // Fill main fields
            $('#hiddenFields').append(`
            <input type="text" name="totalQty"  value="${totalQty}">
            <input type="text" name="totalitms" value="${totalItems}">
            <input type="text" name="totalAmount" value="${totalCost.toFixed(2)}">
            <br>
        `);

	//  Submit the form normally
    $(this).closest('form')[0].submit();



});




  </script>
  
</body>
</html>

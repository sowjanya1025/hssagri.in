<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

//$locations =$account->getAllApartmentsData();
//$itemdata = $account->getInventoryData($accountId);
$itemdata = $account->getAllPurchaseData($accountId);

//print_r($itemdata);
if(!empty($_POST))
{
    if(isset($_POST['sellingprices'])=='sellingprices')
    //  if (isset($_POST['sellingprices']) && $_POST['sellingprices'] === 'sellingprices') 
	{
        $date = date('Y-m-d');

        foreach($_POST['items'] as $items)
        {
                $purchase_items_id = $items['purchase_items_id'];
                $profit = $items['profit'];
                $sellingprice = $items['sellingprice'];
                $account->create_selingPrice($accountId,$purchase_items_id,$profit,$sellingprice,$date); 

        }
                header("Location:selling_form.php?act=1");
                exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php require_once('header.php'); ?>
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

  <title>Selling Form</title>
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
      <div id="carbon-block" class="my-3"></div>
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
	  
    <div class="container">
	
	<div class="table-responsive">
	     <?php if ($itemdata) { ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="sellingprices" value="sellingprices">
  <div class="form-group">
    <label for="date">Date</label>
    <input type="hidden" name="date" value="<?php echo date('Y-m-d');?>">
    <strong><?php echo date('d-m-Y');?></strong>
  </div>
  <table border='1' id='salesform' cellpadding='5'>
  <thead class='thead-dark'>
  <tr>
  	  <th>S.No</th>
	  <th>Name of Item</th>
	  <th>Purchased Price Per kg/piece/kattu</th>
	  <th>% of profit</th>
	  <th>Selling Price for kg/piece/kattu</th>
	  </tr>
  </thead>
  <tbody>
  <?php 
  // Check if any row has non-empty profit_percentage and selling_price
  // so that we can decide whether to apply profit % to all rows or not
  $isFromDB = false;

foreach ($itemdata as $row) {
    if (!empty($row['profit_percentage']) && !empty($row['selling_price'])) {
        $isFromDB = true; // at least one row has saved values
         break; // no need to check further
    }
}
// end 
  $i = 1;
 $index = 0;
  foreach ($itemdata as $itemdata)
            {
			
			  ?>
		<tr>
	<td><?php echo $itemdata['id_no']; ?></td>
	  <td><?php echo htmlspecialchars($itemdata['item_name']) ?>/<?php echo htmlspecialchars($itemdata['kannada_name']) ?></td>
      <td><input type="hidden" value="<?php echo $itemdata['id']?>" name="items[<?php echo $index ?>][purchase_items_id]">
	  <input style="width:80px;" type="number" class="priceforqty" name="items[<?php echo $index ?>][price_per_kg]" 
	  readonly value="<?php echo $itemdata['price_per_kg'] ?>"></td>
	  <td><input style="width:80px;" step="any" required type="text" class="profit" name="items[<?php echo $index ?>][profit]" value="<?php echo $itemdata['profit_percentage'] ?>"></td>
	  <td><input style="width:80px;"  step="any"  type="number" class="sellingprice" name="items[<?php echo $index ?>][sellingprice]"   value="<?php echo $itemdata['selling_price'] ?>"></td>
    </tr>
	<?php $i++; $index++; } ?>
  </tbody>
</table>
  
  <div id="itemTableArea"></div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
	<?php } else { echo "No data is available"; } ?>

	


        </div>

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
  </div>
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
function roundToNearestFive(num) {
    return Math.round(num / 5) * 5;
}
$(document).ready(function() {
    let firstTimeApplied = <?php echo $isFromDB ? 'false' : 'true'; ?>;
    let typingTimer;  
    const typingDelay = 1500; // 0.5s delay after typing stops

    $('.profit').on('input', function() {
        let inputField = $(this); // store current input field

        clearTimeout(typingTimer); // reset timer on every keystroke
        typingTimer = setTimeout(function() {
            var index = $('.profit').index(inputField); 
            var profit = parseFloat(inputField.val()) || 0;

            // calculate selling price for current row
            var price = parseFloat($('.priceforqty').eq(index).val()) || 0;
            var selling = price + (price * profit / 100);
             // round to nearest 5
            $('.sellingprice').eq(index).val(roundToNearestFive(selling));

            if (firstTimeApplied) {
                // first time â†’ apply to all rows
                $('.profit').each(function(i) {
                    if (i !== index) {
                        $(this).val(profit);
                        var priceEach = parseFloat($('.priceforqty').eq(i).val()) || 0;
                        var sellingEach = priceEach + (priceEach * profit / 100);
                       // $('.sellingprice').eq(i).val(sellingEach.toFixed(2));
                        $('.sellingprice').eq(i).val(roundToNearestFive(sellingEach));
                    }
                });
                firstTimeApplied = false; // mark that global update is done
            }
        }, typingDelay); // delay after user stops typing
    });
});
  </script>
</body>
</html>

<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$locations =$account->getAllApartmentsData();
//$itemdata = $account->getInventoryData($accountId);
if(!empty($_POST))
{
    if(isset($_POST['createsales'])=='createsales')
	{
        $date = $_POST['date']; 
        $profit_loss = $_POST['profit_loss']; 
        //$apartmentname = isset($_POST['apartmentname'])? $_POST['apartmentname'] : NULL;
        $cash = isset($_POST['cash'])? $_POST['cash'] : 0;
        $onlinepay = isset($_POST['onlinepay'])? $_POST['onlinepay'] : 0;
        $scanner = isset($_POST['scanner'])? $_POST['scanner'] : 0;
        $apt_id = isset($_POST['aptid'])? $_POST['aptid'] : NULL;
        $acct_id = isset($_POST['acctid'])? $_POST['acctid'] : NULL;
       // $total_revenue = $cash + $online + $scanner;
        // if all are not empty  then insert into db
        if(!empty($cash) && !empty($onlinepay) && !empty($scanner))
        {
            $account->create_apartment_revenue($acct_id,$apt_id,$cash,$onlinepay,$scanner,$profit_loss,$date);
        }
       
        foreach($_POST['items'] as $items)
        {
                 $id_no=$items['id_no'];
                $item_id = $items['item_id'];
                $purchase_items_id = $items['purchase_items_id'];
                $location_id = $items['location_id'];
                $sellingprice = $items['sellingprice'];
                $soldqty = $items['soldqty'];
                $quantity_remaining = $items['remainingqty'];
                $ovrallturnover = $items['ovrallturnover'];
                $account->create_sales($item_id,$purchase_items_id,$location_id,$sellingprice,$soldqty,$quantity_remaining,$ovrallturnover,$accountId,$date,$profit_loss); 

        }
                header("Location:sales_form.php?act=1");
                exit;
         // insert into db

    }

}

?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

  <title>Sales Form</title>
  <style>
	body { background-color: #fafafa; }
	.redtext { color: red; }
	.greentext { color: green; }
.summary table {
    width: 50%;
    border-collapse: collapse;
    margin-top: 10px;
    font-family: Arial, sans-serif;
}

.summary th, .summary td {
    border: 1px solid #ccc;
    padding: 8px 12px;
}

.summary th {
    background-color: #f4f4f4;
    text-align: left;
    width: 60%;
}
.summary td {
    width: 40%;
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
    <form method="post" id="salesfrmsubmit" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="createsales" value="createsales">
  <div class="form-group">
    <label for="date">Date</label>
    <input type="hidden" name="date" value="<?php echo date('Y-m-d');?>">
    <strong><?php echo date('d-m-Y');?></strong>
  </div>
  <div class="form-group">
    <label for="aptaddress">Name of the Apartment</label>
    <select class="form-control" id="aptaddress" name="apartmentname" required>
            <option value="">Select Apartment</option>
            <?php foreach ($locations as $location)
            { ?>
                <option value=<?php echo $location['id'] ?>><?php echo $location['apartment_name'] ?></option>
          <?php   } ?>
            
    </select>
  </div>
  <div id="itemTableArea"></div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
	


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
    $(document).on('change','#aptaddress',function(){

        let aptid = $(this).val();
        let acctid = <?php echo $accountId; ?>;
       // alert(acctid);
        $.ajax({
            url:"fetch_items_sales.php",
            type:"POST",
            data:{'aptid':aptid,'acctid':acctid},
           // dataType:"json",
            success:function(response){
                //alert(response);
                $('#itemTableArea').html(response);

                

            }
        });
    });

    $(document).on('change', '.remainingqty', function(e) {
    const $row = $(this).closest('tr');
    const remqty = parseFloat($(this).val()) || 0;
    const quantityatloc = parseFloat($row.find('.quantityatloc').val()) || 0;

    if (remqty > quantityatloc) {
        alert("⚠ Remaining Quantity cannot be greater than Qty at Location.");
        $(this).val('');
        $(this).focus();
    }
});


$(document).on('change', '.remainingqty', function(e) {
    $('#salesform tbody tr').each(function() {
        const $row = $(this);
        const remqty = parseFloat($row.find('.remainingqty').val());
        const quantityatloct = parseFloat($row.find('.quantityatloc').val()) || 0;
        const sellprice = parseFloat($row.find('.sellingprice').val()) || 0;
        const purchaseprice = parseFloat($row.find('.priceperkgfrompurchases').val()) || 0;

        // Reset sold qty and turnover first
        $row.find('.soldqty').val(0);
        $row.find('.ovrallturnover').val(0);

        // If remaining qty is a valid number AND within range
        if (!isNaN(remqty) && remqty >= 0 && remqty <= quantityatloct) {
            const soldqty = quantityatloct - remqty;
            $row.find('.soldqty').val(soldqty);

            if (sellprice > 0 && soldqty > 0) {
               // const turnover = (sellprice - purchaseprice) * soldqty;
               const turnover = sellprice * soldqty;
                $row.find('.ovrallturnover').val(turnover.toFixed(2));
            }
        }
    });

    calculateSummary(); // Update totals
});

  function calculateSummary() {
    let totalItems = 0;
    let totalQtyLoc = 0;
    let totalSold = 0;
    let totalUnsold = 0;
    let totalTurnover = 0;
    let profitLoss = 0;
    let totalinvestamt = 0;
    let totalPurchasePrice = 0;
    let totalSellingPrice = 0;

    $('#salesform tbody tr').each(function () {
        totalItems++;
        totalPurchasePrice += parseFloat($(this).find('.priceperkgfrompurchases').val()) || 0;
        totalSellingPrice += parseFloat($(this).find('.sellingprice').val()) || 0;
        totalQtyLoc += parseFloat($(this).find('.quantityatloc').val()) || 0;
        totalSold += parseFloat($(this).find('.soldqty').val()) || 0;
        totalinvestamt += parseFloat($(this).find('.priceperkgfrompurchases').val()) * parseFloat($(this).find('.quantityatloc').val());
        totalUnsold += parseFloat($(this).find('.remainingqty').val()) || 0;
        totalTurnover += parseFloat($(this).find('.ovrallturnover').val()) || 0;
       // alert(totalinvestamt);
        
    });

    // Read misc value from a separate field (outside loop)
    let misc = parseFloat($('#misc').val()) || 0;

    //profitLoss = totalTurnover - 0; // msc is hard coded to 0 // for now
    profitLoss = totalTurnover - totalinvestamt; // Adjusted to include misc


    //alert(totalItems);
   // $('#total_items').val(totalItems);
       // Update the summary table

    $('#total_qty_location').val(totalQtyLoc);
    $('#total_sold_qty').val(totalSold); 
    $('#total_invest_amt').val(totalinvestamt);
   // $('#total_unsold_qty').val(totalUnsold); 
    $('#total_turnover').val(parseFloat(totalTurnover).toFixed(2)); // Ensure turnover is formatted to 2 decimal places
    $('#profit_loss').val(parseFloat(profitLoss).toFixed(2)); // Ensure profit_loss is formatted to 2 decimal places
    
    // these are below the table tr 
     $('#tbl_total_purprice').val(totalPurchasePrice);
     $('#tbl_total_qtyloc').val(totalQtyLoc);
     $('#tbl_total_sellingprice').val(totalSellingPrice);
     $('#tbl_total_remqty').val(totalUnsold);
     $('#tbl_total_soldqty').val(totalSold);
     $('#tbl_total_turnover').val(totalTurnover);
}

/*$(document).ready(function() {
    $("#salesfrmsubmit").on("submit", function(e) {
        let totalRevenue = parseFloat($("#total_turnover").val()) || 0;
        let cash = parseFloat($("#cash").val()) || 0;
        let onlinepay = parseFloat($("#onlinepay").val()) || 0;
        let scanner = parseFloat($("#scanner").val()) || 0;

        let sum = cash + onlinepay + scanner;
        // Reset borders first
        $("#cash, #onlinepay, #scanner").css("border", "1px solid #ccc");
        // Allow tolerance of ±1
        if (Math.abs(sum - totalRevenue) > 1) {
            e.preventDefault(); // stop form submission
            // Highlight the inputs in red
            $("#cash, #onlinepay, #scanner").css("border", "2px solid red");
            alert("The sum of Cash + onlinepay + Scanner must equal Total Revenue (" + totalRevenue + ").\nCurrently: " + sum);
            return false;
        }
    });
}); */
</script>

  </script>
</body>
</html>

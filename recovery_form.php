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
//$itemdata = $account->getAllPurchaseData($accountId);
$itemdata = $account->get_purchase_items_forrecovery();

//print_r($itemdata);
if(!empty($_POST))
{
    if(isset($_POST['recoveryform'])=='recoveryform')
	{
        $date = date('Y-m-d');
        // Check if today's purchase already exists
        $todaypurchase = $account->get_purchase_by_date($accountId, $date);
         if (!$todaypurchase) 
          {
        // No purchase entry → show message and stop
        echo "<script>alert('Firstly,Please fill today\'s purchase form');window.location='recovery_form.php';</script>";
        exit;
        }
        if ($todaypurchase['recovery_done'] == 1)
           {
                echo "<script>alert('Recovery already submitted for today!'); window.location='recovery_form.php';</script>";
                exit;
            } 
       $purchase_id = $todaypurchase['id'];
        foreach($_POST['items'] as $items)
        {
                $purchase_items_id = $items['id'];
                $qty = (float) $items['quantity'];
                $price = (float) $items['price'];
                $total = $qty * $price;

                $account->insert_recovery_yesterday($purchase_id,$purchase_items_id,$qty,$price,$total); 

        }
                header("Location:recovery_form.php?act=1");
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

  <title>Recovery Form</title>
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
	<input type="hidden" name="recoveryform" value="recoveryform">
  <div class="form-group">
    <label for="date">Date</label>
    <input type="hidden" name="date" value="<?php echo date('Y-m-d');?>">
    <strong><?php echo date('Y-m-d');?></strong>
  </div>
  <table border='1' id='salesform' cellpadding='5'>
  <thead class='thead-dark'>
  <tr>
                            <th>S.No</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Total Cost</th>
  </thead>
  <tbody>
  <?php 
  $i = 1;
 $index = 0;

  foreach ($itemdata as $itemdata)
            {
                $tot = round(($itemdata['price_per_kg'] * $itemdata['remaining_stock']), 2);
			  ?>
		<tr>
		<td><?php echo $itemdata['id_no']; ?></td>
	  <td>      <input type="hidden" class="itemId" name="items[<?php echo $index ?>][id]" value="<?php echo  $itemdata['item_id'] ?>">
<?php echo htmlspecialchars($itemdata['item_name']) ?>/<?php echo htmlspecialchars($itemdata['kannada_name']) ?></td>
      <td><input style="width:80px;" type="number" readonly class="yesterdayQty" name="items[<?php echo $index ?>][quantity]" 
	   value="<?php echo $itemdata['remaining_stock'] ?>"></td>
     <td><input style="width:80px;" type="number" readonly class="yesterdayPrice" name="items[<?php echo $index ?>][price]" 
	   value="<?php echo $itemdata['price_per_kg'] ?>"></td>
     <td><input style="width:80px;" type="number" readonly class="yesterdayPrice" name="items[<?php echo $index ?>][selling_price]" 
	   value="<?php echo $itemdata['selling_price'] ?>"></td>
     <td><input style="width:100px;" type="number" readonly class="yesterdaytotcost" name="items[<?php echo $index ?>][allcost]" 
	   value="<?php echo $tot; ?>"></td>
    </tr>
	<?php $i++; $index++; } ?>
  </tbody>
</table>
  
  <div id="itemTableArea"></div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
  <?php } else { echo "No items available for recovery."; } ?>


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
  </script>
</body>
</html>

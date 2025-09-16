<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$itemdata = $account->getGoods_SupplyBill($accountId);
?>
<!doctype html>
<html lang="en">
<head>
<?php require_once('header.php'); ?>
  <title>GSB Approved-Rejected</title>
  <style>
    body { background-color: #fafafa;   .redtext{ color: red; .greentext{ color: green;} 
	a[disabled] {
   pointer-events: none;
   cursor: default;
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
	   <h3>Approved-Rejected GSB</h3>
      <div id="carbon-block" class="my-3"></div>
	  	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='del')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Item Deleted</span></b></div>
	  <?php }  } ?>
    <div class="container">
	<div class="table-responsive">
        <table class="table hover" id="table"> <!-- stripe-->
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
	  <th scope="col">Client name</th>
	  <th scope="col">Item name</th>
      <th scope="col">Code</th>
	  <th scope="col">Action</th>
	  <th scope="col">print</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($itemdata as $cdata) 
	{
	  $inc++; 
	?>
	   <tr>
      <th scope="row"><?php echo $inc; ?></th>
	  <td><?php echo htmlspecialchars($cdata['clients_name']); ?></td>
	  <td><?php echo htmlspecialchars($cdata['item_name']); ?></td>
	   <td><?php echo htmlspecialchars($cdata['item_code']); ?></td>
	  
	  <?php 
	  		$state = "";
	  		if ($cdata['approval_status'] == 1)
	  			{
	  				$status = "<span style='color:green'>Approved</span>";
					$state = "";
					$pointerstate = "";
				}
			elseif($cdata['approval_status'] == 0)
				{
					$status = "<span style='color:red'>Rejected</span>";
					$state = "disabled";
					$pointerstate = "style='pointer-events: none'";
				}
			else
				{
					$status = "<span style='color:Blue'>Pending</span>";
					$state = "disabled";
					$pointerstate = "style='pointer-events: none'";
				}
		?>
		<td><?php echo $status; ?></td>
	    <td><a href="printpreview_gsb.php?id=<?php echo $cdata['id']; ?>" <?php echo $pointerstate; ?>><input type="button" name="print" <?php echo $state; ?> value="print"  ></a></td>
    </tr>
	<?php }
  ?>
  </tbody>
</table></div>

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
									<input autocomplete="off" type="hidden" id="del_cmp_id" name="del_cmp_id">
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
	$('#table').DataTable({
    "pageLength": 10,
	order: []
});
	$("#table_wrapper select").addClass("browser-default custom-select");
	$(".custom-select").css("width", "40%" );
});
    $(document).ready(function() {
	
	
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	 $('.deleteitemdata').click(function() { 
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
 
	 });
	 
 $(".printbtn").on('click',function() {
      // Different data to be printed, not visible on the page
	  var name = $(this).attr('id1');
	  var code = $(this).attr('id2');
	  var qty = $(this).attr('id3');
	  var price = $(this).attr('id4');
	  var tot = $(this).attr('id5');
	  
      var printData = '<h2>Goods Details</h2><p><strong>Farmer Name:</strong>'+name+'</p> <p><strong>Item Code:</strong>'+code+'</p> <p><strong>Item Quantity:</strong> '+qty+'</p><p><strong>Item Price:</strong>'+price+'</p> <p><strong>Total Amount:</strong>'+tot+'</p>';
    
      // Create a new window for printing
      var printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print Section</title>');
      
      // Add optional styling for the print window
      printWindow.document.write('<style>');
      printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
      printWindow.document.write('#printableSection { font-size: 20px; padding: 20px; border: 2px solid black; border-radius: 10px; margin: 0 auto; width: 80%; box-sizing: border-box; }');
      printWindow.document.write('</style>');

      // Insert the content to be printed
      printWindow.document.write('</head><body>');
      printWindow.document.write('<div id="printableSection">');
      printWindow.document.write(printData);  // Dynamically insert the different data
      printWindow.document.write('</div>');
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();
      
      // Trigger the print
      printWindow.print();
      printWindow.close();
      });	  
    });
  </script>
</body>
</html>

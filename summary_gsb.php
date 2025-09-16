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
//print_r($itemdata);

		if(isset($_POST['del_item']))
	{
		$del_id = isset($_POST['del_cmp_id']) ? $_POST['del_cmp_id'] : '';
		if($del_id!="")
		{
			$account->deleteGoods_SupplyBill($del_id);
			header("Location:summary_gsb.php?act=del");
			exit;
		} 
		
	}

?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

  <!-- cdnjs.com / libraries / fontawesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <!-- Option 1: Include in HTML -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
  <!-- js validation scripts -->
	<!-- end js validation scripts --> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" charset="utf-8"></script>

  <!-- css ekternal -->
  <link rel="stylesheet" href="css/style.css">
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css"> <!-- for calendar-->

  <title>GSB Summary Page</title>
  <style>
    body { background-color: #fafafa;   .redtext{ color: red; .greentext{ color: green;} 
 }
  .table-fixed {
    table-layout: fixed;
    width: 100%;
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
	   <h3>GSB Summary</h3>
      <div id="carbon-block" class="my-3"></div>
	  	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='del')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Item Deleted</span></b></div>
	  <?php }  } ?>
    <div class="container">
	<form class="form-inline" action="gsb_date_search.php" method="post">
  <input type="text" class="datepicker" name="fromdate" id="fromdate" placeholder="From date" required>
  <input class="datepicker" type="text" name="todate" id="todate" placeholder="To date" required>
   <div class="form-check">
    <label class="form-check-label">
       <button type="submit" class="btn btn-primary">Submit</button>
    </label>
  </div>
</form>
<br>
<form action="excel_gsb_ledger.php" method="post">
<div class="form-row">
    <div class="form-group col-md-2">
					<select class="form-control" name="clientlist" id="clientlist" required oninput="setclientstype()">
					<option value="" disabled="disabled" selected="selected">-Select ClientType-</option>
							<option value="1">ModernTraders</option>
							<option value="2">OracaTraders</option>
							<option value="3">GeneralTraders</option>
							<option value="4">RetailTraders</option>
				    </select>
    </div>
    <div class="form-group col-md-2">
			<select class="form-control" name="names_list" id="names_list" required oninput="setclientsname()">
			</select>
    </div>
	<div class="form-group col-md-2">
	  <button type="submit" class="form-control btn btn-primary">Submit</button>
    </div>
  </div></form>

	<div class="table-responsive">
        <table class="table table-fixed hover" id="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col" style="width: 5%;">#</th>
      <th scope="col" style="width: 15%;">Clients Type</th>
	   <th scope="col" style="width: 15%;">Clients name</th>
      <th scope="col" style="width: 30%;">Item name</th>
<!--      <th scope="col" style="width: 10%;">Code</th>
-->      <th scope="col" style="width: 10%;">Bill Number</th>
      <th scope="col" style="width: 10%;">Total Amount</th>
      <th scope="col" style="width: 10%;">Date</th>
      <th scope="col" style="width: 10%;">Delete</th>
      <th scope="col" style="width: 10%;"><i class="bi bi-download"></i></th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($itemdata as $cdata) 
	{
	  $inc++; 
	  date_default_timezone_set('Asia/Kolkata');
	  $grnregdate = date('F j, Y g:i A', strtotime($cdata['regdate']));
	   $clientType = $cdata['client_type'];
	   switch ($clientType) {
        case '1':
            $list = "modern";
            break;
        case '2':
            $list = "oraca";
            break;
        case '3':
           $list = "general";
            break;
        case '4':
            $list = "retail";
            break;
           }

	?>
     <tr>
        <th scope="row" style="width: 5%;"><?php echo $inc; ?></th>
		<td style="width: 10%;"><?php echo $list; ?></td>
        <td style="width: 10%;"><?php echo htmlspecialchars($cdata['clients_name']); ?></td>
        <td style="width: 25%;"><?php echo htmlspecialchars($cdata['item_name']); ?></td>
<?php /*?>        <td style="width: 10%;"><?php echo htmlspecialchars($cdata['item_code']); ?></td>
<?php */?>        <td style="width: 10%;"><?php echo htmlspecialchars($cdata['billnumber']); ?></td>
        <td style="width: 10%;"><?php echo htmlspecialchars($cdata['totamt']); ?></td>
        <td style="width: 10%;"><?php echo $grnregdate; ?></td>
        <td style="width: 10%;">
            <input type="button" name="delete" value="Delete" data-target='#delete_item' data-toggle='modal' 
                   class="deleteitemdata" id="<?php echo $cdata['id'] ?>">
        </td>
	 <td style="width: 10%;"><a href="excel_gsb.php?id=<?php echo $cdata['id']; ?>"><i class="bi bi-download"></i><a></td>

    </tr>
  <?php } ?>
  </tbody>
</table>
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
  <!-- wrapper and -->
  <!-- Option 2: jQuery, Popper.js, and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<!--https://www.geeksforgeeks.org/form-validation-using-jquery/--> <!--// jquery validation code download-->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
  <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script><!--// jquery for calendar-->
  <script>
  
  		function setclientsname()
		{
				    var selectElement = document.getElementById("names_list");
					var selectedText = selectElement.options[selectElement.selectedIndex].text;
					document.getElementById("clientsname").value = selectedText;
		} 
		function setclientstype()
		{
				    var selectElement = document.getElementById("clientlist");
					var selectedText = selectElement.options[selectElement.selectedIndex].text;
					document.getElementById("clienttype").value = selectedText;
		} 



    $( function() {
     $(".datepicker" ).datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			yearRange: '1970:'+ new Date().getFullYear().toString()
		});	 
		
		
  } ); //  calendar
  
		



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
	 
	 
	 
	 	  $('#clientlist').on('change',function()
	  {
	  //	alert("here");
		let clid = $(this).val();
		//alert(clid);
		$.ajax({
			type:"post",
			url:"getClientList.php",
			data:{clientid:clid},
			success:function(response)
			{ // alert(response);
				 $('#names_list').empty();
				$('#names_list').append(response);
			   }
		});
	  });

	 
	  
    });
  </script>
</body>
</html>

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
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="saleslist" value="saleslist">
  <div class="form-group">
    <label for="date">Date</label>
    <input type="date" name="date" value="<?php echo date('Y-m-d');?>">
  </div>
  <div class="form-group">
    <label for="aptaddress">Name of the Apartment</label>
    <select class="form-control" id="aptaddress" name="apartmentname">
            <option value="">Select Apartment</option>
            <?php foreach ($locations as $location)
            { ?>
                <option value=<?php echo $location['id'] ?>><?php echo $location['apartment_name'] ?></option>
          <?php   } ?>
            
    </select>
  </div>
  <div id="itemTableArea"></div>



</form>
	


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
    $(document).on('change','#aptaddress',function(){

        let aptid = $(this).val();
        let acctid = <?php echo $accountId; ?>;
        let dat = $('input[name="date"]').val();
       // alert(acctid);
        $.ajax({
            url:"fetch_saleslist.php",
            type:"POST",
            data:{'aptid':aptid,'dat':dat},
           // dataType:"json",
            success:function(response){
                //alert(response);
                $('#itemTableArea').html(response);

                

            }
        });
    });

    

  

	
  </script>
</body>
</html>

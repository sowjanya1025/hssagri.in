<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

if(!empty($_POST))
{
	if(isset($_POST['createapartment'])=='createapartment')
	{
		$aptname = isset($_POST['apartmentname'])? $_POST['apartmentname'] : NULL;
		$aptddress= isset($_POST['apartmentaddress'])? $_POST['apartmentaddress'] : NULL;
		$aptlocation = isset($_POST['apartmentlocation'])? $_POST['apartmentlocation'] : NULL;
	    $account->create_apartment($aptname,$aptddress,$aptlocation,$accountId);  // insert into db
		header("Location:add_apartments.php?act=1");
	   exit;
		
	}
	
}
if(isset($_POST['del_item']))
	{
		$del_id = isset($_POST['del_cmp_id']) ? $_POST['del_cmp_id'] : '';
		if($del_id!="")
		{
			$account->delete_Apartment_ById($del_id);
			header("Location:add_apartments.php?act=del");
			exit;
		} 
		
	}
$apartment_list = $account->getAllApartmentsData();
//print_r($apartment_list);
?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

  <title>Add Apartments</title>
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
	
	<h3>Add Apartment</h3>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="createapartment" value="createapartment">
  <div class="form-group">
    <label for="aptname">Apartment Name</label>
    <input type="text" class="form-control" name="apartmentname" id="aptname" placeholder="Enter Apartment Name" required>
  </div>
  <div class="form-group">
    <label for="aptaddre">Apartment Address</label>
    <input type="text" class="form-control" id="aptaddre" name="apartmentaddress" placeholder="Enter Apartment Address">
  </div>
  <div class="form-group">
    <label for="aptgmap">Google Location</label>
    <input type="text" class="form-control" id="aptgmap" name="apartmentlocation"  placeholder="Enter Apartment Google Location">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
	
	
	
	<div class="table-responsive">
        <table class="table hover" id="table"> <!-- stripe-->
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Code</th>
	  <th scope="col">Name</th>
	  <th scope="col">Google Location</th>
	  <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($apartment_list as $cdata) 
	{
	 $inc++; 
	
	?>
	    <tr>
      <th scope="row"><?php echo $inc; ?></th>
	  <td><?php echo htmlspecialchars($cdata['apartment_name']); ?></td>
      <td><?php echo $cdata['apartment_address']; ?></td>
	  <td><a href="<?php echo $cdata['google_location'] ?>"><?php echo $cdata['google_location']; ?></a></td>
	   <td><input type="button" name="delete" value="Delete" data-target='#delete_item' data-toggle='modal' 
		class="deleteitemdata" id="<?php echo $cdata['id'] ?>"></td>
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
	 
	  
    });
	
	
	$(document).on('click','.deleteitemdata',function(e)
	{
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
		
	});
  </script>
</body>
</html>

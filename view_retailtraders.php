<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$clientdata = $account->getClient_OnBoardingData($accountId,$type=4);
 if(isset($_POST['del_client']))
	{
		$del_id = isset($_POST['del_clt_id']) ? $_POST['del_clt_id'] : '';
		if($del_id!="")
		{
			$account->delete_Client_ById($del_id);
			header("Location:view_retailtraders.php?act=del");
			exit;
		} 
	}
?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <title>View Page</title>
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
	  <h3>RetailsTraders List</h3>
      <div id="carbon-block" class="my-3"></div>
	  	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='del')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Client details Deleted</span></b></div>
	  <?php }  } ?>
    <div class="container">
	<div class="table-responsive">
        <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Code</th>
	  <th scope="col">Name</th>
      <th scope="col">Mobile</th>
      <th scope="col">Email</th>
	  <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($clientdata as $fdata) 
	{ $inc++; 
	
	?>
	    <tr>
      <th scope="row"><?php echo $inc; ?></th>
	  <td><?php echo $fdata['code'] ?></td>
      <td><?php echo $fdata['name'] ?></td>
      <td><?php echo $fdata['mobile'] ?></td>
      <td><?php echo $fdata['email'] ?></td>
	    <td><input type="button" name="delete" value="Delete" data-target='#delete_client' data-toggle='modal' 
		class="deleteclientdata" id="<?php echo $fdata['id'] ?>">&nbsp;<a href="edit_client_onboarding.php?id=<?php echo $fdata['id'] ?>"><input type="button" name="edit" value="Edit"></a></td>
    </tr>

	<?php }
  
  
  ?>
    
  </tbody>
</table></div>
<div class="modal fade" id="delete_client" tabindex="-1" role="dialog" aria-labelledby="delete_client" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content" >
							<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
								<div class="modal-body">
									<input autocomplete="off" type="hidden" id="del_client" name="del_client">
									<input autocomplete="off" type="hidden" id="del_clt_id" name="del_clt_id">
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
    </div><a href="client_onboarding_view.php"><input type="button" name="Back" value="Back" class="btn btn-dark"></a>
    </div>

  </div>
<?php require_once('footer.php'); ?>
  <script>
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	 $('.deleteclientdata').click(function() { 
		let ccid = $(this).attr('id');
		$('#del_clt_id').val(ccid);
	 }); 
	  
    });
  </script>
</body>
</html>

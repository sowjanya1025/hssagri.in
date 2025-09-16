<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$farmerdata = $account->getfarmer_OnBoardingData($accountId);
 if(isset($_POST['del_farmer']))
	{
		$del_id = isset($_POST['del_frm_id']) ? $_POST['del_frm_id'] : '';
		if($del_id!="")
		{
			$account->delete_farmer_ById($del_id);
			header("Location:fview.php?act=del");
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
	  <h3>Farmers List</h3>
      <div id="carbon-block" class="my-3"></div>
    <div class="container">
	<div class="table-responsive">
        <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Code</th>
	  <th scope="col">Name</th>
      <th scope="col">Mobile</th>
      <th scope="col">Adhaar</th>
	  <th scope="col">Image</th>
	  <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($farmerdata as $fdata) 
	{ $inc++; 
	
	if($fdata['fimage'] == '' || $fdata['fimage'] == NULL)
	{
		$fpic="img/No_Image_Available.jpg";

	}else
	{
		$fpic="images/".$fdata['fimage']."";
	}
	
	?>
	    <tr>
      <th scope="row"><?php echo $inc; ?></th>
	  <td><?php echo $fdata['fcode'] ?></td>
      <td><?php echo $fdata['fname'] ?></td>
      <td><?php echo $fdata['fmobile'] ?></td>
      <td><?php echo $fdata['fadhar'] ?></td>
	   <td><img width="50" height="50" src="<?php echo $fpic; ?>"></td>
	    <td><input type="button" name="delete" value="Delete" data-target='#delete_farmer' data-toggle='modal' 
		class="deletefarmerdata" id="<?php echo $fdata['id'] ?>">&nbsp;<a href="edit_farmer_onboarding.php?id=<?php echo $fdata['id'] ?>"><input type="button" name="edit" value="Edit"></a></td>
    </tr>

	<?php }
  
  
  ?>
    
  </tbody>
</table></div>
<div class="modal fade" id="delete_farmer" tabindex="-1" role="dialog" aria-labelledby="delete_farmer" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content" >
							<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
								<div class="modal-body">
									<input autocomplete="off" type="hidden" id="del_farmer" name="del_farmer">
									<input autocomplete="off" type="hidden" id="del_frm_id" name="del_frm_id">
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
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	 $('.deletefarmerdata').click(function() { 
		let ccid = $(this).attr('id');
		$('#del_frm_id').val(ccid);
	 }); 
	  
    });
  </script>
</body>
</html>

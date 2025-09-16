<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$companydata = $account->getcompany_OnBoardingData($accountId);
//print_r($farmerdata);

		if(isset($_POST['del_company']))
	{
		$del_id = isset($_POST['del_cmp_id']) ? $_POST['del_cmp_id'] : '';
		if($del_id!="")
		{
			$account->delete_company_ById($del_id);
			header("Location:cview.php?act=del");
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
	   <h3>Vendors List</h3>
      <div id="carbon-block" class="my-3"></div>
    <div class="container">
	<div class="table-responsive">
        <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Code</th>
	  <th scope="col">Name</th>
      <th scope="col">GST</th>
      <th scope="col">Adhaar</th>
	  <th scope="col">PAN</th>
	  <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
  	foreach($companydata as $cdata) 
	{ $inc++; 
	
	?>
	    <tr>
      <th scope="row"><?php echo $inc; ?></th>
	  <td><?php echo $cdata['code'] ?></td>
      <td><?php echo $cdata['name'] ?></td>
      <td><?php echo $cdata['gst'] ?></td>
      <td><?php echo $cdata['adhar'] ?></td>
	   <td><?php echo $cdata['pan'] ?></td>
	    <td><input type="button" name="delete" value="Delete" data-target='#delete_company' data-toggle='modal' 
		class="deletecmpydata" id="<?php echo $cdata['id'] ?>">&nbsp;<a href="edit_company_onboarding.php?id=<?php echo $cdata['id'] ?>"><input type="button" name="edit" value="Edit"></a></td>
    </tr>
	<?php }
  ?>
  </tbody>
</table></div>


<div class="modal fade" id="delete_company" tabindex="-1" role="dialog" aria-labelledby="delete_company" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content" >
							<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
								<div class="modal-body">
									<input autocomplete="off" type="hidden" id="del_company" name="del_company">
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
<!--https://www.geeksforgeeks.org/form-validation-using-jquery/--> <!--// jquery validation code download-->
  <script>
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	  
	 $('.deletecmpydata').click(function() { 
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
		
	 
	 }); 
	  
    });
  </script>
</body>
</html>

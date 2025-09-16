<?php
session_start();
//print_r($_SESSION);
include'account.php';
$account =  new account();

$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

if(!empty($_POST))
{
	if(isset($_POST['createitem'])=='createitem')
	{
		$itm_name = isset($_POST['item_name'])? $_POST['item_name'] : NULL;
		$item_category= isset($_POST['item_category'])? $_POST['item_category'] : NULL;
		$itm_code = isset($_POST['item_code'])? $_POST['item_code'] : NULL;
		$itm_qty = isset($_POST['item_qty'])? $_POST['item_qty'] : NULL;
		$itm_image=isset($_FILES['item_image']['name']) ? $_FILES['item_image']['name'] : NULL;
		//$kyc = isset($_POST['kyc'])? $_POST['kyc'] : NULL;
		//print_r($_POST);
		
		// image upload
/*		$newfilename="";
		if($itm_image !='')
		{
			$allowedExts = array("jpg", "jpeg", "png","gif","webp");
			$extension = pathinfo($itm_image, PATHINFO_EXTENSION);
			if(in_array($extension, $allowedExts))
			{
				$temp = explode(".", $itm_image);
				$newfilename = 'item'.$accountId.'_'.rand().'.'.end($temp);
				move_uploaded_file($_FILES["item_image"]["tmp_name"],"images/items/" . $newfilename);
			}
		}
*/		
		// end image upload///
		
		// kyc multiple upload doc //
	// Checks if user sent an empty form 
	if(!empty(array_filter($_FILES['item_image']['name'])))
	 {
		// Loop through each file in files[] array
		$itemfilename = "";
		$itemFiles = [];
		foreach ($_FILES['item_image']['tmp_name'] as $key => $value)
			 {
						$fileName = basename($_FILES['item_image']['name'][$key]);
						$allowedExts = array("jpg", "jpeg", "png","gif","webp");
						$extension = pathinfo($_FILES["item_image"]["name"][$key], PATHINFO_EXTENSION);
						if(in_array($extension, $allowedExts))
						{
							$temp = explode(".", $_FILES["item_image"]["name"][$key]);
							$itemfilename = 'item_'.$accountId.'_'.date("dmyhis").'_'.rand().'.'.end($temp);
							move_uploaded_file($_FILES["item_image"]["tmp_name"][$key],"images/items/" . $itemfilename);
							$itemFiles[] = $itemfilename;
							//$account->setfarmer_Onboarding_kyc($kycfilename,$lastinsert,$accountId);  // insert into db
					   }else
					   {
						  header("Location:create_item.php?act=2");
						  exit;
					   }	 
			}
	  } 
	// $itemFilesSerialized = serialize($itemFiles); 
	 $itemFilesSerialized = json_encode($itemFiles); 
		
		if($itemFilesSerialized == '')
		{
				 //$account->create_Item($itm_name,$itm_code,$itm_qty,$newfilename);  // insert into db
				 header("Location:create_item.php?act=2");
				 exit;
		}
		else
		{
				 $account->create_Item($itm_name,$item_category,$itm_code,$itm_qty,$itemFilesSerialized,$accountId);  // insert into db
				 header("Location:create_item.php?act=1");
				 exit;
		}
	}
	
}

$categories_list = $account->getCategories();
//print_r($categories_list);
?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  <!--https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_image_gallery //  code downloade -->
  <title>Create item codes</title>
  <style>
    body 
	{ 
	background-color: #fafafa;
 }
 	.redtext{ color: red;} 
	.greentext{ color: green;} 

  </style>
  <style>
div.gallery {
  border: 0px solid #ccc;
}

div.gallery:hover {
  border: 0px solid #777;
}

div.gallery img {
  width: 100%;
  height: auto;
}

div.desc {
  padding: 14px;
  text-align: center;
   overflow: auto;
   height:110px;
}

* {
  box-sizing: border-box;
}

.responsive {
  padding: 0 6px;
  float: left;
  width: 12.99999%;
}

@media only screen and (max-width: 700px) {
  .responsive {
    width: 24.99999%;
    margin: 6px 0;
  }
}

@media only screen and (max-width: 500px) {
  .responsive {
    width: 24.99999%;
  }
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
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

    <div class="container" style="border:0px solid blue">
	 <!-- Fruits and veg grid-->
	<?php include'header_itemslist.php'; ?>
	<!-- Fruits and veg grid-->
      <br><br>
	  	  <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==1)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Item created Successfully</span></b></div>
	  <?php }  } ?>
	  
	  	
	  	<?php   if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']==2)
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#FF0000">Error in Uploading the Item..Please upload correct extension image</span></b></div>
	  <?php } 
	  } ?>

      <h2>Create Item</h2>

        <form action="" method="post" enctype="multipart/form-data" id="itemform" >
		<input type="hidden" name="createitem" value="createitem">
            <div class="form-group">
                <label for="item_name" class="control-label required">Item:</label>
                <input type="text" class="form-control" id="item_name"
                    placeholder="Enter item name" name="item_name" required ><i>(eg:Apple,Onions..)</i>
					<p id="item_nameerr"></p>
            </div>
			  <div class="form-group">
                <label for="item_category" class="control-label required">Category:</label>
				<select class="browser-default custom-select" name="item_category" id="item_category" required>
					<option value="">Select Category</option>
				<?php foreach($categories_list as $key=>$val){ ?>
					<option value="<?php echo $val['id']; ?>"><?php echo ucfirst($val['categoryname']); ?></option>
				<?php } ?>
				</select>
		<p id="cat_nameerr"></p>
            </div>
			<div class="form-group">
                <label for="item_code" class="control-label required">Item code:</label>
                <input type="text" class="form-control" id="item_code"
                    placeholder="Enter Item Code" name="item_code" required><i>(eg:APP001,ONI001)[To view items code list <a href="view_items.php" style="color:#009933">Click here</a>]</i>
					<p id="item_codeerr"></p>
            </div>
			<div class="form-group">
                <label for="item_image" class="control-label required">Item Image:</label>
                <input type="file" class="form-control" id="item_image"
                     name="item_image[]" multiple  required ><i>upload only(jpg,jpeg,png,gif,webp)</i>
					<p id="item_imageerr"></p>
            </div>          
		<input type="button" id="submitbtn" value="Submit">
        </form>
    </div>
    </div>

  </div>
<?php require_once('footer.php'); ?>
  <script>
  
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
	 
	 $('#submitbtn').click(function(e){
	 	let t_name = $("#item_name").val();
		let t_code = $("#item_code").val();
		let t_cat  = $("#item_category").val();
		let t_image = $("#item_image").val();
		let namevalidator;
		let codevalidator;
		let catvalidator;
		let imagevalidator;
		
	 
                if(t_name=="")
                {
                  $('#item_nameerr').html('<span class="redtext">Name is required</span>');
                  namevalidator = 1;
                }else
				{
					$('#item_nameerr').html('');
					 namevalidator = 0;
				}
                if(t_code=="")
                {
				  $('#item_codeerr').html('<span class="redtext">Code is required</span>');
                  codevalidator = 1;
                }else
				{
				  $('#item_codeerr').html('');
                  codevalidator = 0;
				}
                if(t_cat=="")
                {
                  $('#cat_nameerr').html('<span class="redtext">Category is required</span>');
                  catvalidator = 1;
                }else
				{
                  $('#cat_nameerr').html('');
                  catvalidator = 0;
				}
                if(t_image=="")
                {
                  $('#item_imageerr').html('<span class="redtext">Image is required</span>');
                  imagevalidator = 1;
                }else
				{
                  $('#item_imageerr').html('');
                  imagevalidator = 0;
				}
				
		//let itm_code = $(this).val();
		if(t_code!=='')
		{
	//	alert("here");
		$.ajax({
			type:"post",
			url:"check_itemAvailability.php",
			data:{id:t_code},
			dataType: 'json',
			success:function(response)
			{
				//alert(response);
				if(response[0] == 1)
				{
					$('#item_codeerr').html('<span class="redtext"><b>Code already exists</b></span>');
					codevalidator = 1;
				}else if(response[0] == 0)
				{
					$('#item_codeerr').html('<span class="greentext">Success!!</span>');
					codevalidator = 0;
					//alert("namevalidator="+namevalidator+"codevalidator="+codevalidator+"imagevalidator="+imagevalidator);
					if(namevalidator === 0 && catvalidator === 0 && codevalidator === 0 && imagevalidator === 0 )
					 {
						$('#itemform').submit();
					 }
					
				}
			}
		});
		} // if
		
		
		
		
				
				
	 
	 });
	 


	 
/*	$('#item_code').blur(function(e)
	{
		let itm_code = $(this).val();
		if(itm_code!=='')
		{
		$.ajax({
			type:"post",
			url:"check_itemAvailability.php",
			data:{id:itm_code},
			//dataType: 'json',
			success:function(response)
			{
				if(response == 1)
				{
					//alert("Code already exists"); 
					$('#item_codeerr').html('<span class="redtext">Code already exists</span>');
				}else if(response == 0)
				{
					$('#item_codeerr').html('<span class="greentext">Success!!</span>');
				}
			}
		});
		}
	});
*/
});

  </script>
</body>
</html>

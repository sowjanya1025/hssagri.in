<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}



$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) {
    die("Invalid purchase ID.");
}
$purchase_items = $account->get_purchase_items_by_purchase_id($purchase_id);
$purchase = $account->get_purchase_by_id($purchase_id);
//print_r($purchase_items);exit;

if(!empty($_POST))
{
   // print_r($_POST['items']);
   $date = date('Y-m-d');
   $misc = NULL;

    $total_quantity = $_POST['totalQty']; 
    $total_amount = $_POST['totalAmount']; 

    $lastid = $account->update_purchase($purchase_id,$date,$misc,$total_quantity,$total_amount); // insert into db
//     $insertedid =  $lastid['insert_last_id']; // inserted id 
//    // echo $insertedid;
//    // exit;


  // Build a list of item_ids in form submission
    $form_item_ids = [];

    foreach($_POST['items'] as $item)
    {
        if($item['quantity'] > 0 && $item['price'] > 0)
        {
             $qty = (float) $item['quantity'];
             $price = (float) $item['price'];
             $total = $qty * $price;

              $form_item_ids[] = $item['id'];

             // Check if this item already exists in the purchase_items table
             $existing = null;
            foreach ($purchase_items as $pi)
             {
                     if ($pi['item_id'] == $item['id']) {
                             $existing = $pi;
                             break;
                          }
             }

            if ($existing) {
            // Update existing item
            $account->update_purchase_item($existing['id'], $qty, $price, $total);
            } else {
            // Insert new item
            // $account->insert_purchase_item($purchase_id, $item_id, $qty, $price, $total);
             $account->create_purchase_item($purchase_id,$item['id'],$qty,$price,$total); // insert into db
             }  
            
        }
        
    }

     // Delete removed items
    foreach ($purchase_items as $pi) {
    if (!in_array($pi['item_id'], $form_item_ids)) {
        $account->delete_purchase_item($pi['id']);
    }
}

    header("Location:purchase_form1.php?act=edited");
	   exit;

}
$itemdata = $account->get_create_ItemData();


?>
<!doctype html>
<html lang="en">

<head>
<?php require_once('header.php'); ?>
  	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

  <title>Purchase Form</title>
 <style>
	body { background-color: #fafafa; }
	.redtext { color: red; }
	.greentext { color: green; }

.category-filter-btn {
 width:100px;
 height:100px;
 border-radius:50%;
 font-size:16px;
align-items:center;
margin:5px;
background-color:#000000ff;
color:white;
font-weight:640;
font-family: 'Roboto', sans-serif;
cursor: pointer;
transition: transform 0.3s ease-in;

}
.category-filter-btn:hover {
transform: scale(1.05);

}
.category-filter-btn:nth-child(n) {

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  /* z-index: -1; */
}
.category-filter-btn:nth-child(1) {
  background-image: url('https://img.freepik.com/premium-photo/vegetables-fruits-healthy-food-fruits-vegetables-black-stone-background-tropical-fruits-top-view-free-space-your-text_187166-28274.jpg');
  }
.category-filter-btn:nth-child(2)
{
  background-image:url('https://static.vecteezy.com/system/resources/thumbnails/033/235/545/small_2x/ai-generated-flowers-on-dark-background-with-copy-space-photo.jpeg');
}
.category-filter-btn:nth-child(3)
{
  /* background-image:url('https://thumbs.dreamstime.com/b/colorful-fresh-fruits-circle-dark-background-vibrant-assortment-including-apples-berries-grapes-arranged-textured-368049851.jpg'); */
  background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRhVlId3boeI3keP92-zQ_94FiyUbUKxrpmTc3Z-8zw-lC4flPxYNGznM-Yhb_vsQiEgGk&usqp=CAU');
  background-size: 100px 95px;
}
.category-filter-btn:nth-child(4)
{
  background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa2QhbNki1wdIPJrcor5Rj_uEH5wYKdEtePA&s');
}
.category-filter-btn:nth-child(5)
{
  background-image:url('https://t4.ftcdn.net/jpg/02/66/55/75/360_F_266557520_RXblHanGAsBcGvlMeJkoaWWPrdss33dL.jpg');
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
	    <!-- shivani mp -->
    <?php 
    // this is create unique /seperate category list frm items
  $categories=array_unique(array_column($itemdata,'category_name'));
  sort($categories); 
?>
<div  class="category-buttons-container" style ="margin:20px; text-align:center;">
  
  <button class ="category-filter-btn active" data-category="all">ALL</button>
  <?php  foreach ($categories as $cat): ?> 
  <button class ="category-filter-btn" data-category="<?php echo htmlspecialchars($cat);?>">
<?php echo htmlspecialchars($cat);?>    
</button>
<?php endforeach;?>
    
</div>
<!-- shivani mp -->
    <form action="" method="post" enctype="multipart/form-data" id="itemform" >
        <table class="table hover" id="purchaseTable">
  <thead class="thead-dark">
    <tr >
      <th scope="col">#</th>
      <th scope="col">Name of the item</th>
	  <th scope="col">Purchased Qty in kg/piece/kattu</th>
	  <th scope="col">Purchased price per kg/piece/kattu</th>
	  <th scope="col">Total cost</th>
    </tr>
  </thead>
  <tbody>
  <?php
     $inc = 0;
    //  shivani
$current_category = "";
	//  $colors = ['#00FFCC', '#FF0033', '#99FF33', '#CC9999', '#993366']; // Add more colors
    $colors = ['#4DB6AC', '#E57373', '#AED581', '#BAA0A0', '#8E5572'];  //shivani

		$colorIndex = 0;
// shivani

  	foreach($itemdata as $item) 
	{
	 $inc++; 
     $item['quantity'] = null;
      $item['price'] = null;
       $item['allcost'] = null;
        $item['purchaseid'] = null;


     foreach($purchase_items as $pitem)
     {
        if($item['id'] == $pitem['item_id'] )
        {
            $item['quantity'] = $pitem['quantity'];
            $item['price'] = $pitem['price_per_kg'];
            $item['allcost'] = $pitem['total_cost'];
            $item['purchaseid'] = $pitem['purchaseid'];
           break;
        }
     }
        
	
	?>
	<!--shivani-->
	   <?php
		if ($current_category != $item['category_name']) 
		{
            $current_category = $item['category_name'];
            // echo "<th colspan='5'  style='background-color:$color; font-size:18px   '>" . htmlspecialchars($current_category) . "</th>";
        //   echo "<th colspan='6' class='category-header' style='background-color:$colors;'>" . htmlspecialchars($current_category) . "</th>";
$color = $colors[$colorIndex % count($colors)];
echo "<th colspan='6' class='category-header' style='background-color:$color;'>" . htmlspecialchars($current_category) . "</th>";

			$colorIndex++;
		}
		 ?>  

<tr class="item-row" data-category="<?php echo htmlspecialchars($item['category_name']); ?>">
    	<!--shivani-->
    	
      <th scope="row"><?php echo $item['id_no']; ?></th>
	  <td><?php echo htmlspecialchars($item['name']); ?>/<?php echo htmlspecialchars($item['kannada_name']); ?>
      <input type="hidden" name="items[<?php echo $inc ?>][id]" value="<?php echo  $item['id'] ?>">
    <input type="hidden" name="items[<?= $inc ?>][purchaseid]" value="<?= $item['purchaseid'] ?>"></td>
      <td><input style="width:80px;" step="any"   type="number" name="items[<?php echo $inc ?>][quantity]" class="quantity" value="<?php echo $item['quantity']?>"></td>
	  <td><input style="width:80px;"  step="any"  type="number" name="items[<?php echo $inc ?>][price]" class="price" value="<?php echo $item['price']?>"></td>
	   <td><input style="width:80px;"  type="number" disabled name="items[<?php echo $inc ?>][allcost]" class="allcost" value="<?php echo $item['allcost']?>" ></td>
    </tr>
	<?php }
  ?>
  </tbody>
</table><br>
<!--    <strong>Total No of items :</strong><input type="number" name="totalitms" id="totalitms"><br>
-->    <strong>Total Quantity (kg):</strong><input step="any" readonly type="number" name="totalQty" id="totalQty" value="<?php echo  $purchase['total_quantity'] ?>"><br>
    <strong>Total Purchased Amount (₹):</strong><input step="any" readonly type="number" name="totalAmount" id="totalAmount" value="<?php echo  $purchase['total_cost'] ?>"><br><br>

    Date: <input type="text" readonly name="date" value="<?= date('d-m-Y') ?>"><br><br>

    <button type="submit">Submit Purchase</button>


</form></div>

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
	
	
	$(document).on('click','.deleteitemdata',function(e)
	{
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
		
	});
    $(document).on('input','.quantity,.price',function(e)
	{
		recalculate();
		
	});

    function recalculate()
    {
        let totalitems = 0;
        let totalQty = 0;
        let totalAmount = 0;
        $('#purchaseTable tbody tr').each(function() { 
            const qty = parseFloat($(this).find('.quantity').val()) || 0;
            const pric = parseFloat($(this).find('.price').val()) || 0;
            const total = qty * pric;
          //  $(this).find('.allcost').val(total);
            if (!isNaN(total)) {
                $(this).find('.allcost').val(total.toFixed(2));
                totalQty += qty;
                totalAmount += total;
               // totalitems += 1;
            }
            if (qty > 0 || pric > 0) {
                 totalitems += 1;
            } 
            });
            
                 //alert(totalQty);  
                 $('#totalitms').val(totalitems);
                $('#totalQty').val(totalQty.toFixed(2));
                   $('#totalAmount').val(totalAmount.toFixed(2));  
    }
    
     // shivani mp

  $(document).ready(function() {

    // Initially show all items (ALL is default)
    $('.item-row').show();

    // Highlight "ALL" button only on load
    $('.category-filter-btn').removeClass('active');
    $('.category-filter-btn[data-category="all"]').addClass('active');

    // Filter logic
    $('.category-filter-btn').on('click', function() {
      let selectedCategory = $(this).data('category');

      // Highlight clicked button
      $('.category-filter-btn').removeClass('active');
      $(this).addClass('active');

      // Show/hide rows based on selected category
      if (selectedCategory === 'all') {
        $('.item-row').show();
      } else {
        $('.item-row').hide();
        $(`.item-row[data-category="${selectedCategory}"]`).show();
      }
    });
    // Scroll to the table
    $('html, body').animate({
      scrollTop: $("#purchaseTable").offset().top
    }, 400);
  });
  </script>
</body>
</html>

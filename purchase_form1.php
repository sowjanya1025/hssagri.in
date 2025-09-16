<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}

$today = date('Y-m-d');
//$conversion =$account->getConversionData();
// Suppose $conversions is your array from DB
//$conversionMap = [];
//foreach ($conversion as $row) {
//    $conversionMap[$row['id']] = $row['conversion_factor'];
//}
//print_r($conversionMap); exit;


if(!empty($_POST))
{
   // print_r($_POST); exit;
  // $date = $_POST['date'];
   $date = date('Y-m-d');
   $misc = NULL;

    $total_quantity = $_POST['totalQty']; 
    $total_amount = $_POST['totalAmount']; 

    $lastid = $account->create_purchase($accountId,$date,$misc,$total_quantity,$total_amount); // insert into db
    $insertedid =  $lastid['insert_last_id']; // inserted id 
  //  print_r($insertedid); exit;


    if ($insertedid != 0) {
    foreach($_POST['items'] as $item)
    {
        if($item['quantity'] > 0 && $item['price'] > 0 && $item['id'] > 0  )
        {
             $qty = (float) $item['quantity'];
             $price = (float) $item['price'];
             $total = $qty * $price;

              // Get conversion factor from DB
            // $conversions = isset($conversionMap[$item['id']]) ? $conversionMap[$item['id']] : 1;
              // Recalculate pieces in backend
             //$pieces = $qty * $conversions;
             //echo $item['id']."-".$qty."-".$conversions."-".$pieces."<br>";

             $account->create_purchase_item($insertedid,$item['id'],$qty,$price,$total); // insert into db
        }
        
    }// exit;
  }
    header("Location:purchase_form1.php?act=1");
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
	/* body { background-color: #fafafa; }
	.redtext { color: red; }
	.greentext { color: green; } */
    
    
    /* === GLOBAL RESET & BASE STYLES === */
    /* shivani style added */
body {
  background-color: #fafafa;
  margin: 0;
  padding: 0;
  font-family: sans-serif;
  line-height: 1.4;
}

.redtext { color: red; }
	.greentext { color: green; } 


input[type="number"],
input[type="text"],
select,
button {
  max-width: 100%;
  box-sizing: border-box;
  padding: 8px;
  font-size: 1rem;
}

button {
  cursor: pointer;
}

.container {
  padding: 10px;
  max-width: 1200px;
  margin: auto;
}

#recoverYesterday,
#combineAndSubmit {
  width: 100%;
  max-width: 300px;
  padding: 12px;
  margin: 10px 0;
  font-size: 1rem;
  background: green;
  color:white;
  border: none;
  border-radius: 5px; 
  box-shadow: 2px 2px 5px gray;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border: 3px solid #099114;
  padding: 10px;
  background-color: #fff;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}

thead {
  background-color: #333;
  color: white;
}

th,
td {
  text-align: left;
  padding: 10px;
  font-size: 14px;
  border-bottom: 1px solid #ccc;
}

input.todayQty,
input.todayPrice,
input.allcost,
select {
  width: 100%;
  min-width: 60px;
}

select {
  min-width: 80px;
}

.category-header {
  font-size: 18px;
  padding: 12px;
  text-align: left;
  width: 100%;
  color: #fff;
  word-break: break-word;
}

/* --- MEDIA QUERIES --- */

@media (max-width: 768px) {
  table {
    font-size: 13px;
    min-width: unset;
  }
  
  th,
  td {
    padding: 8px 6px;
    white-space: normal;
  }
  
  input.todayQty,
  input.todayPrice,
  input.allcost,
  select {
    width: 100%;
  }

  .container,
  .table-responsive {
    padding: 5px;
  }
  
  input[name="totalitms"],
  input[name="totalQty"],
  input[name="totalAmount"] {
    width: 100%;
    max-width: 300px;
    margin-bottom: 10px;
  }
  
  #hiddenFields input {
    width: 100%;
    margin-bottom: 5px;
  }
  
  .text-center {
    text-align: center;
    padding: 10px;
  }
  
  .category-header {
    font-size: 16px;
    padding: 10px;
  }
}

@media (max-width: 480px) {
  th,
  td {
    font-size: 12px;
    padding: 6px 4px;
  }
  
  button {
    font-size: 1rem;
    padding: 10px;
  }
  
  .category-header {
    font-size: 14px;
    padding: 8px;
  }
}

#totalitms ,#totalQty ,#totalAmount{
    /* input[name="totalitms"]{ */
    margin:5px;
    
    }
 /* imgs round round */
.category-filter-btn {
  width:100px;
  height:100px;
 border-radius: 50%;
  font-size:16px;
  margin: 5px;
  /* padding: 10px 15px; */
  border: none;
  background-color: #000000ff;
  color: #ffffffff;
  font-weight:bold;
  cursor: pointer;

/* justify-content: center; */
justify-content: flex-end;
transition: transform 0.3s ease-in;
  align-items: center;
   
}
.category-filter-btn:hover {
  transform: scale(1.05);
}
.category-filter-btn:nth-child(n) {

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
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



.category-filter-btn.active {
   outline: none;
  background-color: rgba(40, 167, 70, 0.56); 
  border: 3px solid #28a745;
  box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

 /* shivani style end */
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
    <?php if(!empty($_GET['act']))
	   {
	  	 if($_GET['act']=='edited')
		 {			 ?>
	  		<div class="text-center"><b><span style="color:#009900">Edited Successfully</span></b></div>
	  <?php }  } ?>
	  <br><br>
	       <a href="download_purchase.php" class="btn btn-success">Download Purchase Form (XLS)</a>

      <?php 
      // Check if today's purchase already exists
$existing_purchase = $account->get_purchase_by_date($accountId, $today);
//exit;

if ($existing_purchase) {
    echo "<h3 style='color:green;'>Today's purchase form is filled.</h3>";
    echo "<a style='color:blue;' href='purchase_editform.php?id=".$existing_purchase['id']."'>Click here to edit today's purchase</a>";
   // exit; // Stop the rest of the form from displaying
} else {  ?>

      <div id="carbon-block" class="my-3"></div>
	  	  
    <div class="container">
	<!-- <button id="recoverYesterday" type="button" class="btn btn-warning">Recover from Yesterday</button> -->
<form id="yesterdayForm">
</form>
	
	<div class="table-responsive" style=" border:3px solid #099114ff; padding:10px; background-color:#fff;">
    <!-- shivani mp -->
    <?php 
    // this is create unique /seperate category list frm items
  $categories=array_unique(array_column($itemdata,'category_name'));
  sort($categories); 
?>
<div  class="category-buttons-container" style ="margin:20px; text-align:center;">
  
  <button class ="category-filter-btn active" data-category="all">ALL</button>
  <?php  foreach ($categories as $cat): ?> 
  <button class ="category-filter-btn active" data-category="<?php echo htmlspecialchars($cat);?>">
<?php echo htmlspecialchars($cat);?>    
</button>
<?php endforeach;?>
    
</div>
<!-- shivani mp -->
    <form action="" method="post" enctype="multipart/form-data" id="itemform" >
        <table class="table hover" id="purchaseTable">
  <thead class="thead-dark">
    <tr>
      
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
	 $current_category = "";
	//  $colors = ['#00FFCC', '#FF0033', '#99FF33', '#CC9999', '#993366']; // Add more colors
    $colors = ['#4DB6AC', '#E57373', '#AED581', '#BAA0A0', '#8E5572'];  //shivani

		$colorIndex = 0;
  	foreach($itemdata as $item) 
	{
	 $inc++;
	 $color = $colors[$colorIndex % count($colors)];
	  
	?>
	   
		<?php
		if ($current_category != $item['category_name']) 
		{
            $current_category = $item['category_name'];
            // echo "<th colspan='5'  style='background-color:$color; font-size:18px   '>" . htmlspecialchars($current_category) . "</th>";
           echo "<th colspan='6' class='category-header' style='background-color:$color;'>" . htmlspecialchars($current_category) . "</th>";

			$colorIndex++;
		}
		 ?>
      <!-- shivanimp -->
      <tr class="item-row" data-category="<?php echo htmlspecialchars($item['category_name']); ?>">

      <!-- shivanimp -->
       <!-- <tr> -->
      <th scope="row" ><?php echo $item['id_no']; ?></th>
	  <td><?php echo htmlspecialchars($item['name']); ?>/<?php echo htmlspecialchars($item['kannada_name']); ?>
      <input type="hidden" class="itemId" name="items[<?php echo $inc ?>][id]" value="<?php echo  $item['id'] ?>"></td>
      <td><input style="width:80px;" step="any" class="todayQty"   type="number" name="items[<?php echo $inc ?>][quantity]"  value=""></td>
	  <td><input style="width:80px;" step="any" class="todayPrice"  type="number" name="items[<?php echo $inc ?>][price]"  value=""></td>

	   <td><input style="width:80px;" disabled  type="number" name="items[<?php echo $inc ?>][allcost]" class="allcost" value="" ></td>
    </tr>
	<?php }
  ?>
  </tbody>
</table><br>
    <strong>Todays Total No of items :</strong><input readonly type="number" name="totalitms" id="totalitms"><br>
    <strong>Todays Total Quantity (kg):</strong><input readonly type="number" name="totalQty" id="totalQty"><br>
    <strong>Todays Total Purchased Amount (₹):</strong><input readonly type="number" name="totalAmount" id="totalAmount"><br><br>

    Date: <input type="text" readonly name="date" value="<?php echo date('Y-m-d') ?>"><br><br>

    <div id="hiddenFields"></div>

    <button id="combineAndSubmit" type="button">Submit Purchase</button>


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
  </div> <?php } ?>
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
    $(document).on('input','.todayQty,.todayPrice',function(e)
	{
		recalculate();
		
	});

    function recalculate()
    {
        let totalitems = 0;
        let totalQty = 0;
        let totalAmount = 0;
        $('#purchaseTable tbody tr').each(function() { 
            const qty = parseFloat($(this).find('.todayQty').val()) || 0;
            const pric = parseFloat($(this).find('.todayPrice').val()) || 0;
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
	
	
$(document).on('click', '#recoverYesterday', function() {
    $.ajax({
        url: 'get_yesterday_purchase.php',
        type: 'POST',
        dataType: 'json',
        data: { account_id: <?php echo $accountId; ?> }, // Pass the account ID
        success: function(res) { 

            let form = $('#yesterdayForm');
            form.empty(); // Clear old table if any

            // Start building the table HTML
            let tableHtml = `
                <h4>Remaining items from Yesterday's purchase:</h4>
                <table id="yesterdayTable" border="5" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Append rows dynamically
            res.forEach(function(item) {
                tableHtml += `
                    <tr style="background-color: #f3d369ff">
                        <td>${item.item_id}</td>
                        <td>${item.item_name}/${item.kannada_name}
                            <input type="hidden" class="yesterdayItemId" value="${item.item_id}">
                        </td>
                        <td>
                            <input type="number" readonly class="yesterdayQty" value="${item.remaining_stock}">
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdayPrice" value="${item.price_per_kg}" readonly>
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdayPrice" value="${item.selling_price}" readonly>
                        </td>
                        <td>
                            <input type="number" readonly step="0.01" class="yesterdaytotcost" value="${(item.price_per_kg * item.remaining_stock).toFixed(2)}" readonly>
                        </td>
                    </tr>
                `;
            });

            // Close the table
            tableHtml += `
                    </tbody>
                </table>
            `;

            // Append table to form
            form.append(tableHtml);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching yesterday's purchase:", error); 
        }
    });
});

// $(document).on('click', '#combineAndSubmit', function() {

// //e.preventDefault(); // stop immediate submit

//     let combinedData = [];

//     // Get yesterday's data
//     $('#yesterdayTable tbody tr').each(function () {
//         //alert ("Processing yesterday's data...");
//        // alert($(this).find('.yesterdayItemId').val() + " " + $(this).find('.yesterdayQty').val() + " " + $(this).find('.yesterdayPrice').val());
//         combinedData.push({
//             id: $(this).find('.yesterdayItemId').val(),
//             qty: parseFloat($(this).find('.yesterdayQty').val()) || 0,
//             price: parseFloat($(this).find('.yesterdayPrice').val()) || 0
//         });
//     });
//    //  Merge today's data
//     $('#purchaseTable tbody tr').each(function () {
//         let id = $(this).find('.itemId').val();
//         let todayQty = parseFloat($(this).find('.todayQty').val()) || 0;
//         let todayPrice = parseFloat($(this).find('.todayPrice').val()) || 0;
//        // alert(todayQty + " " + todayPrice + " " + id);

//         let existing = combinedData.find(item => item.id == id && item.qty > 0);
//         if (existing) {
//             let totalQty = existing.qty + todayQty;
//             if (todayPrice > 0) {
//                 existing.price = todayPrice; // Average price
//             } else {
//                 existing.price = existing.price; // Keep existing price if no new price
//             }
//             existing.qty = totalQty;
            
//         } else {
//            // alert("New Item: " + id + " " + todayQty + " " + todayPrice);
//             combinedData.push({
//                 id: id,
//                 qty: todayQty,
//                 price: todayPrice
//             });


//         }
//     });   
    
//     // alert(combinedData.length);
//      //  Clear old hidden fields
//     $('#hiddenFields').empty();

//      //  Create hidden inputs for each item
//     combinedData.forEach((item, index) => {
//         $('#hiddenFields').append(`
//             <input type="text" name="items[${index}][id]" value="${item.id}">
//             <input type="text" name="items[${index}][quantity]" value="${item.qty}">
//             <input type="text" name="items[${index}][price]" value="${item.price}">
//             <br>
//         `);
//     });

//     // Calculate totals
//    // let totalItems = combinedData.length;  // Number of unique items
//     let validItems = combinedData.filter(item => item.qty > 0 && item.price > 0);
//     let totalItems = validItems.length; // Number of items with qty and price
//     let totalQty = 0;
//     let totalCost = 0;

//     combinedData.forEach(item => {
//         totalQty += item.qty;
//         totalCost += item.qty * item.price; // qty × price for each item
//     });

//     //alert("Total Items: " + totalItems + "\nTotal Quantity: " + totalQty.toFixed(2) + "\nTotal Cost: " + totalCost.toFixed(2));
//             // // Fill main fields
//             $('#hiddenFields').append(`
//             <input type="text" name="totalQty"  value="${totalQty}">
//             <input type="text" name="totalitms" value="${totalItems}">
//             <input type="text" name="totalAmount" value="${totalCost.toFixed(2)}">
//             <br>
//         `);

// 	//  Submit the form normally
//     $(this).closest('form')[0].submit();



// });

$(document).on('click', '#combineAndSubmit', function(e) {
    e.preventDefault(); // prevent immediate submission

    let hasData = false;

    // Check if any today's item has quantity and price > 0
    $('#purchaseTable tbody tr').each(function () {
        const qty = parseFloat($(this).find('.todayQty').val()) || 0;
        const price = parseFloat($(this).find('.todayPrice').val()) || 0;

        if (qty > 0 && price > 0) {
            hasData = true;
            return false; // break the loop
        }
    });

    // Also check if yesterday's data exists
    if ($('#yesterdayTable').length > 0) {
        hasData = true;
    }

    if (!hasData) {
        if (!confirm("You haven't filled any data. Are you sure you want to submit an empty form?")) {
            return; // stop submission
        }
    }

    // If data exists OR user confirms empty submit, proceed as normal
    

    let combinedData = [];

    // 1. Get yesterday's data (if any)
    $('#yesterdayTable tbody tr').each(function () {
        combinedData.push({
            id: $(this).find('.yesterdayItemId').val(),
            qty: parseFloat($(this).find('.yesterdayQty').val()) || 0,
            price: parseFloat($(this).find('.yesterdayPrice').val()) || 0
        });
    });

    // 2. Merge today's data
    $('#purchaseTable tbody tr').each(function () {
        let id = $(this).find('.itemId').val();
        let todayQty = parseFloat($(this).find('.todayQty').val()) || 0;
        let todayPrice = parseFloat($(this).find('.todayPrice').val()) || 0;

        let existing = combinedData.find(item => item.id == id && item.qty > 0);
        if (existing) {
            let totalQty = existing.qty + todayQty;
            existing.price = todayPrice > 0 ? todayPrice : existing.price;
            existing.qty = totalQty;
        } else {
            combinedData.push({
                id: id,
                qty: todayQty,
                price: todayPrice
            });
        }
    });

    // Clear old hidden fields
    $('#hiddenFields').empty();

    // Create hidden inputs
    combinedData.forEach((item, index) => {
        $('#hiddenFields').append(`
            <input type="hidden" name="items[${index}][id]" value="${item.id}">
            <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            <input type="hidden" name="items[${index}][price]" value="${item.price}">
            <br>
        `);
    });

    // Calculate totals
    let validItems = combinedData.filter(item => item.qty > 0 && item.price > 0);
    let totalItems = validItems.length;
    let totalQty = 0;
    let totalCost = 0;

    combinedData.forEach(item => {
        totalQty += item.qty;
        totalCost += item.qty * item.price;
    });

    $('#hiddenFields').append(`
        <input type="text" name="totalQty" value="${totalQty}">
        <input type="text" name="totalitms" value="${totalItems}">
        <input type="text" name="totalAmount" value="${totalCost.toFixed(2)}">
        <br>
    `);

    // Submit the form
    $(this).closest('form')[0].submit();
});


// shivani mp
$(document).ready(function(){
  $('.item-row').hide();  //hiding all the  categories 

  //filter buttons on clicking
  $('.category-filter-btn').on('click',
    function(){
      let selectCategory = $(this).data('category');

// highliting the active button
      $('.category-filter-btn').removeClass('active');
      $(this).addClass('active');

      //selecting accorgin to selected cat
      if(selectCategory ==='all'){
        $('.item-row').show();

        }else {
          $('.item-row').hide();
          $(`.item-row[data-category="${selectCategory}"]`).show();

        }
        });
$('.item-row').hide();

$('html, body').animate({
  scrollTop: $("#purchaseTable").offset().top
}, 400);

      });
   
// shivani mp
</script>
  
</body>
</html>
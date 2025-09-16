<?php
session_start();
include'account.php';
$account =  new account();
$accountId=$account->getCurrentUserId(); 

if(!isset($_SESSION['user_id']))
{
	header("Location:index.php");
}


if(isset($_GET['id']))
{
	$pid = $_GET['id'];
}
$itemdata = $account->getGoodsReceive_noteByID($accountId,$pid);
//print_r($itemdata);

?>
<!doctype html>
<html lang="en">
<head>
<?php require_once('header.php'); ?>
  <title>Approved-Rejected Goods</title>
  <style>
    
 .invoice
 {
 	width:75%
  }
  .invoice td { border:2px solid black; padding: 8px;text-align: left; } 
  .invoice th { border:2px solid black; padding: 8px;text-align: center; }
  .sku-list { width:75% }
  .sku-list td { border:2px solid black; padding: 8px;text-align: left; } 
  .sku-list th { border:2px solid black; padding: 8px;text-align: center; }
  .total {
            text-align: center;
        }
  
  @media print {
            .print-button {
                display: none;
            }
			.printsidebar
			{
				display: none;
			}
        } 
  </style>
</head>
<body>
  <!-- start wrapper -->
  <div class="wrapper">
  <div class="printsidebar">
    <?php require_once('side_bar.php'); ?>
	</div>
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
	  	 
    <div class="container">
	<div class="table-responsive">
	<table class="invoice" style="border:2px solid black">
    <tr>
      <th colspan="2" >Enterprises name</th>
    </tr>
  <tbody>
    <tr>
      <td > <strong>Collection Centre:</strong> Chikballapur CC<br></td>
      <td><strong>Farmer Name:</strong> <?php echo htmlspecialchars($itemdata['farmers_name']); ?>(FAR13477)<br></td>
    </tr>
    <tr>
      <td><strong>Address:</strong> No-128 P18 & 19, Budigere cross, near Mandur village, Bangalore, Karnataka - 560049</td>
      <td><strong>Date:</strong> 24/08/2024</td>
    </tr>
	<tr>
      <td><strong>Generated at:</strong> 24/08/2024 10:14:18 PM</td>
      <td><strong>Invoice No.:</strong> 338016, <strong>Purchase Order No.:</strong> 338016</td>
    </tr>
    <tr>
      <td colspan="2"><input type="text" value="" name="code"></td>
    </tr>
  </tbody>
</table>
<br>
<table class="sku-list">
        <thead>
		<tr>
      		<th colspan="6" >SKU list</th>
    	</tr>
            <tr>
                <th>Sr No.</th>
                <th>Product Name</th>
                <th>Graded Qty</th>
                <th>Uom</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Green Chilli (Hari Mirch)</td>
                <td>230.0</td>
                <td>kg</td>
                <td>38.0</td>
                <td>8740.0</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="total">Total Amount</td>
                <td>8740.0</td>
            </tr>
            <tr>
                <td colspan="6">Total Amount in Words:Eight Thousand Seven Hundred Forty Only</td>
            </tr>
        </tfoot>
    </table>

<button class="print-button" onclick="window.print()">Print</button><button class="print-button" >Back</button>
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
	  
	 $('.deleteitemdata').click(function() { 
		let ccid = $(this).attr('id');
		$('#del_cmp_id').val(ccid);
 
	 });
	 
    });
  </script>
</body>
</html>

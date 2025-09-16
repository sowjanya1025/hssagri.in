<?php
session_start();
include'account.php';
$account =  new account();



if(!isset($_SESSION['user_id']))
{
	//print_r($_SESSION);
	header("Location:index.php");
}


?>

<!doctype html>
<html lang="en">
<head>
<?php require_once('header.php'); ?>
  <title>Hssagri-dashboard</title>
  <style>
    body { background-color: #fafafa; }
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
      <h1>Dashboard</h1>
      <div id="carbon-block" class="my-3"></div>

      <p>"Welcome to the Hssagri Dashboard, your central hub for managing all aspects of your vegetable store. Here, you can easily oversee your product inventory, track customer orders, and analyze sales data to optimize your business. Let's help you deliver the freshest produce to your customers!"</p>

      <div class="line"></div>
      <h3>Support</h3>
      <p>"Need help? Access our support resources or get in touch with our customer service team. We're here to assist you with any questions or issues you may encounter."</p>

    </div>

  </div>
<?php require_once('footer.php'); ?>
  <script>
    $(document).ready(function() {
      $("#sidebarCollapse").on('click',function() {
        $("#sidebar").toggleClass('active');
      });
    });
  </script>
</body>
</html>

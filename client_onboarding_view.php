<?php
session_start();
//print_r($_SESSION);

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
  <title>Client onboarding view</title>
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
      <div id="carbon-block" class="my-3"></div>
	  <h3><span class="badge badge-secondary"><a href="view_moderntraders.php">Modern Traders</a></span></h3>
	  <h3><span class="badge badge-secondary"><a href="view_oreca.php">Oreca</a></span></h3>
	  <h3><span class="badge badge-secondary"><a href="view_generaltraders.php">General Traders</a></span></h3>
	  <h3><span class="badge badge-secondary"><a href="view_retailtraders.php">Retail Traders</a></span></h3></p>
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

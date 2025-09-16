<nav id="sidebar">
      <div class="sidebar-header">
         <h3>Hssagri</h3>
      </div><ul class="lisst-unstyled components" >
	   <?php if($account->superAdmin)
		{ ?>
        <li class="active">
          <a href="#foodSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Onboarding Menu</a>
          <ul class="collapse lisst-unstyled" id="foodSubmenu">
            <li><a href="#vendors" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Vendors</a></li>
			<ul class="collapse lisst-unstyled" id="vendors">
            <li><a href="farmer_onboarding.php">Farmersnn</a></li>
            <li><a href="company_onboarding.php">Suppliers</a></li>
          </ul>
		    <li><a href="#clients" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Clients</a></li>
			<ul class="collapse lisst-unstyled" id="clients">
            <li><a href="moderntraders_onboarding.php">ModernTraders</a></li>
            <li><a href="oreca_onboarding.php">Oraca</a></li>
			<li><a  href="generaltraders_onboarding.php">GeneralTraders</a></li>
			<li><a href="retailtraders_onboarding.php">RetailTraders</a></li>
          </ul>
          </ul>
        </li>
        <!--<li class="active">
          <a href="#foodSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Onboarding Menu</a>
          <ul class="collapse lisst-unstyled" id="foodSubmenu">
            <li><a href="farmer_onboarding.php">Farmer onboarding</a></li>
            <li><a href="company_onboarding.php">Vendors onboarding</a></li>
          </ul>
        </li>
		<li class="active">
          <a href="#clientmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Client onboarding</a>
          <ul class="collapse lisst-unstyled" id="clientmenu">
            <li><a href="moderntraders_onboarding.php">Modern Traders</a></li>
            <li><a href="oreca_onboarding.php">Oreca</a></li>
			<li><a href="generaltraders_onboarding.php">General Traders</a></li>
			<li><a href="retailtraders_onboarding.php">Retail Traders</a></li>
          </ul>
        </li>-->
		<li class="active">
          <a href="#viewmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">View</a>
          <ul class="collapse lisst-unstyled" id="viewmenu">
            <li><a href="fview.php">Farmer onboarding view</a></li>
            <li><a href="cview.php">Vendors onboarding view</a></li>
			<li><a href="client_onboarding_view.php">Client onboarding view</a></li>
          </ul>
        </li>
		<li class="active">
          <a href="#grn" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">GRN</a>
          <ul class="collapse lisst-unstyled" id="grn">
            <li><a href="goodsreceivenote.php">Create GRN</a></li>
            <li><a href="summary.php">Summary</a></li>
			<li><a href="approvereject_goods.php">Approved/Rejected Goods</a></li>
          </ul>
        </li>
		<li class="active">
          <a href="#gsb" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">GSB</a>
          <ul class="collapse lisst-unstyled" id="gsb">
            <li><a href="goods_supply_bill.php">Create GSB</a></li>
            <li><a href="summary_gsb.php">Summary</a></li>
			<li><a href="approvereject_gsb.php">Approved/Rejected Goods</a></li>
          </ul>
        </li>
		<?php }  ?>
     <?php  if (($account->hasAccess('purchase')) || ($account->superAdmin) ) { ?>
		<li class="active">
          <a href="#itemmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Item codes</a>
          <ul class="collapse lisst-unstyled" id="itemmenu">
            <li><a href="create_item.php">Create Item Codes</a></li>
            <li><a href="view_items.php">View Item Codes</a></li>
          </ul>
        </li>
    <?php }  ?>
         <?php if($account->superAdmin)
		{ ?>
		<li>
          <a href="add_apartments.php">Add Apartments</a>
        </li>
         <?php }  ?>
        <?php  if (($account->hasAccess('purchase')) ) { ?>
    <li>
          <a href="purchase_form1.php">Purchase Form</a>
        </li>
     <li>
          <a href="recovery_form.php">Current Inventory</a>
       </li>
		<li>
          <a href="inventory_form.php">Inventory at Location</a>
        </li>
        <li>
          <a href="analysis_three_day.php">Purchase Analysis</a>
        </li>
		<li>
          <a href="selling_form.php">Selling Form</a>
        </li> <?php } ?>
        <?php  if (($account->hasAccess('sales'))  ) { ?>
		<li>
          <a href="sales_form.php">Sales Form</a>
        </li>  <?php } ?>
         <?php  if (($account->hasAccess('sales')) || ($account->hasAccess('purchase'))  ) { ?>
		<li>
          <a href="sales_bydate.php">Download XLS</a>
        </li>  <?php } ?>
       
		<li>
      <?php if($account->superAdmin)
		{ ?>
      <li>
          <a href="sales_list.php">Sales List</a>
        </li>
         <?php }  ?>
		<li>
          <a href="logout.php">Logout<b>&nbsp;<i class="bi bi-box-arrow-right"></i></b></a>
        </li>
      </ul></nav>

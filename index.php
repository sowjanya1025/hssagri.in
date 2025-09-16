<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HSSAgrii</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
/*      background: #ff4d00;
*/	  background: #33FF99;
      padding: 30px 10px;
    }

    .offer-card {
      border-radius: 20px;
      background: #fff;
      padding: 20px;
      text-align: left;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
      height: 220px;
    }

    .offer-card h5 {
      font-weight: 800;
    }

    .offer-card p {
      margin-bottom: 5px;
      color: #777;
    }

    .offer-discount {
      background: #fff0e5;
      color: #ff4d00;
      font-weight: 600;
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .offer-image {
      max-width: 100px;
      position: absolute;
      bottom: 10px;
      right: 10px;
    }

    .arrow-btn {
      width: 40px;
      height: 40px;
      background: #ff4d00;
      color: #fff;
      border-radius: 50%;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      margin-top: 10px;
    }
	
	

    @media (max-width: 767.98px) {
      .offer-card {
        height: auto;
        margin-bottom: 20px;
      }

      .offer-image {
        position: static;
        display: block;
        margin-top: 15px;
        max-width: 80px;
      }

      .arrow-btn {
        margin-top: 15px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row">
    <!-- Card 1 -->
	<div class="col-md-4 mb-4">
	<a href="https://hssagri.in/b2c/aptgrid.php" class="text-decoration-none text-dark">
      <div class="offer-card">
        <h5> B2C (Business to Consumer)</h5>
        <p>Direct customer purchases</p>
        <button class="arrow-btn">&#8594;</button>
        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081648.png" class="offer-image" alt="b2c">
      </div>
	  </a>
    </div>
    <!-- Card 2 -->
    <div class="col-md-4 mb-4">
	<a href="https://hssagri.in/b2b/" class="text-decoration-none text-dark">
      <div class="offer-card">
        <h5>B2B (Business to Business)</h5>
        <p>Selling bulk fruits/vegetables to shops, wholesalers, etc.</p>
        <button class="arrow-btn">&#8594;</button>
        <img src="https://cdn-icons-png.flaticon.com/512/1055/1055687.png" class="offer-image" alt="b2b">
      </div>
	 </a>
    </div>

    <!-- Card 3 -->
	<div class="col-md-4 mb-4">
	<a href="http://localhost/hssagrii/dcmanagement.php" class="text-decoration-none text-dark">
      <div class="offer-card" >
        <h5>DC Management</h5>
        <p>Distribution Center Management - logistics, warehousing, routing</p>
        <button class="arrow-btn">&#8594;</button>
        <img src="https://cdn-icons-png.flaticon.com/512/10439/10439669.png" class="offer-image" alt="dc">
      </div>
	  </a>
    </div>
    
  </div>
</div>

</body>
</html>

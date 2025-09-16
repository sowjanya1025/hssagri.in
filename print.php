<!--<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Farmer Information</title>
  
  <style>
    /* General style for the printable section */
    #printableSection {
      font-size: 18px;
      padding: 20px;
      border: 2px solid black;
      width: 80%;
      margin: 20px auto;
      border-radius: 10px;
      box-sizing: border-box;
    }

    /* Style for the table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      border: 1px solid black; /* Add border to table for complete border */
    }

    th, td {
      padding: 10px;
      text-align: left;
      border: 1px solid black; /* Ensure every cell has a border */
    }

    th {
      background-color: #f2f2f2;
    }

    /* General body styles */
    body {
      font-family: Arial, sans-serif;
    }
  </style>
</head>
<body>

  <h2 style="text-align: center;">Farmer Details</h2>
  <div id="printableSection">
    <p><strong>Farmer Name:</strong> Saketh Chepuri</p>

    <h3>Goods Details</h3>
    <table>
      <tr>
        <th>Item Code</th>
        <th>Item Quantity</th>
        <th>Item Price</th>
        <th>Total Amount</th>
      </tr>
      <tr>
        <td>oni001</td>
        <td>55</td>
        <td>55</td>
        <td>3025</td>
      </tr>
    </table>
  </div>

  <!-- Print Button -->
  <!--<div style="text-align: center;">
    <button class="print-button" onclick="printSection()">Print</button>
  </div>-->

 // <script>
//    function printSection() {
//      var printContents = document.getElementById('printableSection').outerHTML;
//      var originalContents = document.body.innerHTML;
//
//      // Create a new window to apply the print styles
//      var printWindow = window.open('', '', 'height=600,width=800');
//      printWindow.document.write('<html><head><title>Print Section</title>');
//
//      // Add print-specific styles inside the new window
//      printWindow.document.write('<style>');
//      printWindow.document.write('body { font-family: Arial, sans-serif; }');
//      printWindow.document.write('#printableSection { font-size: 20px; padding: 20px; border: 4px solid black; border-radius: 10px; margin: 0 auto; width: 80%; box-sizing: border-box; }');
//      printWindow.document.write('table { border-collapse: collapse; width: 100%; margin-top: 20px; border: 2px solid black; }');
//      printWindow.document.write('th, td { padding: 10px; text-align: left; border: 2px solid black; }');
//      printWindow.document.write('th { background-color: #f2f2f2; }');
//      printWindow.document.write('</style>');
//      
//      printWindow.document.write('</head><body>');
//      printWindow.document.write(printContents);
//      printWindow.document.write('</body></html>');
//      printWindow.document.close();
//      printWindow.focus();
//      printWindow.print();
//      printWindow.close();
//    }
//  </script>

<!--</body>
</html>-->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Data Example</title>
  
  <style>
    /* Example styling */
    #printableSection {
      font-size: 18px;
      padding: 20px;
      border: 2px solid black;
      width: 80%;
      margin: 20px auto;
      border-radius: 10px;
      box-sizing: border-box;
    }
  </style>
</head>
<body>

  <h2 style="text-align: center;">Current Page Content</h2>
  <p>This is the content on the page. The print will have different content.</p>

  <!-- Print Button -->
  <div style="text-align: center;">
    <button class="print-button" onclick="printDifferentData()">Print Different Data</button>
  </div>

  <script>
    function printDifferentData() {
      // Different data to be printed, not visible on the page
      var printData = `
        <h2>Invoice Details</h2>
        <p><strong>Farmer Name:</strong> John Doe</p>
        <p><strong>Item Code:</strong> abc123</p>
        <p><strong>Item Quantity:</strong> 75</p>
        <p><strong>Item Price:</strong> 100</p>
        <p><strong>Total Amount:</strong> 7500</p>
      `;

      // Create a new window for printing
      var printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print Section</title>');
      
      // Add optional styling for the print window
      printWindow.document.write('<style>');
      printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
      printWindow.document.write('#printableSection { font-size: 20px; padding: 20px; border: 2px solid black; border-radius: 10px; margin: 0 auto; width: 80%; box-sizing: border-box; }');
      printWindow.document.write('</style>');

      // Insert the content to be printed
      printWindow.document.write('</head><body>');
      printWindow.document.write('<div id="printableSection">');
      printWindow.document.write(printData);  // Dynamically insert the different data
      printWindow.document.write('</div>');
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();
      
      // Trigger the print
      printWindow.print();
      printWindow.close();
    }
  </script>

</body>
</html>

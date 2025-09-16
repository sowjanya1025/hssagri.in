<?php
session_start();
include'account.php';
$account =  new account();



if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$accountId = $_SESSION['user_id'];
$date = isset($_GET['date']) ? $_GET['date'] : null;


$items = $account->view_anaysis_day_three($accountId);


$itemData = [];
$dateColumns = [];

foreach ($items as $row) {
    $id_no = $row['id_no'];
    $itemName = $row['item_name'];
    $dateFormatted = date('d-m-Y', strtotime($row['date'])); // Convert date to dd-mm-yyyy

    $dateColumns[$dateFormatted] = true; 

  
    $label = "<b>" . htmlspecialchars($id_no) . ".</b>&nbsp;&nbsp;" . htmlspecialchars($itemName);

    $itemData[$label][$dateFormatted] = $row['total_cost'];
}


ksort($dateColumns);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Purchase Analysis</title>
    <?php require_once('header.php'); ?>
    <style>
        .content-wrapper { padding: 20px; }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 5px 10px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
        }
        tr {
            height: auto;
        }
    </style>
</head>
<body>

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

        <table>
            <thead>
                <tr>
                    <th rowspan="2">Item Name</th>
                    <th colspan="<?= count($dateColumns); ?>">Total Cost</th>
                </tr>
                <tr>
                    <?php foreach ($dateColumns as $date => $_): ?>
                        <th><?= htmlspecialchars($date); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemData as $itemName => $dateValues): ?>
                    <tr>
                        <td><?= $itemName; ?></td> 
                        <?php foreach ($dateColumns as $date => $_): ?>
                            <td>
                                <?= isset($dateValues[$date]) ? htmlspecialchars($dateValues[$date]) : '-'; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once('footer.php'); ?>
</body>
</html>
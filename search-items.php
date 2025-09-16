<?php
// search-items.php

// Simulated data (replace with your actual database query)
include'account.php';
$account =  new account();
$items = $account->getAll_item_names();
//print_r($items);
//$items =  json_encode($res);
//print_r($items);


/*$items = [
    ['id' => 1, 'name' => 'Onion', 'code' => 'ON001'],
    ['id' => 2, 'name' => 'Garlic', 'code' => 'GA002'],
    ['id' => 3, 'name' => 'Tomato', 'code' => 'TO003'],
    ['id' => 4, 'name' => 'Potato', 'code' => 'PO004'],
    ['id' => 5, 'name' => 'Lettuce', 'code' => 'LE005']
];

*/// Capture search term
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// If there's no search term, return all items
if ($searchTerm === '') {
    $filteredItems = $items;
} else {
    // Filter items based on search term
    $filteredItems = array_filter($items, function($item) use ($searchTerm) {
        return stripos($item['name'], $searchTerm) !== false;
    });
}

// Return JSON data
header('Content-Type: application/json');
echo json_encode(array_values($filteredItems));

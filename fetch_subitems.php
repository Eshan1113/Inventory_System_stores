<?php
require_once 'config.php';

// Fetch sub-items based on item_id and search query

    
$sub_items = $conn->query("SELECT id, sub_item_name FROM sub_item_list")->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode($sub_items);

<?php
require_once 'config.php';

// Fetch sub-items based on item_id and search query

    
$item_name = $conn->query("SELECT id, item_name FROM item_name_list")->fetchAll(PDO::FETCH_ASSOC);



    echo json_encode($item_name);

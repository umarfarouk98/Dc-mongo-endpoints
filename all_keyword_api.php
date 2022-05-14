<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once("../db.inc");

$collection = $db_connect->all_keywords;

if (isset($_GET['key_id'])) {
    $id = intval($_GET['key_id']);
    $searchCriteria = ['tid' => $id];
}else{
    $searchCriteria = [];
}

$keyword = $collection->find($searchCriteria);

foreach ($keyword as $index => $keys) {
    unset($keys['_id']);
    $keyword_data[] = $keys;
            
    }
    echo json_encode($keyword_data);

?>
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../db.inc");

$collection = $db_connect->tbl_category_try;

if (isset($_GET['cat_id'])) {
    $id = intval($_GET['cat_id']);
    // echo $id;
    $searchCriteria = ['id' => $id];
}else{
    $searchCriteria = [];
}

$findcateg_id = $collection->find($searchCriteria);

foreach ($findcateg_id as $index => $cat_id) {
    unset($cat_id['_id']);
    $categ_id_list[] = $cat_id;
            
        }
    echo json_encode($categ_id_list);

?>
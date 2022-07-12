<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once("../db.inc");

$collection = $db_connect->tbl_lang;

if (isset($_GET['lang_id'])) {
    $id = intval($_GET['lang_id']);
    // echo $id;
    $searchCriteria = ['id' => $id];
}else{
    $searchCriteria = [];
}

$findlang_id = $collection->find($searchCriteria);

foreach ($findlang_id as $index => $lang_id) {
    unset($lang_id['_id']);
    $lang_id_list[] = $lang_id;
            
        }
    echo json_encode($lang_id_list);

?>
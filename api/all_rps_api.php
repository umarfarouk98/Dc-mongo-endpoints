<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
    
    require_once("../db.inc");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    if (isset($_GET['rp_id'])){
        
        $searchCriteria = ['id' => intval($_GET['rp_id'])];
        $opt = ['sort' => ['id' => 1]];
        
    } 
    else {
        
        $searchCriteria = [];
        $opt = ['sort' => ['id' => 1]];
    }

    

$rpColl = $db_connect->tbl_rp;
$result = $rpColl->find($searchCriteria,$opt);

foreach ($result as $document) {
    unset($document['_id']);
    $document['img'] = str_replace(' ', '%20', $document['img']);
    $albums[] =  $document;
}

if(empty($albums)){
    echo "null";
}else{
    echo json_encode($albums); 
}

?>
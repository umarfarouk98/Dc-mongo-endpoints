<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
    
    require_once("../db.inc");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    if (isset($_GET['page'])) {
    
        $getpage = intval($_GET['page']);
        $limit = 20;
        $skip = ($getpage - 1) * $limit;
        
        $searchCriteria = [];
        $opt = ['limit' => $limit, 'sort' => ['id' => 1], 'skip' => $skip];
    
    } else {
        $limit = 20;
        $skip = 0;
        
        $searchCriteria = [];
        $opt = ['sort' => ['id' => 1], 'skip' => $skip];
    }

    

$rpColl = $db_connect->tbl_rp;
$result = $rpColl->find($searchCriteria,$opt);

foreach ($result as $document) {
    unset($document['_id']);
    $albums[] =  $document;
}
echo json_encode($albums); 

?>
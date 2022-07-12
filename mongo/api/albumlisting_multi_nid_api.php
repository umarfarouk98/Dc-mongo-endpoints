<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
if( (isset($_GET['id'])) ) {
    
    
    $ids = array_map('intval', explode(',', $_GET["id"]));
    
    // echo json_encode($ids);
    
    // $filt1 = [ 'id' => $ids ];
    $filt1 =['id' => ['$in' => $ids]];

//-----------//---------//---------//--------//--------//------------//----------//

    $db_find = $db_connect->tbl_album;
   
    // $searchCriteria = $match_try;
    // $opt = ['skip' => $skip, 'limit' => $limit];
    $result = $db_find->find($filt1);
    
    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }
    
    if (empty($albums)) {
        echo 'null';
    } else {
        $rewriteKey = array();
        $newArr = array();
        foreach ($albums as $key => $value) {
            // echo json_encode($value);
            
            $rewriteKey[$key]['title'] = $albums[$key]['name'];
            $rewriteKey[$key]['img'] = $albums[$key]['img'];
            $rewriteKey[$key]['nid'] = $albums[$key]['id'];
            $rewriteKey[$key]['lec_no'] = $albums[$key]['lec_no'];
    
            //---------------catch exception for empty cats-------------//
            if (empty($albums[$key]['language'])) {
                $rewriteKey[$key]['lang'] = "";
            } else {
                $rewriteKey[$key]['lang'] = $albums[$key]['language'];
            }
            if (empty($albums[$key]['categories'])) {
        
                $rewriteKey[$key]['categories'] = "";
            } else {
        
                $rewriteKey[$key]['categories'] = $albums[$key]['categories'];
            }
            
            
        }
        echo json_encode($rewriteKey);
    }
}else {
    echo 'null !!';
}
    
?>
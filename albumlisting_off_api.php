<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    

    require_once("../db.inc");

if (isset($_GET['offset'])) {
    
    $offset = intval($_GET['offset']);
    $db_find = $db_connect->tbl_album_try;
    
    $searchCriteria = [];
    $opt = ['skip' => $offset, 'limit' => 20];
    $result = $db_find->find($searchCriteria, $opt);
    
    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }

    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        
        // if ($albums[$key]['joinTab'][0]['lec_no'] == 0){
        //     echo "album is empty.......";
        // }else{
            
            // $rewriteKey[$key]['title'] = $albums[$key]['joinTab'][0]['name'];
            // $rewriteKey[$key]['img'] = $albums[$key]['joinTab'][0]['img'];
            // $rewriteKey[$key]['nid'] = $albums[$key]['joinTab'][0]['id'];
            // $rewriteKey[$key]['lec_no'] = $albums[$key]['joinTab'][0]['lec_no'];
            // $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab4'][0]['name'];
            
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
        
        // }
        
        
    }
    echo json_encode($rewriteKey);
} else {
    echo 'null';
}

?>
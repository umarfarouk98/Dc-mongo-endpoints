<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
    
if( (isset($_GET['lim'])) && (isset($_GET['langid'])) && (isset($_GET['key'])) && (isset($_GET['page'])) ) {
    
    $getpage = intval($_GET['page']);
    $limit = intval($_GET['lim']);
    $lang = intval($_GET['langid']);
    $key = strval($_GET['key']);
    
    $skip = ($getpage - 1) * $limit;
    $filt1 = ['lang_id' => $lang, 'key_id' => $key];
    
}else if (isset($_GET['key']) && (isset($_GET['langid'])) && (isset($_GET['page'])) ) {
    
    $getpage = intval($_GET['page']);
    $key = strval($_GET['key']);
    $lang = intval($_GET['langid']);
    $limit = 20;
    
    $skip = ($getpage - 1) * $limit;
    $filt1 = ['key_id' => $key, 'lang_id' => $lang];
    
    
}else if( (isset($_GET['key'])) && (isset($_GET['page'])) ) {
    
    $getpage = intval($_GET['page']);
    $key = strval($_GET['key']);
    $limit = 20;
    
    $skip = ($getpage - 1) * $limit;
    $filt1 = ['key_id' => $key];
    
}else {
    echo 'null';
}



//-------------------------------------------------------------------------//
    $db_find = $db_connect->tbl_album;
   
    $searchCriteria = $filt1;
    $opt = ['skip' => $skip, 'limit' => $limit];
    $result = $db_find->find($searchCriteria, $opt);
    
    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }
    
    if (empty($albums)) {
        echo 'null';
    } else {
        $rewriteKey = array();
        foreach ($albums as $key => $value) {
    
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
            if (empty($albums[$key]['Keywords'])) {
    
                $rewriteKey[$key]['Keyword'] = "";
            } else {
                
                $rewriteKey[$key]['keyword'] = $albums[$key]['Keywords'];
            }
            
            if (empty($albums[$key]['key_id'])) {
    
                $rewriteKey[$key]['key_id'] = "";
            } else {
                
                $rewriteKey[$key]['key_id'] = $albums[$key]['key_id'];
            }
            
            if (isset($albums[$key]['downloads'])){
                $albums[$key]['downloads'] = $albums[$key]['downloads'];
                $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
            }else{
                $albums[$key]['downloads'] = 0;
                $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
            }
            
            if (isset($albums[$key]['views'])){
                
                $albums[$key]['views'] = $albums[$key]['views'];
                $rewriteKey[$key]['views'] = $albums[$key]['views'];
            }else{
                $albums[$key]['views'] = 0;
                 $rewriteKey[$key]['views'] = $albums[$key]['views'];
            }
            
        }
        echo json_encode($rewriteKey);
    }

?>
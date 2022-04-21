<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
    
    require_once("../db.inc");
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


if ( isset($_GET['page']) && (isset($_GET['lim'])) && (isset($_GET['langid'])) ) {
    
    $getpage = intval($_GET['page']);
    $limit = intval($_GET['lim']);
    $lang = intval($_GET['langid']);
    
    // $limit = 20;
    $skip = ($getpage - 1) * $limit;
    $match_try = ['lang_id' => $lang];
    
} else if(( isset($_GET['page']) && (isset($_GET['lim'])))){ //match_try = [];
    
    $getpage = intval($_GET['page']);
    $limit = intval($_GET['lim']);
    $skip = ($getpage - 1) * $limit;
    
    $match_try = [] ;
        
} else if(( isset($_GET['page']) && (isset($_GET['langid'])))){ //limt = 20 default
    
    $getpage = intval($_GET['page']);
    $limit = 20;
    $lang = intval($_GET['langid']);
    $skip = ($getpage - 1) * $limit;
    
    $match_try = ['lang_id' => $lang];
    
} else if(( isset($_GET['page']))) {
    
    $getpage = intval($_GET['page']);
    $limit = 20;
    $skip = ($getpage - 1) * $limit;
    
    $match_try = [];
}
else {
    echo 'you must specify an argument';
}
//-------------------------------------------------------------------------//
    $db_find = $db_connect->tbl_album_new;
   
    $searchCriteria = $match_try;
    $opt = ['skip' => $skip, 'limit' => $limit];
    $result = $db_find->find($searchCriteria, $opt);
    
    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }

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


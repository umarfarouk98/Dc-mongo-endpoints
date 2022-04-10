<?php
header("Content-Type: application/json; charset=UTF-8");
    
    header("Access-Control-Allow-Origin: https://www.dawahbox.com/mongo/api/");
    
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
    $match_try = ['$match' => ['lang_id' => $lang]];
    
} else if(( isset($_GET['page']) && (isset($_GET['lim'])))){ //match_try = [];
    
    $getpage = intval($_GET['page']);
    $limit = intval($_GET['lim']);
    $skip = ($getpage - 1) * $limit;
    
    $match_try = [ '$sort' => [ 'lang_id' => 1] ];
        
} else if(( isset($_GET['page']) && (isset($_GET['langid'])))){ //limt = 20 default
    
    $getpage = intval($_GET['page']);
    $limit = 20;
    $lang = intval($_GET['langid']);
    $skip = ($getpage - 1) * $limit;
    
    $match_try = ['$match' => ['lang_id' => $lang]];
    
} else if(( isset($_GET['page']))) {
    
    $getpage = intval($_GET['page']);
    $limit = 20;
    $skip = ($getpage - 1) * $limit;
    
    $match_try = [ '$sort' => [ 'lang_id' => 1] ];
}
else {
    echo 'you must specify an argument';
}
//-------------------------------------------------------------------------//
    $db_find = $db_connect->tbl_mp3;
    $ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_album_try', //from: <collection2 to join>,
            'localField' => 'album_id', //localField: <field from the input documents1>,
            'foreignField' => 'aid',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>
        ],
    ];
    $ops2 = [ // (1)
        '$lookup' => [
            'from' => 'tbl_category_try', //from: <collection2 to join>,
            'localField' => 'cat_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab2'     //as: <output array field>
        ],
    ];
    $ops3 = [ // (1)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab3'     //as: <output array field>
        ],
    ];
    $ops4 = [ // (1)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab4'     //as: <output array field>
        ],
    ];
    $result = $db_find->aggregate(
        [
            $match_try,
            $ops,
            $ops2,
            $ops3,
            $ops4,
            ['$skip' => $skip],
            ['$limit' => $limit],
            // ['$count' => 'id']
        ]
    );
    
    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }
    

    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        $rewriteKey[$key]['title'] = $albums[$key]['joinTab'][0]['album_name'];
        $rewriteKey[$key]['img'] = $albums[$key]['joinTab'][0]['album_image'];
        $rewriteKey[$key]['nid'] = $albums[$key]['joinTab'][0]['aid'];
        // $rewriteKey[$key]['cats'] = $albums[$key]['joinTab2'][0]['name'];
        
        if (empty($albums[$key]['joinTab3'][0])) {
             $rewriteKey[$key]['lang'] = "nil";
        }else{
            $rewriteKey[$key]['lang'] = $albums[$key]['joinTab3'][0]['name'];
        }
        
        // $rewriteKey[$key]['lang'] = $albums[$key]['joinTab3'][0]['name'];
        // $rewriteKey[$key]['lec_no'] = $albums[$key][''];
        // $rewriteKey[$key]['reciter'] = $albums[$key][''];
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab4'][0]['name'];
    }
    echo json_encode($rewriteKey);


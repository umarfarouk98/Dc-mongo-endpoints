<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once("../db.inc");

//----------------------------------------------------------------------------//

if (isset($_GET['page']) && (isset($_GET['aid']))) {

    $getpage = intval($_GET['page']);
    function aggQuery($op1, $op2){
        global $getpage;
        $args = [
            ['$match' => ['album_id' => intval($_GET['aid'])]],
            $op1,
            $op2,
            ['$skip' => ($getpage - 1) * 20],
            ['$limit' => 20]
        ];
        return $args;
    }

    
}else if ((isset($_GET['aid']))) {
    
    function aggQuery($op1, $op2){
        // global $limit;
        $args = [
            ['$match' => ['album_id' => intval($_GET['aid'])]],
            $op1,
            $op2,
            ['$skip' => 0]
        ];
        return $args;
    }
    
}else {
    
    echo 'you must specify an argument';
}
//-----------//---------//---------//--------//--------//------------//----------//

    $db_find = $db_connect->tbl_mp3;
    $ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_album', //from: <collection2 to join>,
            'localField' => 'album_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>
        ],
    ];
    $ops2 = [ // (2)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            // 'pipeline' => [
            //     [ '$match' => [ 'lec_no'=> ['$gte' => 0 ] ] ]
            //   ],
            'as' => 'joinTab2'     //as: <output array field>
        ],
    ];
    
    // { $match: { $or: [ { score: { $gt: 70, $lt: 90 } }, { views: { $gte: 1000 } } ] } }
    
    $result = $db_find->aggregate(
        aggQuery($ops,$ops2)
    );
//-----------//---------//---------//--------//--------//------------//----------//

    foreach ($result as $document) {
        unset($document['_id']);
        $albums[] =  $document;
    }
    // echo $albums;
//--------------------------------------------//
    $rewriteKey = array();
    foreach ($albums as $key => $value){
        $rewriteKey[$key]['lectitle'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['album_name'] = $albums[$key]['joinTab'][0]['name'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['mp3_size'] = $albums[$key]['mp3_size'];
        $rewriteKey[$key]['img'] = $albums[$key]['img'];
        $rewriteKey[$key]['rp'] = $albums[$key]['joinTab2'][0]['name'];
        $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
        
        
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

?>
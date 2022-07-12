<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once("../db.inc");

if( (isset($_GET['lim'])) && (isset($_GET['langid'])) ) {
    
    $limit = intval($_GET['lim']);
    $lang = intval($_GET['langid']);
    $filt1 = ['$match' => ['lang_id' => $lang]];
    
}else if (isset($_GET['lecid'])) {
    
    $id = intval($_GET['lecid']);
    $filt1 = ['$match' => ['id' => $id]];
    $limit = 1;
    
}else if( (isset($_GET['lim']))) {
    
    $limit = intval($_GET['lim']);
    $filt1 = ['$limit' => $limit];
    
}else {
    echo 'argument needed!';
}

$collection = $db_connect->tbl_mp3; 
$ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>

        ],

    ];
$ops2 = [ // (3)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab2'     //as: <output array field>
        ],
    ];

$result = $collection->aggregate(

        [

            $filt1,
            $ops,
            $ops2,
            ['$limit' => $limit],
            
        ]
    );
// echo json_encode($result);
// $db_find = $db_connect->tbl_mp3_new->find($filt1, $filt2);
    
    foreach ($result as $document) {
        unset($document['_id']);
        $albums[] =  $document;
    }
    // echo json_encode($albums);
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['img'];
        $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
        $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
        
        if (isset($albums[$key]['views'])){
            $albums[$key]['views'] = $albums[$key]['views'];
            $rewriteKey[$key]['views'] = $albums[$key]['views'];
        }else{
            $albums[$key]['views'] = 0;
            $rewriteKey[$key]['views'] = $albums[$key]['views'];
        }
        if (isset($albums[$key]['downloads'])){
                
            $albums[$key]['downloads'] = $albums[$key]['downloads'];
            $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
        }else{
            $albums[$key]['downloads'] = 0;
            $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
        }

        if (empty($albums[$key]['joinTab2'][0])) {
            $rewriteKey[$key]['lang'] = "";
        } else {
            $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
        }
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab2'][0]['name'];
    }
    echo json_encode($rewriteKey);
    
?>
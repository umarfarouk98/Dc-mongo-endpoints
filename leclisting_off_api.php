<?php

    header("Content-Type: application/json; charset=UTF-8");
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once("../db.inc");

if (isset($_GET['offset'])) {
    $offset = intval($_GET['offset']);
    $db_find = $db_connect->tbl_mp3;
    
    $ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_category_try', //from: <collection2 to join>,
            'localField' => 'cat_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>
        ],
    ];
    $ops2 = [ // (2)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab2'     //as: <output array field>
        ],
    ];
    $ops3 = [ // (3)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab3'     //as: <output array field>
        ],
    ];
    $result = $db_find->aggregate(
        [
            // ['$unset' => ['_id' => '']],
            // ['$match' => ['cat_id' => 14]],
            // ['$project' => ["mp3_artist" => 1]],
            $ops,
            $ops2,
            $ops3,
            ['$skip' => $offset],
            ['$limit' => 100],
        ]
    );
    foreach ($result as $document) {
        unset($document['_id']);
        $albums[] =  $document;
    }
    // echo json_encode($albums);
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['mp3_thumbnail'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab2'][0]['name'];
        $rewriteKey[$key]['reciter'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab3'][0]['name'];
        // $rewriteKey[$key]['cats'] = $albums[$key]['joinTab'][0]['name'];
        $rewriteKey[$key]['cats'] = ['boys'];
    }
    echo json_encode($rewriteKey);
} else {
    echo 'null';
} 
?>
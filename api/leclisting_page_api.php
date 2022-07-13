<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
if (isset($_GET['page'])) {

    $getpage = intval($_GET['page']);

    $limit = 20;
    $skip = ($getpage - 1) * $limit;
} else {
    $limit = 20;
    $skip = 0;
}



$db_find = $db_connect->tbl_mp3;

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
$result = $db_find->aggregate(
    [

        $ops,
        $ops2,

        ['$skip' => $skip],
        ['$limit' => $limit],
    ]
);
foreach ($result as $document) {
    unset($document['_id']);
    $albums[] =  $document;
}
// echo json_encode($albums);





if (empty($albums)) {
    echo 'null';
} else {
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['lec_thumbnail'];
        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
        $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
        $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];

        $rewriteKey[$key]['rp'] = $albums[$key]['joinTab2'][0]['name'];
        
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
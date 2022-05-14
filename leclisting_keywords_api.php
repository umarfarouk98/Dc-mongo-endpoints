<?php

    header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
if( (isset($_GET['lim'])) && (isset($_GET['langid'])) && (isset($_GET['key'])) ) {
    
    $limit = intval($_GET['lim']);
    $lang = intval($_GET['langid']);
    $key = intval($_GET['key']);
    
    $filt1 = ['$match' => ['lang_id' => $lang, 'key_id' => $key]];
    
}else if (isset($_GET['key']) && (isset($_GET['langid'])) ) {
    
    $key = intval($_GET['key']);
    $lang = intval($_GET['langid']);
    $filt1 = ['$match' => ['key_id' => $key, 'lang_id' => $lang]];
    $limit = 20;
    
}else if( (isset($_GET['key']))) {
    
    $key = intval($_GET['key']);
    $limit = 20;
    $filt1 = ['$match' => ['key_id' => $key]];
    
}else {
    echo 'argument needed!';
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
        $filt1,
        $ops,
        $ops2,
        // ['$skip' => $skip],
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
        $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['img'];
        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
        $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
        $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
        
        if (empty($albums[$key]['joinTab'][0])) {
            $rewriteKey[$key]['lang'] = "";
        } else {
            $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
        }
        
        if (empty($albums[$key]['cat_name'])) {

            $rewriteKey[$key]['cats'] = "";
        } else {

            $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
        }
        
        if (empty($albums[$key]['key_name'])) {

            $rewriteKey[$key]['keyword'] = "";
        } else {
            
            $rewriteKey[$key]['keyword'] = $albums[$key]['key_name'];
        }
        
        if (empty($albums[$key]['key_id'])) {

            $rewriteKey[$key]['key_id'] = "";
        } else {
            
            $rewriteKey[$key]['key_id'] = $albums[$key]['key_id'];
        }
        
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab2'][0]['name'];
        
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
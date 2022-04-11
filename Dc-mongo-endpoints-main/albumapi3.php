<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://www.dawahbox.com/mongo/api/");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("db_monconfig.php");
if (isset($_GET['page']) && (isset($_GET['aid']))) {

    $getpage = intval($_GET['page']);
    $id = intval($_GET['aid']);

    // $limit = intval($_GET['lim']);
    // $lang = intval($_GET['langid']);

    $limit = 20;
    $skip = ($getpage - 1) * $limit;
    // $match_try = ['$match' => ['lang_id' => $lang]];
} else if ((isset($_GET['aid']))) {
    $id = intval($_GET['aid']);

    // $getpage = intval($_GET['page']);
    $skip = 0;
    $limit = 20;
    // $skip = ($getpage - 1) * $limit;

    // $match_try = ['$sort' => ['lang_id' => 1]];
} else {
    echo 'you must specify an argument';
}



// $id = intval($_GET['aid']);
// echo $id;
$db_find = $db_connect->tbl_mp3;
$ops = [ // (1)
    '$lookup' => [
        'from' => 'tbl_album_try', //from: <collection2 to join>,
        'localField' => 'album_id', //localField: <field from the input documents1>,
        'foreignField' => 'aid',  //foreignField: <field from the documents of the "from" collection2>,
        'as' => 'joinTab'     //as: <output array field>
    ],
];
$ops2 = [ // (2)
    '$lookup' => [
        'from' => 'tbl_rp', //from: <collection2 to join>,
        'localField' => 'rp_id', //localField: <field from the input documents1>,
        'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
        'as' => 'joinTab2'     //as: <output array field>
    ],
];


$result = $db_find->aggregate(
    [
        ['$match' => ['album_id' => $id]],
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

$rewriteKey = array();
$newArr = array();
foreach ($albums as $key => $value) {
    $rewriteKey[$key]['lectitle'] = $albums[$key]['mp3_title'];
    $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
    $rewriteKey[$key]['album_name'] = $albums[$key]['joinTab'][0]['album_name'];
    $rewriteKey[$key]['nid'] = $albums[$key]['id'];
    $rewriteKey[$key]['mp3_size'] = $albums[$key]['mp3_size'];
    // $rewriteKey[$key]['reciter'] = $albums[$key][''];
    $rewriteKey[$key]['rp'] = $albums[$key]['joinTab2'][0]['name'];
    $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
}
echo json_encode($rewriteKey);
<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://www.dawahbox.com/mongo/api/");

require_once("db_monconfig.php");
if (isset($_GET['page']) && (isset($_GET['catid']))) {

    $getpage = intval($_GET['page']);
    $id = $_GET['catid'];

    // $limit = intval($_GET['lim']);
    // $lang = intval($_GET['langid']);

    $limit = 20;
    $skip = ($getpage - 1) * $limit;
    // $match_try = ['$match' => ['lang_id' => $lang]];
} else if ((isset($_GET['catid']))) {
    $id = $_GET['catid'];

    // $getpage = intval($_GET['page']);
    $skip = 0;
    $limit = 20;
    // $skip = ($getpage - 1) * $limit;

    // $match_try = ['$sort' => ['lang_id' => 1]];
} else {
    echo 'you must specify an argument';
}



//----------------------------------------------------//
//--------------table left join-----------------------//
$collection = $db_connect->tbl_mp3; //to: <collection1 to join>,
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
$result = $collection->aggregate(
    [
        ['$match' => ['cat_id' => $id]],
        // ['$project' => ["mp3_artist" => 1]],
        $ops,
        $ops2,
        $ops3,
        ['$skip' => $skip],
        ['$limit' => $limit],
    ]
);
foreach ($result as $row) {
    unset($row['_id']);
    $albums[] = $row;
}
$rewriteKey = array();
$newArr = array();
foreach ($albums as $key => $value) {
    $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
    $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
    // $rewriteKey[$key]['cats'] = $albums[$key]['joinTab'][0]['name'];
    // $rewriteKey[$key]['lang'] = $albums[$key]['joinTab2'][0]['name'];
    // $rewriteKey[$key]['keyword'] = $albums[$key][''];
    if (empty($albums[$key]['joinTab3'][0])) {
        $rewriteKey[$key]['lang'] = "nil";
    } else {

        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab3'][0]['name'];
    }
    if (empty($albums[$key]['cat_name'])) {

        $rewriteKey[$key]['cats'] = "nil";
    } else {

        $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
    }
    // $rewriteKey[$key]['img'] = $albums[$key]['mp3_thumbnail'];
    $rewriteKey[$key]['nid'] = $albums[$key]['id'];
    $rewriteKey[$key]['mp3_size'] = $albums[$key]['mp3_size'];
    // $rewriteKey[$key]['reciter'] = $albums[$key][''];
    $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab3'][0]['name'];
}
echo json_encode($rewriteKey);
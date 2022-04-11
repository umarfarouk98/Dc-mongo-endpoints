<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://www.dawahbox.com/mongo/api/");

require_once("db_monconfig.php");

if (isset($_GET['offset'])) {
    $offset = intval($_GET['offset']);
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
            'from' => 'tbl_category_try', //from: <collection2 to join>,
            'localField' => 'cat_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab2'     //as: <output array field>
        ],
    ];
    $ops3 = [ // (3)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab3'     //as: <output array field>
        ],
    ];
    $ops4 = [ // (4)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab4'     //as: <output array field>
        ],
    ];
    $result = $db_find->aggregate(
        [
            $ops,
            $ops2,
            $ops3,
            $ops4,
            ['$skip' => $offset],
            ['$limit' => 20],
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
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];

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
        // $rewriteKey[$key]['lec_no'] = $albums[$key][''];
        // $rewriteKey[$key]['reciter'] = $albums[$key][''];
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab4'][0]['name'];
    }
    echo json_encode($rewriteKey);
} else {
    echo 'null';
}
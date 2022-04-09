<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../db.inc");

if (isset($_GET['rpid'])) {
    $id = intval($_GET['rpid']);
    // $strid = strval($_GET['id']);


    //----------------------------------------------------//
    //--------------table left join-----------------------//
    $collection = $db_connect->tbl_mp3; //to: <collection1 to join>,



    $ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_rp', //from: <collection2 to join>,
            'localField' => 'rp_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>
        ],

    ];
    $ops2 = [ // (1)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab2'     //as: <output array field>

        ],

    ];
    $ops3 = [ // (1)
        '$lookup' => [
            'from' => 'tbl_category_try', //from: <collection2 to join>,
            'localField' => 'cat_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab3'     //as: <output array field>

        ],

    ];

    $result = $collection->aggregate(

        [
            // ['$unset' => ['_id' => '']],

            ['$match' => ['rp_id' => $id]],

            // ['$project' => ['mp3_title' => 1]],
            $ops,
            $ops2,
            $ops3,
            // ['$skip' => $start],
            // ['$limit' => $limit],
            
        ]
    );



    foreach ($result as $row) {
        unset($row['_id']);
        // unset($row['joinTab'][0]['_id']);
        $albums[] = $row;
    }
    // echo json_encode($albums);
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        // $rewriteKey['mp3_title'] = 'mummy';
        $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['cats'] = $albums[$key]['joinTab3'][0]['name'];
        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab2'][0]['name'];
        // $rewriteKey[$key]['keyword'] = $albums[$key][''];
        $rewriteKey[$key]['img'] = $albums[$key]['mp3_thumbnail'];
        // $rewriteKey[$key]['album_name'] = $albums[$key]['album_title'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        // $rewriteKey[$key]['reciter'] = $albums[$key][''];
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab'][0]['name'];
    }
    echo json_encode($rewriteKey);
} else {
    echo json_encode(['message => error']);
}

?>
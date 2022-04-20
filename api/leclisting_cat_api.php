<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once("../db.inc");
//----------------------------------------------------------------------------//
if (isset($_GET['page']) && (isset($_GET['catid'])) && (isset($_GET['langid'])) ) {

    $getpage = intval($_GET['page']);
    $id = $_GET['catid'];
    $lang = intval($_GET['langid']);

    $limit = 20;
    $skip = ($getpage - 1) * $limit;
    $match_try = ['$match' => ['lang_id' => $lang, 'cat_id' => $id]];

    
}else if ((isset($_GET['catid'])) && (isset($_GET['page'])) ) {
    
    $getpage = intval($_GET['page']);
    $id = $_GET['catid'];

    
    $limit = 100;
    $skip = ($getpage - 1) * $limit;
    $match_try = ['$match' => ['cat_id' => $id]];

}else if ((isset($_GET['catid']))) {
    
    $id = $_GET['catid'];

    $skip = 0;
    $limit = 20;
    $match_try = ['$match' => ['cat_id' => $id]];

}
else {
    echo 'you must specify an argument';
}
//-----------//---------//---------//--------//--------//------------//----------//


    //----------------------------------------------------//
    //--------------table left join-----------------------//
    $collection = $db_connect->tbl_mp3; //to: <collection1 to join>,
    
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
            $match_try,
            $ops2,
            $ops3,
            ['$skip' => $skip],
            ['$limit' => $limit],
        ]
    );
//-----------//---------//---------//--------//--------//------------//----------//

    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['cats'] = $albums[$key]['cat_id'];
        $rewriteKey[$key]['cats name'] = $albums[$key]['cat_name'];
        $rewriteKey[$key]['mp3_size'] = $albums[$key]['mp3_size'];
        
        if (empty($albums[$key]['joinTab2'][0])) {
            $rewriteKey[$key]['lang'] = "";
        } else {
    
            $rewriteKey[$key]['lang'] = $albums[$key]['joinTab2'][0]['name'];
        }
        
        if (empty($albums[$key]['joinTab3'][0])) {
            $rewriteKey[$key]['rpname'] = "";
        } else {
    
            $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab3'][0]['name'];
        }
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['img'];
    }
    echo json_encode($rewriteKey);

?>
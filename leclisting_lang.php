<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    require_once("../db.inc");
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
//----------------------------------------------------------------------------//
if (isset($_GET['page']) && (isset($_GET['langid']))) {

    $getpage = intval($_GET['page']);
    $id = intval($_GET['langid']);

    $limit = 20;
    $skip = ($getpage - 1) * $limit;

    
} else if ((isset($_GET['langid']))) {
    
    $id = intval($_GET['langid']);

    $skip = 0;
    $limit = 20;
   
} else {
    echo 'you must specify an argument';
}

//-----------//---------//---------//--------//--------//------------//----------//
    //----------------------------------------------------//
    //--------------table left join-----------------------//
    $collection = $db_connect->tbl_mp3; //to: <collection1 to join>,
    $ops = [ // (1)
        '$lookup' => [
            'from' => 'tbl_lang', //from: <collection2 to join>,
            'localField' => 'lang_id', //localField: <field from the input documents1>,
            'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
            'as' => 'joinTab'     //as: <output array field>
        ],
    ];
    // $ops2 = [ // (2)
    //     '$lookup' => [
    //         'from' => 'tbl_category_try', //from: <collection2 to join>,
    //         'localField' => 'cat_id', //localField: <field from the input documents1>,
    //         'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
    //         'as' => 'joinTab2'     //as: <output array field>
    //     ],
    // ];
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
            ['$match' => ['lang_id' => $id]],
            $ops,
            // $ops2,
            $ops3,
            ['$skip' => $skip],
            ['$limit' => 100],
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
        
        $rewriteKey[$key]['title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['img'];
        $rewriteKey[$key]['nid'] = $albums[$key]['id'];
        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab3'][0]['name'];
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
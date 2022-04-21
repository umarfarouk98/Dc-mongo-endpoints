<?php
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
require_once("../db.inc");
//----------------------------------------------------------------------------//
if (isset($_GET['page']) && (isset($_GET['catid']))) {

    $getpage = intval($_GET['page']);
    $id = $_GET['catid'];


    $limit = 20;
    $skip = ($getpage - 1) * $limit;

    
}else if ((isset($_GET['catid']))) {
    
    $id = strval($_GET['catid']);

    $skip = 0;
    $limit = 20;

}else {
    echo 'you must specify an argument';
}
//-----------//---------//---------//--------//--------//------------//----------//


    //----------------------------------------------------//
    //--------------table left join-----------------------//
    // echo $id;
    $collection = $db_connect->tbl_mp3; //to: <collection1 to join>,
    
//----------------------------------------------------------------------------//


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
    
    $result = $collection -> aggregate(
        [
            ['$match' => ['cat_id' => $id]],
            $ops2,
            $ops3,
            ['$skip' => $skip],
            ['$limit' => 100],
        ]
    );
    // $search = [$id => ['$all' => ['40260', '40300', '40375'] ] ];
    // $search = ['rp_id' => ['$all' => [33, 34]]];
    // $search = [
    //         'cat_id' => $id,
    //     ];
    // $opt = ['limit' => 100];
    // $result = $collection->find($search, $opt);
//-----------//---------//---------//--------//--------//------------//----------//

    foreach ($result as $row) {
        unset($row['_id']);
        $albums[] = $row;
    }
    // echo json_encode($albums);
    
    $rewriteKey = array();
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
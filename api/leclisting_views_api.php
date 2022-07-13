<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once("../db.inc");
    
    
    
   //-----------post to mongo------------------------------------------//
    function post_to_mongo($albums){
        
        global $db_connect;
        $mpcoll = $db_connect->tbl_mp3;
        
        
        $document = $mpcoll->updateOne(
            [
                "id" => $albums['id'],
                "status" => $albums['status'],
                "cat_id" => $albums['cat_id'],
                "album_id" => $albums['album_id'],
            ],
            [
                
                 '$inc' => ['views' => 1]
            ],
            ['upsert' => False]
        );
    }
    // post_to_mongo();
    //--------------------------------------------------------
    function ret_req_data($condition, $limit){
        
        global $db_connect;
        $collection = $db_connect->tbl_mp3; 
    
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
        
        $ops3 = [ // (3)
            '$lookup' => [
                'from' => 'tbl_album', //from: <collection2 to join>,
                'localField' => 'album_id', //localField: <field from the input documents1>,
                'foreignField' => 'id',  //foreignField: <field from the documents of the "from" collection2>,
                'as' => 'joinTab3'     //as: <output array field>
            ],
        ];
        
        $result = $collection->aggregate(
            [
                $condition,
                $ops,
                $ops2,
                $ops3,
                ['$limit' => $limit]
            ]
        );
        
        //--------------------------------//
        foreach ($result as $document) {
            unset($document['_id']);
            $albums[] =  $document;
        }
    
        if (empty($albums)) {
            echo 'null';
        } else {
            $rewriteKey = array();
            foreach ($albums as $key => $value) {
                
                $rewriteKey[$key]['nid'] = $albums[$key]['id'];
                $rewriteKey[$key]['post_date'] = $albums[$key]['updated_date'];
                $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
                $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
                $rewriteKey[$key]['img'] = $albums[$key]['img'];
                $rewriteKey[$key]['lec_thumbnail'] = $albums[$key]['lec_thumbnail'];
                $rewriteKey[$key]['lec_img'] = $albums[$key]['lec_img'];
                $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
                $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
                $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
                $rewriteKey[$key]['album_id'] = $albums[$key]['album_id'];
                $rewriteKey[$key]['mp3_size'] = $albums[$key]['mp3_size'];
                $rewriteKey[$key]['amr_size'] = $albums[$key]['amr_size'];
                $rewriteKey[$key]['cat_id'] = $albums[$key]['cat_id'];
                $rewriteKey[$key]['lang_id'] = $albums[$key]['lang_id'];
                
                
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
                
                if (empty($albums[$key]['joinTab'][0])) {
                    $rewriteKey[$key]['lang'] = "";
                }else {
                    $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
                }
                
                if (empty($albums[$key]['joinTab2'][0])) {
                    $rewriteKey[$key]['rpname'] = "";
                }else{
                    $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab2'][0]['name'];   
                }
                
                if (empty($albums[$key]['joinTab3'][0])) {
                    $rewriteKey[$key]['album_name'] = "";
                }else {
                    $rewriteKey[$key]['album_name'] = $albums[$key]['joinTab3'][0]['name'];
                }
                
            //----------------------------//
                post_to_mongo($albums[0]);
                
            }
            return json_encode($rewriteKey);
        }
        //---------------------------------------------------------------//
    }
    
    //--------------init func-------------------------
    function __initializer__($condition, $limit){
        
        echo ret_req_data($condition, $limit);
    }
    // __initializer__();
    

?>
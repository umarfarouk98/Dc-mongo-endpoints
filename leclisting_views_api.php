<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once("../db.inc");
    
    
    
   //-----------post to mongo------------------------------------------
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
                '$set' => [
                    
                    "id" => $albums['id'],
                    "status" => $albums['status'],
                    "album_id" => $albums['album_id'],
                    "rp_id" => $albums['rp_id'],
                    "lang_id" => $albums['lang_id'],
                    "key_id" => $albums['key_id'],
                    "downloads"  => $albums['downloads'],
                    "views"  => $albums['views'] + 1,
                    "cat_id" => $albums['cat_id'],
                    "mp3_title" => $albums['mp3_title'],
                    "mp3_url" => $albums['mp3_url'],
                    "mp3_thumbnail" => $albums['mp3_thumbnail'],
                    "cat_name" => $albums['cat_name'],
                    "key_name" => $albums['key_name'],
                    "mp3_duration" => $albums['mp3_duration'],
                    "mp3_description" => $albums['mp3_description'],
                    "mp3_share_url" => $albums['mp3_share_url'],
                    "img" => $albums['img'],
                    "mp3_size" => $albums['mp3_size'],
                    
                ]
            ],
            ['upsert' => true]
        );
    }
    // post_to_mongo();
    //--------------------------------------------------------
    function ret_req_data(){
        
        global $db_connect;

        if( (isset($_GET['id'])) ) {
            
            $id = intval($_GET['id']);
            $filt = ['$match' =>['id' => $id] ];
        
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
            $result = $collection->aggregate(
                [
                    $filt,
                    $ops,
                    $ops2,
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
                    $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
                    $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
                    $rewriteKey[$key]['img'] = $albums[$key]['img'];
                    $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
                    $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
                     $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
                    
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
                    
                    
                //----------------------------//
                    post_to_mongo($albums[0]);
                    
                }
                return json_encode($rewriteKey);
            }
            //---------------------------------------------------------------//
            
        //----------------------------------
        }else {
            echo 'argument needed!';
        }
    }
    
    //--------------init func-------------------------
    function __initializer__(){
        if (isset($_GET['api_key'])) {
            
            global $db_connect;
            $keycoll = $db_connect->tbl_keys;
            $key = $_GET['api_key'];
            $filt = [
                'key' => $key,
                // 'api' => 'download_api'
            ];
            
            $result = $keycoll->find($filt);
            foreach($result as $index => $dat){
                 if($dat['api'] == 'mp3_view'){
                    echo ret_req_data();
                }else{
                    echo 'null';
                }
            }
           
        }else{
            echo 'null';
        }
    }
    __initializer__();
    

?>
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
        $mpcoll = $db_connect->tbl_mp3_try;
        
        
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
                    "downloads"  => $albums['downloads'] + 1,
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
            $filt = ['id' => $id];
        
            $collection = $db_connect->tbl_mp3_try; 
            $db_find = $collection->find($filt);
        
            foreach ($db_find as $document) {
                unset($document['_id']);
                $albums[] =  $document;
            }
        
            if (empty($albums)) {
                echo 'empty';
            } else {
                $rewriteKey = array();
                foreach ($albums as $key => $value) {
                    
                    $rewriteKey[$key]['nid'] = $albums[$key]['id'];
                    $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
                    $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
                    $rewriteKey[$key]['img'] = $albums[$key]['img'];
                    
                    if (isset($albums[$key]['downloads'])){
                        
                        $albums[$key]['downloads'] = $albums[$key]['downloads'];
                    }else{
                        $albums[$key]['downloads'] = 0;
                    }
                    
                //----------------------------//
                    post_to_mongo($albums[0]);
                    
                }
                return json_encode($rewriteKey);
            }
            
            
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
                 if($dat['api'] == 'download_api'){
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
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
                
                 '$inc' => ['downloads' => 1]
            ],
            ['upsert' => False]
        );
        
    }
    // post_to_mongo();
    //--------------------------------------------------------
    function ret_req_data(){
        
        global $db_connect;

        if( (isset($_GET['id'])) ) {
            
            $id = intval($_GET['id']);
            $filt = ['id' => $id];
        
            $collection = $db_connect->tbl_mp3; 
            $db_find = $collection->find($filt);
        
            foreach ($db_find as $document) {
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
                    $rewriteKey[$key]['amr_url'] = $albums[$key]['amr_url'];
                    $rewriteKey[$key]['img'] = $albums[$key]['lec_thumbnail'];
                    $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
                    $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
                    
                    if (isset($albums[$key]['downloads'])){
                        
                        $albums[$key]['downloads'] = $albums[$key]['downloads'];
                    }else{
                        $albums[$key]['downloads'] = 0;
                    }
                    if (isset($albums[$key]['views'])){
                        $albums[$key]['views'] = $albums[$key]['views'];
                        $rewriteKey[$key]['views'] = $albums[$key]['views'];
                    }else{
                        $albums[$key]['views'] = 0;
                        $rewriteKey[$key]['views'] = $albums[$key]['views'];
                    }
                    
                //----------------------------//
                    post_to_mongo($albums[0]);
                    
                }
                return json_encode($rewriteKey);
            }
            
            
        //----------------------------------
        }else {
            echo 'null';
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
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
        $mpcoll = $db_connect->tbl_album;
        
        
        $document = $mpcoll->updateOne(
            [
                    "id" => $albums['id'],
                    "name" => $albums['name'],
            ],
            [
                '$set' => [
                    
                    "id" => $albums['id'],
                    "name" => $albums['name'],
                    "img" => $albums['img'],
                    
                    "rp_id" => $albums['rp_id'],
                    "lang_id" => $albums['lang_id'],
                    "lec_no" => $albums['lec_no'],
                    
                    "language" => $albums['language'],
                    "categories" => $albums['categories'],
                    
                    "downloads"  => $albums['downloads'],
                    "views"  => $albums['views'] + 1,
                    
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
        
            $collection = $db_connect->tbl_album;
            $result = $collection->find($filt);
            
            //--------------------------------//
            foreach ($result as $document) {
                unset($document['_id']);
                $albums[] =  $document;
            }
        
            if (empty($albums)) {
                echo 'empty';
            } else {
                $rewriteKey = array();
                foreach ($albums as $key => $value) {
                    
                    $rewriteKey[$key]['title'] = $albums[$key]['name'];
                    $rewriteKey[$key]['img'] = $albums[$key]['img'];
                    $rewriteKey[$key]['nid'] = $albums[$key]['id'];
                    $rewriteKey[$key]['lec_no'] = $albums[$key]['lec_no'];
            
                    //---------------catch exception for empty cats-------------//
                    if (empty($albums[$key]['language'])) {
                        $rewriteKey[$key]['lang'] = "";
                    }else {
                        $rewriteKey[$key]['lang'] = $albums[$key]['language'];
                    }
                    if (empty($albums[$key]['categories'])) {
                
                        $rewriteKey[$key]['categories'] = "";
                    }else {
                
                        $rewriteKey[$key]['categories'] = $albums[$key]['categories'];
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
                 if($dat['api'] == 'album_view'){
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
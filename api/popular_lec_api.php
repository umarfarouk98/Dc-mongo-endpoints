<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
    
    if( (isset($_GET['lim'])) && (isset($_GET['langid'])) ) {
        
        $limit = intval($_GET['lim']);
        $lang = intval($_GET['langid']);
        
        function aggQuery($op1, $op2){
            global $lang, $limit;
            $args = [
                ['$match' => ['lang_id' => $lang] ],
                ['$sort' => ['views' => -1]],
                $op1,
                $op2,
                ['$limit' => $limit],
            ];
            return $args;
        }

        
    }else if (isset($_GET['langid'])) {
        
        $lang = intval($_GET['langid']);

        function aggQuery($op1, $op2){
            global $lang, $limit;
            $args = [
                ['$match' => ['lang_id' => $lang] ],
                ['$sort' => ['views' => -1]],
                $op1,
                $op2,
                ['$limit' => 20],
            ];
            return $args;
        }
    
    }else if((isset($_GET['lim']))) {
        
        $limit = intval($_GET['lim']);
        function aggQuery($op1, $op2){
            global $limit;
            $args = [
                ['$limit' => $limit],
                ['$sort' => ['views' => -1]],
                $op1,
                $op2,
                ['$limit' => $limit],
            ];
            return $args;
        }

    }else {
        function aggQuery($op1, $op2){
            // global $limit;
            $args = [
                ['$limit' => 20],
                ['$sort' => ['views' => -1]],
                $op1,
                $op2,
                ['$limit' => 20],
            ];
            return $args;
        }
        
    }


    function ret_aggreg(){
        global $db_connect, $opt, $filt1, $limit;
        $db_find = $db_connect->tbl_mp3;   
        
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
        $result = $db_find->aggregate(
            aggQuery($ops,$ops2)
        );
        
        return $result;
    }


    //------------------return-final-output------------------------------------------//
    
    function final_resul(){
        
        $result = ret_aggreg();
        
        foreach ($result as $document) {
            unset($document['_id']);
            $albums[] =  $document;
        }
        // echo json_encode($albums[0]);
        
        if (empty($albums)) {
            echo 'null';
        } else {
            $rewriteKey = array();
            $newArr = array();
            foreach ($albums as $key => $value) {
                // echo json_encode($value)." <br/>";
                if (!empty($albums[$key]['mp3_title']) && (!empty($albums[$key]['mp3_url']))) {
                    $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
                    $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
                    $rewriteKey[$key]['img'] = $albums[$key]['img'];
                    // $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
                    $rewriteKey[$key]['nid'] = $albums[$key]['id'];
                    $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
                    $rewriteKey[$key]['duration'] = $albums[$key]['mp3_duration'];
                    $rewriteKey[$key]['description'] = $albums[$key]['mp3_description'];
                    
                    
                    if (isset($albums[$key]['views'])){
                            
                        $albums[$key]['views'] = $albums[$key]['views'];
                        $rewriteKey[$key]['views'] = $albums[$key]['views'];
                    }else{
                        $albums[$key]['views'] = 0;
                        $rewriteKey[$key]['views'] = $albums[$key]['views'];
                    }
                    if (isset($albums[$key]['downloads'])){
                            
                        $albums[$key]['downloads'] = $albums[$key]['downloads'];
                        $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
                    }else{
                        $albums[$key]['downloads'] = 0;
                        $rewriteKey[$key]['downloads'] = $albums[$key]['downloads'];
                    }
                     
                    //------------------------------------------------------------//   
                    if (empty($albums[$key]['joinTab'][0])) {
                        $rewriteKey[$key]['lang'] = "";
                    } else {
                        $rewriteKey[$key]['lang'] = $albums[$key]['joinTab'][0]['name'];
                    }
                    //------------------------------------------------------------//
                    if (empty($albums[$key]['cat_name'])) {
            
                        $rewriteKey[$key]['cats'] = "";
                    } else {
            
                        $rewriteKey[$key]['cats'] = $albums[$key]['cat_name'];
                    }
                    //------------------------------------------------------------//
                    if (empty($albums[$key]['key_name'])) {
            
                        $rewriteKey[$key]['keyword'] = "";
                    } else {
                        
                        $rewriteKey[$key]['keyword'] = $albums[$key]['key_name'];
                    }
                    //------------------------------------------------------------//
                    if (empty($albums[$key]['key_id'])) {
            
                        $rewriteKey[$key]['key_id'] = "";
                    } else {
                        
                        $rewriteKey[$key]['key_id'] = $albums[$key]['key_id'];
                    }
                    //------------------------------------------------------------//
                    if (empty($albums[$key]['joinTab2'][0])) {
            
                       $rewriteKey[$key]['rpname'] = "";
                    } else {
                        
                        $rewriteKey[$key]['rpname'] = $albums[$key]['joinTab2'][0]['name'];
                    }

                }

                
            }
            echo json_encode($rewriteKey);
        }
    }
    final_resul();



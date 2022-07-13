<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
  
    $db_coll1 = $db_connect->tbl_mp3_views;
    $db_coll2 = $db_connect2->tbl_mp3_views;
    
    function trf_db_data(){
        global $db_coll2,$db_coll1;
        
        $results = $db_coll2->find([]);

        $saved1 = [];
        foreach ($results as $index => $result) {

            $document = $db_coll1->updateOne(
                $result,
                [
                    '$set' => $result
                ],
                ['upsert' => true]
            );
        }
        
    }
     trf_db_data();  
     
     echo "Done...";

?>
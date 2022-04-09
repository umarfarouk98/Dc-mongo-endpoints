<?php

header("Content-Type: application/json; charset=UTF-8");

require_once("../db.inc");

if (isset($_GET['lecid'])) {
    $id = intval($_GET['lecid']);
    
    $db_find = $db_connect->tbl_mp3_try->find(['id' => $id]);
    
    foreach ($db_find as $document) {
        unset($document['_id']);
        $albums[] =  $document;
    }
    // echo json_encode($albums);
    $rewriteKey = array();
    $newArr = array();
    foreach ($albums as $key => $value) {
        $rewriteKey[$key]['Title'] = $albums[$key]['mp3_title'];
        $rewriteKey[$key]['audio'] = $albums[$key]['mp3_url'];
        $rewriteKey[$key]['img'] = $albums[$key]['mp3_thumbnail'];
    }
    echo json_encode($rewriteKey);
} else {
    echo 'id needed!';
}

?>
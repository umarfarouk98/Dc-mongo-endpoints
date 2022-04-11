<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://www.dawahbox.com/mongo/api/");

require_once("db_monconfig.php");
if (isset($_GET['page'])) {


    $getpage = intval($_GET['page']);
    // $limit = intval($_GET['lim']);
    // $lang = intval($_GET['langid']);
    $limit = 20;
    $skip = ($getpage - 1) * $limit;


    // $match_try = ['$match' => ['lang_id' => $lang]];
} else {
    $limit = 20;
    $skip = 0;
}

$db_find = $db_connect->tbl_rp->find([], ['$skip' => $skip], ['$limit' => $limit]);
foreach ($db_find as $document) {
    unset($document['_id']);
    $albums[] =  $document;
}
echo json_encode($albums);
<?php

header("Content-Type: application/json; charset=UTF-8");
require_once("../db.inc");

$db_find = $db_connect->tbl_rp->find();
foreach ($db_find as $document) {
    unset($document['_id']);
    $albums[] =  $document;
}
echo json_encode($albums); 

?>
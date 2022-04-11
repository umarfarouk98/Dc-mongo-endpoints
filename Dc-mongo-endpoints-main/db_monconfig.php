<?php

require_once __DIR__ . '../../vendor/autoload.php';

$client = new MongoDB\Client(
	
);

// $db_connect = $client->dawahbox_oct2019;
$db_connect = $client->dboxcluster;

// if ($db_connect->connected) {
// 	echo " Collection Connected successfully <br/>";
// } else {
// 	echo "wrong collection connection ! <br/>";
// }

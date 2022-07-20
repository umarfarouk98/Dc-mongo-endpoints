<?php 
require __DIR__ . '/vendor/autoload.php';
    
    $client = new MongoDB\Client(
        'mongodb+srv://dboxAdmin_mongo:enSrsfkmrphldT1r@dboxcluster.qs6wu.mongodb.net/myFirstDatabase?retryWrites=true&w=majority'
    );

    $db_connect = $client-> dboxcluster;
    $db_connect2 = $client->dawahbox_nov20;
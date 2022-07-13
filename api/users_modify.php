<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../db.inc");
  
    $db_coll1 = $db_connect->tbl_users;
    // $db_coll2 = $db_connect->mainusers;
    $db_coll3 = $db_connect->tbl_users_dbox;
    
    function read_db_data($collName){
        $data = $collName->find([], ['projection' => ['name' => 1, 'email' => 1, 'password' => 1, 'phone' => 1, 'registered_on' => 1, 'user_type' => 1, 'status' => 1 ,'id' => 1 ]]);

        $saved1 = [];
        foreach ($data as $index => $datum) {
            $saved1['id'][$index] = $datum['id'];
            $saved1['name'][$index] = $datum['name'];
            $saved1['email'][$index] = $datum['email'];
            $saved1['phone'][$index] = $datum['phone'];
            $saved1['password'][$index] = $datum['password'];
            $saved1['registered_on'][$index] = $datum['register_on'];
            $saved1['user_type'][$index] = $datum['user_type'];
            $saved1['status'][$index] = $datum['status'];
                    
            }
            return $saved1;   
    }
    
    $newRecordCount = 0;
    $updateRecordCount = 0;
    function push_to_coll($result1){
        
        global $db_connect;
        for ($i = 0; $i < count($result1['name']); $i++) {
            $id = $result1['id'][$i];
            $name = $result1['name'][$i];
            $email = $result1['email'][$i];
            $phone = $result1['phone'][$i];
            $password = $result1['password'][$i];
            $registered_on = $result1['registered_on'][$i];
            $user_type = $result1['user_type'][$i];
            $status = $result1['status'][$i];
            
            $document = $db_connect->tbl_users_demo->updateOne(
                [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    "phone" => $phone,
                ],
                [
                    '$set' => [
                        "name" => $name,
                        'email' => $email,
                        "phone" => $phone,
                        "password" => $password,
                        'registered_on' => $registered_on,
                        "user_type" => $user_type,
                        "auth_id" => "",
                        "albums" => [],
                        'lectures' => [],
                        "favLecturers" => [],
                        "languageId" => "",
                        'recents' => [],
                        "updatedUser" => false,
                         "status" => $status
                    ]
                ],
                ['upsert' => true]
            );
            $newRecordCount += $document->getUpsertedCount();
            $updateRecordCount += $document->getModifiedCount();
        }
        echo "$newRecordCount documents created<br>";
        echo "$updateRecordCount documents updated";
         
    }
    push_to_coll(read_db_data($db_coll3));

?>
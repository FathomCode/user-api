<?php
require_once 'UserApi.php';
 
try {
    $api = new userApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
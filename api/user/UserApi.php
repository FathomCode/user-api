<?php

include_once '../Database.php';
include_once '../Api.php';

$database = new Database();
$db = $database->getConnection();


$request_method = $_SERVER["REQUEST_METHOD"];

 
class UserApi extends Api
{
    public $apiName = 'user';
 
    /**
     * Метод GET
     * @return string
     */
    public function testAction()
    {	
		$database = new Database();
		$db = $database->getConnection();
        
        if($this->requestUri){
            return $this->response($this->requestUri, 200);
        }
        return $this->response('Data not found', 404);
    }
 }

//var_dump($request_method);
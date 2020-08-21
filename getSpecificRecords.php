<?php
require './vendor/autoload.php';
use \Firebase\JWT\JWT;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");


// files needed to connect to database
include_once 'database.php';
include_once './classes/Records.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$records = new Records($db);
if($_SERVER['REQUEST_METHOD'] === "GET"){
    try{
        $secret_key = "owt125";
        $jwt = JWT::decode($_GET['token'], $secret_key,  array('HS512'));
    
        $records->id = $jwt->data->id;
    
    }catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }
    $records->type = isset($_GET['type']) ? $_GET['type'] : die();
    
    $result = $records->readSpecificRecords();
    //get the row count
    $num = $result->rowCount();
    
    if($num > 0){
        $data_info = array();
    
        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            $data_info[] = $data;
    
        }
        http_response_code(200);
        //convert to JSON output
        echo json_encode($data_info);
    
    }else{
        http_response_code(404);
        echo json_encode(array('message' => 'No record(s) found.'));
    }
}
else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
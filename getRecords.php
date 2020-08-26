<?php
require './vendor/autoload.php';
use \Firebase\JWT\JWT;

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// files needed to connect to database
include_once 'database.php';
include_once 'timezone.php';
include_once './classes/Records.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$records = new Records($db);
if($_SERVER['REQUEST_METHOD'] === "GET"){
    try{
        $secret_key = "owt125";
        $jwt = null;

        $headers = apache_request_headers();

        $arr = explode(" ", $headers["Authorization"]);

        $jwt = $arr[1];

        if($jwt){
            $jwt = JWT::decode($jwt, $secret_key,  array('HS512'));
    
            $records->id = $jwt->data->id;
        }
        
    
    }catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }
    
    
    $result = $records->readRecords();
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
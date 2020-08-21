<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require './vendor/autoload.php';
use \Firebase\JWT\JWT;

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// files needed to connect to database
include_once 'database.php';
include_once './classes/Records.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$records = new Records($db);
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $data = json_decode(file_get_contents("php://input"));

    try{
        $secret_key = "owt125";
        $jwt = JWT::decode($data->id, $secret_key,  array('HS512'));
    
        $records->id = $jwt->data->id;
    
    }catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }

    $records->name          = $data->name;
    $records->location      = $data->location;
    $records->dateContacted = date('Y-m-d h:i:s', strtotime($data->dateContacted));
    $records->timeContacted = date('Y-m-d h:i:s', strtotime($data->timeContacted));
    $records->hasFacemask   = $data->hasFacemask;
    $records->hasFaceshield = $data->hasFaceshield;
    $records->duration      = $data->duration;
    $records->contactInfo   = $data->contactInfo;
    $records->address       = $data->address;
    $records->type          = 1;

    print_r($records);
    // make sure data is not empty
    if(
        !empty($records->name) &&
        !empty($records->location) &&
        !empty($records->id) &&
        !empty($records->dateContacted) &&
        !empty($records->timeContacted) &&
        !empty($records->duration) &&
        !empty($records->contactInfo) &&
        !empty($records->address)
    ){
        if($records->createRecord()){

            http_response_code(201);
            //convert to JSON output
            echo json_encode(array('message' => "Contact Recorded Successfully!"));
        }else{
            http_response_code(503);
            echo json_encode(array('message' => "Unable to create contact record."));
        }
    }
    else{
        // set response code - 400 bad request
        http_response_code(400);
            
        // tell the user
        echo json_encode(array("message" => "Unable to create contact record. Data is incomplete."));
    }
}else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
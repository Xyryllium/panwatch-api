<?php
require './vendor/autoload.php';
use \Firebase\JWT\JWT;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
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

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $data = json_decode(file_get_contents("php://input"));
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

    $records->name                  = $data->name;
    $records->location              = $data->location;
    $records->dateContacted         = date('Y-m-d H:i:s', strtotime($data->dateContacted));
    $records->timeContacted         = date('Y-m-d H:i:s', strtotime($data->timeContacted));
    if($data->hasFacemask == true){
        $records->hasFacemask = 1;
    }
    else
        $records->hasFacemask = 0;
    
    if($data->hasFaceshield == true){
        $records->hasFaceshield = 1;
    }
    else
        $records->hasFaceshield = 0;
    if($data->hasSocialDistancing == true){
        $records->hasSocialDistancing = 1;
    }
    else
        $records->hasSocialDistancing = 0;
    
    if($data->hasTemperatureCheck == true){
        $records->hasTemperatureCheck = 1;
    }
    else
        $records->hasTemperatureCheck = 0;
    $records->duration              = $data->duration;
    $records->contactInfo           = $data->contactInfo;
    $records->type                  = 2;

    // make sure data is not empty
    if(
        !empty($records->name) &&
        !empty($records->location) &&
        !empty($records->id) &&
        !empty($records->dateContacted) &&
        !empty($records->timeContacted) &&
        !empty($records->duration) &&
        !empty($records->contactInfo)
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
}
else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
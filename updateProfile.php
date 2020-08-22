<?php
require './vendor/autoload.php';
use \Firebase\JWT\JWT;
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'database.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

if($_SERVER['REQUEST_METHOD'] === "PUT"){
    $data = json_decode(file_get_contents("php://input"));
    try{
        $secret_key = "owt125";
        $jwt = null;

        $headers = apache_request_headers();

        $arr = explode(" ", $headers["Authorization"]);

        $jwt = $arr[1];

        if($jwt){
            $jwt = JWT::decode($jwt, $secret_key,  array('HS512'));
    
            $user->id = $jwt->data->id;
        }
    
    }catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }

    $result = $user->readUserInfo();

    while($record = $result->fetch(PDO::FETCH_ASSOC)){
        extract($record);

        $info = array(
            "id" => $id,
            "name" => $name,
            "address" => $address,
            "email" => $email,
            "avatar" => $avatar,
            "mobileNumber" => $mobileNumber
        );
        
    }

    if($info["name"] != $data->name)
        $user->name = $data->name;
    else
        $user->name = $info["name"];

    if($info["mobileNumber"] != $data->mobileNumber)
        $user->contactInfo = $data->mobileNumber;
    else
        $user->contactInfo = $info["mobileNumber"];

    if($info["address"] != $data->address)
        $user->address = $data->address;
    else
        $user->address = $info["address"];

    if($info["avatar"] != $data->avatar)
        $user->avatar = $data->avatar;
    else
        $user->avatar = $info["avatar"];
    
    //make sure data is not empty
    if(
        !empty($user->id)
    ){
        if($user->updateInfo()){
    
            http_response_code(201);
            //convert to JSON output
            echo json_encode(array('message' => "Information Updated!"));
        }else{
            http_response_code(503);
            echo json_encode(array('message' => "Unable to update information."));
        }
    }
    else{
        // set response code - 400 bad request
        http_response_code(400);
            
        // tell the user
        echo json_encode(array("message" => "Unable to update information."));
    }
}else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
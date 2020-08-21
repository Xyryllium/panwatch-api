<?php
require './vendor/autoload.php';
require 'config-cloudinary.php';
use \Firebase\JWT\JWT;
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'database.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

// $data = json_decode(file_get_contents("php://input"));

if($_SERVER['REQUEST_METHOD'] === "POST"){
    try{
        $secret_key = "owt125";
        $jwt = JWT::decode($_POST['token'], $secret_key,  array('HS512'));
    
        $user->id = $jwt->data->id;
    
    }catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }
    
    $avatar = $_FILES["avatar"]["tmp_name"];
    $default_upload_options = array('tags' => 'avatar');
    
    $cloud = \Cloudinary\Uploader::upload($avatar, $default_upload_options);
    
    $user->avatar = $cloud["secure_url"];
    
    //make sure data is not empty
    if(
        !empty($user->avatar) &&
        !empty($user->id)
    ){
        if($user->updateAvatar()){
    
            http_response_code(201);
            //convert to JSON output
            echo json_encode(array('message' => "Uploaded Avatar!"));
        }else{
            http_response_code(503);
            echo json_encode(array('message' => "Unable to upload avatar."));
        }
    }
    else{
        // set response code - 400 bad request
        http_response_code(400);
            
        // tell the user
        echo json_encode(array("message" => "Unable to upload avatar."));
    }
}else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
<?php

require './vendor/autoload.php';
use \Firebase\JWT\JWT;

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");


// files needed to connect to database
include_once 'database.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $data = json_decode(file_get_contents("php://input"));

    $user->email        = $data->email;
    $user->password     = $data->password;
    
    $result = $user->readUser();
    
    //get the row count
    $num = $result->rowCount();
    
    if($num > 0){
        $data_info = array();
    
        while($data = $result->fetch(PDO::FETCH_ASSOC)){
                extract($data);  
                $info = array(
                    "id" => $id,
                    "name" => $name,
                    "address" => $address,
                    "email" => $email,
                    "avatar" => $avatar,
                    "mobileNumber" => $mobileNumber
                );

                $iss = "localhost";
                $iat = time();
                $nbf = $iat + 10;
                $aud = "myusers";

                $secret_key = "owt125";

                $payload_info = array(
                    "iss" => $iss,
                    "iat" => $iat,
                    "nbf" => $nbf,
                    "aud" => $aud,
                    "data"=> $info
                );

                $jwt = JWT::encode($payload_info, $secret_key, 'HS512');
                
                $data_info [] = array("token" => $jwt, "user" => $info);
                
                http_response_code(200);
                //convert to JSON output
                echo json_encode($data_info);
            
        }
        
    }else{
        http_response_code(400);
        echo json_encode(array('message' => 'Account does not exist'));
    }
}
else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
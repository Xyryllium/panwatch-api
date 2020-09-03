<?php
date_default_timezone_set('Asia/Manila');
require './vendor/autoload.php';
use \Firebase\JWT\JWT;

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");


// files needed to connect to database
include_once 'database.php';
include_once 'timezone.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $data = json_decode(file_get_contents("php://input"));

    $user->email        = $data->email;
    $password     = $data->password;
    
    $result = $user->readUser();
    
    //get the row count
    $num = $result->rowCount();
    
    if($num > 0){
        $data_info = "";
    
        while($data = $result->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($password, $data['password'])){
                
                extract($data);  
                $user = array(
                    "id" => $id,
                    "name" => $name,
                    "address" => $address,
                    "email" => $email,
                    "avatar" => $avatar,
                    "mobileNumber" => $mobileNumber,
                    "hasTemporaryPassword" => $hasTemporaryPassword == 1 ? true : false
                );

                $info = array(
                    "id" => $id
                );

                $iss = "localhost";
                $iat = time();
                $datePlus1 = date('Y-m-d H:i:s', strtotime('+1 day', $iat));
                $exp = strtotime($datePlus1);
                $aud = "myusers";

                $secret_key = "owt125";

                $payload_info = array(
                    "iss" => $iss,
                    "iat" => $iat,
                    "aud" => $aud,
                    "exp" => $exp,
                    "data"=> $info
                );

                $jwt = JWT::encode($payload_info, $secret_key, 'HS512');
                
                $data_info = ["token" => $jwt, "user" => $user];
                
                http_response_code(200);
                //convert to JSON output
                echo json_encode($data_info);
            }else{
                http_response_code(400);
                echo json_encode(array('message' => 'Password does not match!'));
            }
            
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
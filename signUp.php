<?php
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// files needed to connect to database
include_once 'database.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$generatedPassword = $user->randomPassword();  

$user->name         = $data->name;
$user->email        = $data->email;
$user->contactInfo  = $data->mobileNumber;
$user->address      = $data->address;
$user->password     = password_hash($generatedPassword, PASSWORD_BCRYPT);

$result = $user->readUserExist();
// make sure data is not empty
$num = $result->rowCount();

if($num > 0){
    http_response_code(400);
    echo json_encode(array('message' => 'Account Exist!'));
}
else{
    if(
        !empty($user->name) &&
        !empty($user->email) &&
        !empty($user->contactInfo) &&
        !empty($user->address)
    ){
        if($user->createUser()){
            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom("xyaranzaaa@gmail.com", "Xyryl Aranza");
            $email->setSubject("Application Password");
            $email->addTo($data->email, $data->name);
            $email->addContent("text/plain", "This is your generated password " . $generatedPassword . ". Please change after logging in to our system.");
            $sendgrid = new \SendGrid('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');
            try {
                $response = $sendgrid->send($email);
            } catch (Exception $e) {
                echo 'Caught exception: '. $e->getMessage();
            }

            $result = $user->readUser();

            while($data = $result->fetch(PDO::FETCH_ASSOC)){
                extract($data);  
                $user = array(
                    "id" => $id,
                    "name" => $name,
                    "address" => $address,
                    "email" => $email,
                    "avatar" => $avatar,
                    "mobileNumber" => $mobileNumber
                );

                $info = array(
                    "id" => $id
                );

                
            }

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
            http_response_code(503);
            echo json_encode(array('message' => "Unable to create user."));
        }
    }
    else{
        // set response code - 400 bad request
        http_response_code(400);
            
        // tell the user
        echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
    }
}
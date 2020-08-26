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

    $passwordDB = '';
    $oldPW = '';
    $oldPW = $data->oldPassword;
    $user->password = password_hash($data->newPassword, PASSWORD_BCRYPT);

    $result = $user->readUserPassword();


    while($data = $result->fetch(PDO::FETCH_ASSOC)){
        $passwordDB = $data['password'];
    }
    
    //make sure data is not empty
    if(
        !empty($user->id)
    ){
        if(password_verify($oldPW, $passwordDB)){
            $result2 = $user->updatePassword();
        
            http_response_code(200);
            echo json_encode(array('message' => 'Password Successfully Changed!', 'hasTemporaryPassword' => false));
        }
        else{
            http_response_code(200);
            echo json_encode(array('message' => 'Old Password Does Not Match!'));
        }
        
    }
    else{
        // set response code - 400 bad request
        http_response_code(400);
            
        // tell the user
        echo json_encode(array("message" => "Fill all the required fields."));
    }
}else{
    http_response_code(404);
    echo json_encode(array('message' => 'Invalid Request.'));
}
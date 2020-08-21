<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// files needed to connect to database
include_once 'database.php';
include_once './classes/User.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->name         = $data->name;
$user->email        = $data->email;
$user->contactInfo  = $data->mobileNumber;
$user->address      = $data->address;
$user->password     = $user->randomPassword();  

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

            http_response_code(201);
            //convert to JSON output
            echo json_encode(array('message' => "User Created Successfully!"));
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
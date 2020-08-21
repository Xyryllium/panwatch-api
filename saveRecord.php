<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// files needed to connect to database
include_once 'database.php';
include_once 'Records.php';

// get database connection
$database = new Database();
    $db = $database->getConnection();

// instantiate user object
$records = new Records($db);

// $data = json_decode(file_get_contents("php://input"));

$records->name          = $_POST['name'];
$records->location      = $_POST['location'];
$records->id            = $_POST['id'];
$records->dateContacted = $_POST['dateContacted'];
$records->timeContacted = $_POST['timeContacted'];
$records->duration      = $_POST['duration'];
$records->contactInfo   = $_POST['contactInfo'];
$records->address       = $_POST['address'];
$records->hasFacemask   = $_POST['hasFacemask'];
$records->hasFaceshield = $_POST['hasFaceshield'];
$records->type          = $_POST['type'];

if($records->type === 2 || $records->type === 3){
    $records->hasSocialDistancing = $_POST['hasSocialDistancing'];
    $records->hasTemperatureCheck = $_POST['hasTemperatureCheck'];

    if($records->type === 3)
        $records->attendees = $_POST['attendees'];;
}


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
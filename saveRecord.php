<?php
require './vendor/autoload.php';

use \Firebase\JWT\JWT;
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    try {
        $secret_key = "owt125";
        $jwt = null;

        $headers = apache_request_headers();

        $arr = explode(" ", $headers["Authorization"]);

        $jwt = $arr[1];

        if ($jwt) {
            $jwt = JWT::decode($jwt, $secret_key,  array('HS512'));

            $records->id = $jwt->data->id;
        }
    } catch (Exception $ex) {
        http_response_code(500);
        echo json_encode(array('message' => $ex->getMessage()));
    }


    $records->name          = $data->name;
    $records->location      = $data->location;
    $records->dateContacted = date('Y-m-d H:i:s', strtotime($data->dateContacted));
    $records->timeContacted = date('Y-m-d H:i:s', strtotime($data->timeContacted));
    $records->duration      = date('Y-m-d H:i:s', strtotime($data->timeContactedEnded));
    $records->contactInfo   = $data->contactInfo;
    $records->address       = $data->address;
    $records->type          = $data->type;

    if ($data->hasFacemask == true) {
        $records->hasFacemask = 1;
    } else
        $records->hasFacemask = 0;

    if ($data->hasFaceshield == true) {
        $records->hasFaceshield = 1;
    } else
        $records->hasFaceshield = 0;

    if ($records->type === 'establishment' || $records->type === 'event') {
        if ($data->hasSocialDistancing == true) {
            $records->hasSocialDistancing = 1;
        } else
            $records->hasSocialDistancing = 0;

        if ($data->hasTemperatureCheck == true) {
            $records->hasTemperatureCheck = 1;
        }
    }

    if ($records->type === 'event') {
        if (is_array($data->attendees)) {
            $counter = 0;
            foreach ($data->attendees as $row => $value) {
                $records->attendees = $value;
                $counter++;
                // make sure data is not empty
                if (
                    !empty($records->name) &&
                    !empty($records->location) &&
                    !empty($records->id) &&
                    !empty($records->dateContacted) &&
                    !empty($records->timeContacted) &&
                    !empty($records->duration)
                ) {
                    if ($records->createRecord()) {

                        end($data->attendees);
                        if ($row === key($data->attendees)) {

                            $records->limit = $counter;

                            $result = $records->readLastRecord();

                            $data_info = array();

                            while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                                $data_info[] = $data;
                            }

                            http_response_code(201);
                            //convert to JSON output
                            echo json_encode($data_info);
                        }
                    } else {
                        end($data->attendees);
                        if ($row === key($data->attendees)) {
                            http_response_code(503);
                            echo json_encode(array('message' => "Unable to create contact record."));
                        }
                    }
                } else {
                    // set response code - 400 bad request
                    http_response_code(400);

                    // tell the user
                    echo json_encode(array("message" => "Unable to create contact record. Data is incomplete."));
                }
            }
        } else {
            // make sure data is not empty
            if (
                !empty($records->name) &&
                !empty($records->location) &&
                !empty($records->id) &&
                !empty($records->dateContacted) &&
                !empty($records->timeContacted) &&
                !empty($records->duration)
            ) {
                if ($records->createRecord()) {

                    $records->limit = 1;

                    $result = $records->readLastRecord();

                    $data_info = array();

                    while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                        $data_info[] = $data;
                    }

                    http_response_code(201);
                    //convert to JSON output
                    echo json_encode($data_info);
                } else {
                    http_response_code(503);
                    echo json_encode(array('message' => "Unable to create contact record."));
                }
            } else {
                // set response code - 400 bad request
                http_response_code(400);

                // tell the user
                echo json_encode(array("message" => "Unable to create contact record. Data is incomplete."));
            }
        }
    } else {
        // make sure data is not empty
        if (
            !empty($records->name) &&
            !empty($records->location) &&
            !empty($records->id) &&
            !empty($records->dateContacted) &&
            !empty($records->timeContacted) &&
            !empty($records->duration)
        ) {
            if ($records->createRecord()) {

                $records->limit = 1;

                $result = $records->readLastRecord();

                $data_info = array();

                while ($data = $result->fetch(PDO::FETCH_ASSOC)) {
                    $data_info[] = $data;
                }

                http_response_code(201);
                //convert to JSON output
                echo json_encode($data_info);
            } else {
                http_response_code(503);
                echo json_encode(array('message' => "Unable to create contact record.", 'error' => var_dump($records->createRecord())));
            }
        } else {
            // set response code - 400 bad request
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "Unable to create contact record. Data is incomplete."));
        }
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Invalid Request."));
}
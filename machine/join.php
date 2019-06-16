<?php
// required headers
header("Access-Control-Allow-Origin: *");//have to change this to support API key
header("Content-Type: application/json; charset=UTF-8");
header("Machine ID:");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// includes
include_once '../config/database.php';
include_once '../objects/machine.php';

date_default_timezone_set ("America/New_York");

$database = new Database();
$db = $database->getConnection();

$machine = new Machine($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

if( !empty($data->id) && !empty($data->name) && !empty($data->requester) ){
    $machine->id = $data->id;
    $machine->name = $data->name;
    $machine->requester = $data->requester;
    $machine->time_requested = date("Y-m-d H:i:s");
    $machine->isComplete = false;
    $machine->isAccountedFor = false;
    if(!empty($data->groups)){
        $machine->groups = $data->groups;
    }
    else{
        $machine->groups = "default groups";
    }

    $fail = false;
    if($machine->create()){
        // 201 created
        http_response_code(201);
        echo json_encode(array("message"=>"[*_*] Attempting to join machine ".$machine->name." to Active Directory..."));
        $output = shell_exec("python3 ../../joinScript/join.py ".$machine->id." ".$machine->name." ".$machine->requester); //whatever we need for AD join script
        // check if join requested successfully
        if(strpos($output, "error") !== false){
            $fail = true;
        }
    }
    else{
        $fail = true;
    }
    if($fail){
        // service unavailable
        http_response_code(503);
        echo json_encode(array("message"=>"Unable to join. Please review credentials, or check EasyJoinAPI container health. \_[*=*]_/"));
    }
}
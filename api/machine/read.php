<?php
// Header define
header("Access-Control-Allow-Origin: *"); // need to specify this for API key access
header("Content-Type: application/json; charset=UTF-8");

// includes
include_once '../config/database.php';
include_once '../objects/machine.php';

$database = new Database();
$db = $database->getConnection();

$machine = new Machine($db);

$stmt = $machine->read();
$num = $stmt->rowCount();

// check for entries
if($num>0){

    $machine_arr = array();
    $machine_arr["records"] = array();

    // strip and organize entries
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $single_machine = array(
            "id" => $id,
            "name" => $name,
            "requester" => $requester,
            "groups" => $groups,
            "time_requested" => $time_requested,
            "time_completed" => $time_completed,
            "isComplete" => $isComplete,
            "isAccountedFor" => $isAccountedFor
        );
        array_push($machine_arr["records"], $single_machine);
    }

    // 200 ok
    http_response_code(200);

    echo json_encode($machine_arr);
}
else{
    //404 not found
    http_response(404);
    
    echo json_encode( array("message" => "No machines found."));
}
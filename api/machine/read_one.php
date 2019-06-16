<?php
// def Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// includes
include_once '../config/database.php';
include_once '../objects/product.php';

$database = new Database();
$db = $database->getConnection();

$machine = new Machine($db);

$machine->id = isset($_GET['id']) ? $_GET['id'] : die();

$machine->readOne();

// check that this is a real entry
if($machine->name!=null){
  $machine_arr = array(
    "id" => $machine->id,
    "name" => $machine->name,
    "requester" => $machine->requester,
    "groups" => $machine->groups,
    "time_requested" => $machine->time_requested,
    "isComplete" => $machine->isComplete,
    "isAccountedFor"=> $machine->isAccountedFor
  );
  // 200 ok
  http_response_code(200);
  echo json_encode($machine_arr);

}
else{
  // 404 not found
  http_response_code(404);
  echo json_encode(array("message"=>"Unable to find this machine... Please check you machine id, or check EasyJoinAPI container health. \_[*=*]_/"))
}
?>

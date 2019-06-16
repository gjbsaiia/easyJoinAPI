<?php
// def Headers
header("Access-Control-Allow-Origin: *");//have to change this to support API key
header("Content-Type: application/json; charset=UTF-8");
header("Machine ID:");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// includes
include_once '../config/database.php'
include_once '../objects/product.php'

$database = new Database();
$db = $database->getConnection();

$machine = new Machine($db);

$data = json_decode(file_get_contents("php://input"));

// id of machine to be removed from system
$machine->id = $data->id;

$machine->readOne();
// check that this is a real entry
if($machine->name!=null){
  // 200 ok
  http_response_code(200);
  if($machine->isComplete == "true"){
    if($machine->delete()){
      // complete and removed
      echo json_encode(array("message"=>"Your machine was successfully entered into AD, and removed from our system. Please restart your machine."));
    }
    else{
      // complete, not deleted
      echo json_encode(array("message"=>"ERR_DELETE: Your machine was successfully entered into AD, but we had trouble removing you from our records. Please request this service again."));
    }
  }
  else{
    echo json_encode(array("message"=>"ERR_JOIN: Your machine has not completed its join yet. Please check back shortly."));
  }
}
else{
  // 503 service unavailable
  http_response_code(503);
  echo json_encode(array("message"=>"Unable to access our records. Please review credentials, or check EasyJoinAPI container health. \_[*=*]_/"))
}

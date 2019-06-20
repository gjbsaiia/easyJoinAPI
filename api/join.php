<?php
// required headers
header("Access-Control-Allow-Origin: *");//have to change this to support API key
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get posted data
$data = json_decode(file_get_contents("php://input"));

$fail = false;
if( !empty($data->id) && !empty($data->name) && !empty($data->domain) && !empty($data->groups)){
    $output = shell_exec("./joinScript/join.ps1 -ApiKey ".$data->id." -ComputerName ".$data->name." -AdminGroups ".$data->groups." -ADDomain ".$data->domain); //whatever we need for AD join script
    // check if join requested successfully
    if(strpos($output, "ERR") !== false){
        $fail = true;
    }
}
else{
    $fail = true
}
if($fail){
    // service unavailable
    http_response_code(503);
    echo json_encode(array("message"=>"ERR: Unable to join. Please review credentials, or check EasyJoinAPI container health. \_[*=*]_/"));
}
else{
    // 201 created
    http_response_code(201);
    echo json_encode(array("message"=>"[*_*] Hello. Attempting to join machine ".$data->name." to Active Directory..."));
}
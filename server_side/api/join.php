<?php
// required headers
header("Access-Control-Allow-Origin: *");//have to change this to support API key
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$fail = false;

$token = null;
$headers = apache_request_headers();
if(isset($headers['Authorization'])){
    $token = $headers['Authorization'];
    $authorized = shell_exec("python3 ../../backend/decrypt.py --api_key ".$token." 2>&1");
    $trimmed_auth = trim($authorized, " \n\r\t\0");
    if($trimmed_auth == "ur_gud" || $trimmed_auth == "hello_admin"){
        // get posted data
        $json = json_decode(file_get_contents("php://input"));
        $data = $json->{"data"};

        if (!empty($data)){
            if( !empty($data->{"name"}) && !empty($data->{"domain"}) && !empty($data->{"groups"})){
                $output = shell_exec("pwsh ../../backend/join.ps1 ".$data->{"name"}." ".$data->{"domain"}." ".$data->{"groups"}." 2>&1");
                $trimmed = trim($output, " \n\r\t\0");
                // check if join requested successfully
                if($trimmed == "ERR"){
                    $fail = true;
                    echo json_encode(array("message"=>"ERR: Unable to join. Please review credentials, or request configuration. \_[*=*]_/"));
                }
            }
            else{
                $fail = true;
                echo json_encode(array("message"=>"ERR: arguments received, but unable to read tham. DUMP: ".var_export($data, true)));
            }
        }
        else{
            $fail = true;
            echo json_encode(array("message"=>"ERR: Unable to join. Could not interpret parameters. \_[*=*]_/"));
        }

        if($fail){
            http_response_code(503);
        }
        else{
            // 201 created
            http_response_code(201);
            echo json_encode(array("message"=>"[*_*] Hello. Attempting to join machine, ".$data->{"name"}.", to Active Directory..."));
        }
    }
    else{
        http_response_code(503);
        echo json_encode(array("message"=>"ERR: Unauthorized user. These credentials, '".$token."', returned an unexpected value: '".$trimmed_auth."'. Please review documentation and set the appropriate user-credentials, encrypted with this API's key and function. \_[*=*]_/"));
    }
}
else{
    http_response_code(503);
    echo json_encode(array("message"=>"ERR: No Authorization set. Please review documentation and set the appropriate 'Bearer' token, encrypted with this API's key and function. \_[*=*]_/"));
}
       

?>
<?php
// required headers
header("Access-Control-Allow-Origin: *");
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
    if($trimmed_auth == "hello_admin"){
        // get posted data
        $json = json_decode(file_get_contents("php://input"));
        $data = $json->{"data"};


        if (!empty($data)){
            if( !empty($data->{"new_cred"}) && !empty($token)){
                $decryp_out = shell_exec("python3 ../../backend/decrypt.py --api_key ".$data->{"new_cred"}." --checkAuth 0 2>&1");
                $processed = trim($decryp_out, " \n\r\t\0");
                $output = shell_exec("python3 ../../backend/updateManifest.py --new_creds ".$processed." 2>&1");
                $trimmed = trim($output, " \n\r\t\0");
                // check if creds added successfully
                if($trimmed == "ERR"){
                    $fail = true;
                    echo json_encode(array("message"=>"ERR: Unable to add new API credentials. Ensure you are using the master login for this operation. \_[*=*]_/"));
                }
            }
            else{
                $fail = true;
                echo json_encode(array("message"=>"ERR: arguments received, but unable to read tham.\_[*=*]_/ DUMP: ".var_export($data, true)));
            }
        }
        else{
            $fail = true;
            echo json_encode(array("message"=>"ERR: Could not interpret parameters. \_[*=*]_/"));
        }

        if($fail){
            http_response_code(503);
        }
        else{
            // 201 created
            http_response_code(201);
            echo json_encode(array("message"=>"[*_*] Hello. I've added the following key to our authorized user listing, ".$data->{"new_cred"}));
        }
    }
    else{
        http_response_code(503);
        echo json_encode(array("message"=>"ERR: Unauthorized user. You must be admin to use this function. Please review documentation and set the appropriate user-credentials, encrypted with this API's key and function. \_[*=*]_/"));
    }
}
else{
    http_response_code(503);
    echo json_encode(array("message"=>"ERR: No Authorization set. Please review documentation and set the appropriate 'Bearer' token, encrypted with this API's key and function."));
}
       

?>
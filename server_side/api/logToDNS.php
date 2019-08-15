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
    if($trimmed_auth == "hello_admin" || $trimmed_auth == "dns_access_granted"){
        // get posted data
        $json = json_decode(file_get_contents("php://input"));
        $data = $json->{"data"};


        if (!empty($data)){
            if( !empty($data->{"name"}) && !empty($data->{"raw_ip"}) ){
                $output = shell_exec("python3 ../../backend/updateDNS.py --domain_name ".$data->{"name"}." --ip_address ".$data->{"raw_ip"}." 2>&1");
                $trimmed = trim($output, " \n\r\t\0");
                // check if dns updated successfully
                if($trimmed == "ERR"){
                    $fail = true;
                    echo json_encode(array("message"=>"ERR: Unable to add DNS record successfully"));
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
            #echo json_encode(array("message"=>"[*_*] Hello. I've added the machine, ".$data->{"name"}.", to the DNS listing. Please reboot this machine now."));
            echo json_encode(array("message"=>"[*_*] Hello. This feature is not currently set up. You need to configure this for your environment. \_['_']_/ ERR"));
        }
    }
    else{
        http_response_code(503);
        echo json_encode(array("message"=>"ERR: Unauthorized user. You must be admin or have the dns credential. Please review documentation and set the appropriate user-credentials, encrypted with this API's key and function. \_[*=*]_/"));
    }
}
else{
    http_response_code(503);
    echo json_encode(array("message"=>"ERR: No Authorization set. Please review documentation and set the appropriate 'Bearer' token, encrypted with this API's key and function."));
}
       

?>

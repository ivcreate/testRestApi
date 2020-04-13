<?php
//get dirname for include and require
$path_dir = dirname(__FILE__);
include_once($path_dir."/db_cfg.php");
require_once($path_dir."/functions.php");
require_once($path_dir."/api/API.php");
require_once($path_dir."/api/ServicesApi.php");

try {
    $uri_templates = [
        "ServicesApi" => "|^\/users\/[\d]*\/services\/[\d]*\/[a-z]*$|Umsi",
    ];

    $result = parserUri($uri_templates);

    if(!empty($result)) {
        $api = new $result['api_name']($result["param"]);
        echo $api->start();
    }else
        returnError();
}catch (Exception $e){
    returnError();
}

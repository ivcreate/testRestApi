<?php
function returnError(){
    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    header("HTTP/1.1 404 Not Found");
}

function parserUri($uri_templates){
    foreach($uri_templates as $key => $template){
        preg_match($template, $_SERVER['REQUEST_URI'], $matches);
        if(!empty($matches))
            return ["api_name" => $key ,"param" =>$matches[0]];
    }
}
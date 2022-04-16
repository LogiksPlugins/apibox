<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("apibox_list")) {
    
    include_once __DIR__."/curl.php";

    function apibox_info($uriID) {
        if(is_array($uriID)) return $uriID; // Is URI Data
        
        $uriInfo = _db()->_selectQ("apibox_tbl", "*", [
                "id"=>$uriID,
                "blocked"=>"false"
            ])->_GET();
        if($uriInfo) $uriInfo = $uriInfo[0];
        
        if(strlen($uriInfo["end_point"])<=1) return false;
        
        if(substr($uriInfo["end_point"], strlen($uriInfo["end_point"])-1,1) == "/") {
            $uriInfo["uri"] = substr($uriInfo["end_point"], 0, strlen($uriInfo["end_point"])-1);
        } else {
            $uriInfo["uri"] = $uriInfo["end_point"];
        }
        
        $uriInfo["method"] = strtoupper($uriInfo["method"]);
        
        if(!$uriInfo["authorization"] || strlen($uriInfo["authorization"])<=0) $uriInfo["authorization"] = false;
        
        if(!$uriInfo["input_validation"] || strlen($uriInfo["input_validation"])<=0) $uriInfo["input_validation"] = false;
        else $uriInfo["input_validation"] = json_decode($uriInfo["input_validation"], true);
        
        if(!$uriInfo["output_transformation"] || strlen($uriInfo["output_transformation"])<=0) $uriInfo["output_transformation"] = false;
        else $uriInfo["output_transformation"] = json_decode($uriInfo["output_transformation"], true);
        
        if(!$uriInfo["params"] || strlen($uriInfo["params"])<=0) $uriInfo["params"] = [];
        else $uriInfo["params"] = json_decode($uriInfo["params"], true);
        
        if(!$uriInfo["headers"] || strlen($uriInfo["headers"])<=0) $uriInfo["headers"] = [];
        else $uriInfo["headers"] = json_decode($uriInfo["headers"], true);
        
        if(!$uriInfo["body"] || strlen($uriInfo["body"])<=0) $uriInfo["body"] = [];
        else $uriInfo["body"] = json_decode($uriInfo["body"], true);
        
        if(!$uriInfo["authorization"]) $uriInfo["authorization"] = [];
        else $uriInfo["authorization"] = json_decode($uriInfo["authorization"], true);

        if(!$uriInfo["input_validation"]) $uriInfo["input_validation"] = [];
        else $uriInfo["input_validation"] = json_decode($uriInfo["input_validation"], true);

        if(!$uriInfo["output_transformation"]) $uriInfo["output_transformation"] = [];
        else $uriInfo["output_transformation"] = json_decode($uriInfo["output_transformation"], true);

        if(!$uriInfo["params"]) $uriInfo["params"] = [];
        else $uriInfo["params"] = json_decode($uriInfo["params"], true);

        if(!$uriInfo["headers"]) $uriInfo["headers"] = [];
        else $uriInfo["headers"] = json_decode($uriInfo["headers"], true);

        
        return $uriInfo;
    }

    function apibox_uri($uriID, $slug = "/") {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return false;
        
        if(!$slug) $slug = "/";
        
        return $apiInfo['uri'].$slug;
    }
    
    function apibox_fetch($uriID, $slug = "/", $getParams, $postData = [], $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        //Validate Get Params : If Applicable
        //$getParams = 
        
        //Validate Post Params : If Applicable
        //$postData = 
        
        $data = false;
        switch($apiInfo['method']) {
            case "GET":
                $data = apibox_curl_get($uriID, $slug, $getParams, $addonParams);
                break;
            case "POST":
                $data = apibox_curl_post($uriID, $slug, $getParams, $postData, $addonParams);
                break;
            case "PUT":
                $data = apibox_curl_put($uriID, $slug, $getParams, $postData, $addonParams);
                break;
            case "DELETE":
                $data = apibox_curl_delete($uriID, $slug, $getParams, $postData, $addonParams);
                break;
        }
        if(!$data) return ["error"=>"Method not supported"];
        
        //Transform Data : If Applicable
        //$data = 
        
        return $data;
    }
}
?>
<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("apibox_get")) {
    
    function apibox_curl_headers($params = []) {
        $headers = [];
        //General Headers
        
        //Auth Headers
        
        return $headers;
    }
    
    function apibox_curl_get($uriID, $slug, $getParams, $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];
        
        $headers = apibox_headers($apiInfo);
        $response = ["error"=>false, "response"=> false];
        
        
        
        
        return $response;
    }
    
    function apibox_curl_post($uriID, $slug, $getParams, $postData, $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];
        
        $headers = apibox_headers($apiInfo);
        $response = ["error"=>false, "response"=> false];
        
        
        
        
        return $response;
    }
    
    function apibox_curl_put($uriID, $slug, $getParams, $putData, $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];
        
        $headers = apibox_headers($apiInfo);
        $response = ["error"=>false, "response"=> false];
        
        
        
        
        return $response;
    }
    
    function apibox_curl_delete($uriID, $slug, $getParams, $deleteData, $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];
        
        $headers = apibox_headers($apiInfo);
        $response = ["error"=>false, "response"=> false];
        
        
        
        
        return $response;
    }
}
?>
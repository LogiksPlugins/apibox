<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("apibox_get")) {
    
    define("USE_CURL_HEADERS", false);

    function apibox_curl_headers($apiInfo = []) {
        $headers = [];
        //General Headers
        if($apiInfo['headers']) {
            $temp = apibox_process_dataobject($apiInfo['headers']);
            if(is_array($temp)) {
                $temp1 = [];
                foreach($temp as $a=>$b) {
                    $temp1[] = "{$a}: "._replace($b);
                }
                $headers = array_merge($headers, $temp1);
            } else {
                $headers[] = $temp;
            }
        }
        
        //Auth Headers
        if($apiInfo['authorization']) {
            $headers[] = apibox_curl_authorization($apiInfo['authorization']);
        }
        
        return $headers;
    }

    function apibox_curl_authorization($authorizationParams) {
        // printArray($authorizationParams);
        if($authorizationParams) {
            switch ($authorizationParams['type']) {
                case 'Bearer':
                    return "Authorization: Bearer ".implode(":", _replace($authorizationParams['data']));
                    break;
            }
        }
        return "";
    }
    
    function apibox_curl_get($uriID, $getParams, $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];

        $apiInfo = array_merge($apiInfo, $addonParams);
        $slug = $apiInfo['subpath'];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];

        if($getParams) {
            $temp = apibox_process_dataobject($getParams);
            $temp = http_build_query($temp);
            if($temp && strlen($temp)>0) {
                $targetURL .= "?{$temp}";
            }
        }
        $targetURL = str_replace("??", "?", $targetURL);
        
        $headers = apibox_curl_headers($apiInfo);

        $response = false;$status="failure";

        // printArray([$getParams, $apiInfo, $targetURL]);exit();

        $ch = curl_init(); 
        $curlParams = [
                CURLOPT_URL => $targetURL,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => $apiInfo['encoding'],
                CURLOPT_MAXREDIRS => $apiInfo['maxdirs'],
                CURLOPT_TIMEOUT => $apiInfo['timeout'],
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => $apiInfo['user_agent'],
            ];

        if($headers) {
            $curlParams[CURLOPT_HTTPHEADER] = $headers;
        }
        if(USE_CURL_HEADERS) {
            curl_setopt($cURLConnection, CURLOPT_HEADER, true);
        }

        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $response = ["error"=>true, "response"=> $response, "error_msg"=> $error_msg];
        } else {
            $response = ["error"=>false, "response"=> $response];
        }
        
        return $response;
    }
    
    function apibox_curl_post($uriID, $getParams, $postData, $addonParams = [], $useJSON = true) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];

        $apiInfo = array_merge($apiInfo, $addonParams);
        $slug = $apiInfo['subpath'];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];

        if($getParams) {
            $temp = apibox_process_dataobject($getParams);
            $temp = http_build_query($temp);
            if($temp && strlen($temp)>0) {
                $targetURL .= "?{$temp}";
            }
        }
        $targetURL = str_replace("??", "?", $targetURL);
        
        $headers = apibox_curl_headers($apiInfo);

        if(strpos("##".implode("\n", $headers), "Content-Type")<=0) {
            if($useJSON) {
                $headers[] = 'Content-Type: application/json';
            } else {
                $headers[] = 'Content-Type: multipart/form-data';
            }
        }

        $response = false;$status="failure";

        // printArray([$getParams, $apiInfo, $targetURL]);exit();

        $ch = curl_init(); 
        $curlParams = [
                CURLOPT_URL => $targetURL,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POST => 1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => $apiInfo['encoding'],
                CURLOPT_MAXREDIRS => $apiInfo['maxdirs'],
                CURLOPT_TIMEOUT => $apiInfo['timeout'],
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => $apiInfo['user_agent'],
            ];

        if($headers) {
            $curlParams[CURLOPT_HTTPHEADER] = $headers;
        }
        if(USE_CURL_HEADERS) {
            curl_setopt($cURLConnection, CURLOPT_HEADER, true);
        }
        //printArray([$curlParams, $postData]);exit();
        //POST BODY - //http_build_query($params)
        if($useJSON) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $response = ["error"=>true, "response"=> $response, "error_msg"=> $error_msg];
        } else {
            $response = ["error"=>false, "response"=> $response];
        }
        
        return $response;
    }
    
    function apibox_curl_put($uriID, $getParams, $putData, $addonParams = [], $useJSON = true) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];

        $apiInfo = array_merge($apiInfo, $addonParams);
        $slug = $apiInfo['subpath'];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];

        if($getParams) {
            $temp = apibox_process_dataobject($getParams);
            $temp = http_build_query($temp);
            if($temp && strlen($temp)>0) {
                $targetURL .= "?{$temp}";
            }
        }
        $targetURL = str_replace("??", "?", $targetURL);
        
        $headers = apibox_curl_headers($apiInfo);

        if(strpos("##".implode("\n", $headers), "Content-Type")<=0) {
            if($useJSON) {
                $headers[] = 'Content-Type: application/json';
            } else {
                $headers[] = 'Content-Type: multipart/form-data';
            }
        }

        $response = false;$status="failure";

        // printArray([$getParams, $apiInfo, $targetURL]);exit();

        $ch = curl_init(); 
        $curlParams = [
                CURLOPT_URL => $targetURL,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => $apiInfo['encoding'],
                CURLOPT_MAXREDIRS => $apiInfo['maxdirs'],
                CURLOPT_TIMEOUT => $apiInfo['timeout'],
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => $apiInfo['user_agent'],
            ];
        if($headers) {
            $curlParams[CURLOPT_HTTPHEADER] = $headers;
        }
        if(USE_CURL_HEADERS) {
            curl_setopt($cURLConnection, CURLOPT_HEADER, true);
        }

        //POST BODY - //http_build_query($params)
        if($useJSON) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($putData));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $putData);
        }

        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $response = ["error"=>true, "response"=> $response, "error_msg"=> $error_msg];
        } else {
            $response = ["error"=>false, "response"=> $response];
        }
        
        return $response;
    }

    function apibox_curl_patch($uriID, $getParams, $patchData, $addonParams = [], $useJSON = true) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];

        $apiInfo = array_merge($apiInfo, $addonParams);
        $slug = $apiInfo['subpath'];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];

        if($getParams) {
            $temp = apibox_process_dataobject($getParams);
            $temp = http_build_query($temp);
            if($temp && strlen($temp)>0) {
                $targetURL .= "?{$temp}";
            }
        }
        $targetURL = str_replace("??", "?", $targetURL);
        
        $headers = apibox_curl_headers($apiInfo);

        if(strpos("##".implode("\n", $headers), "Content-Type")<=0) {
            if($useJSON) {
                $headers[] = 'Content-Type: application/json';
            } else {
                $headers[] = 'Content-Type: multipart/form-data';
            }
        }

        $response = false;$status="failure";

        // printArray([$getParams, $apiInfo, $targetURL]);exit();

        $ch = curl_init(); 
        $curlParams = [
                CURLOPT_URL => $targetURL,
                CURLOPT_CUSTOMREQUEST => "PATCH",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => $apiInfo['encoding'],
                CURLOPT_MAXREDIRS => $apiInfo['maxdirs'],
                CURLOPT_TIMEOUT => $apiInfo['timeout'],
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => $apiInfo['user_agent'],
            ];
        if($headers) {
            $curlParams[CURLOPT_HTTPHEADER] = $headers;
        }
        if(USE_CURL_HEADERS) {
            curl_setopt($cURLConnection, CURLOPT_HEADER, true);
        }

        //POST BODY - //http_build_query($params)
        if($useJSON) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($patchData));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $patchData);
        }

        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $response = ["error"=>true, "response"=> $response, "error_msg"=> $error_msg];
        } else {
            $response = ["error"=>false, "response"=> $response];
        }
        
        return $response;
    }
    
    function apibox_curl_delete($uriID, $getParams, $deleteData, $addonParams = [], $useJSON = true) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];

        $apiInfo = array_merge($apiInfo, $addonParams);
        $slug = $apiInfo['subpath'];
        
        $targetURL = apibox_uri($apiInfo, $slug);
        if(!$targetURL) return ["error"=>"URI EndPoint format error"];

        if($getParams) {
            $temp = apibox_process_dataobject($getParams);
            $temp = http_build_query($temp);
            if($temp && strlen($temp)>0) {
                $targetURL .= "?{$temp}";
            }
        }
        $targetURL = str_replace("??", "?", $targetURL);
        
        $headers = apibox_curl_headers($apiInfo);

        if(strpos("##".implode("\n", $headers), "Content-Type")<=0) {
            if($useJSON) {
                $headers[] = 'Content-Type: application/json';
            } else {
                $headers[] = 'Content-Type: multipart/form-data';
            }
        }

        $response = false;$status="failure";

        // printArray([$getParams, $apiInfo, $targetURL]);exit();

        $ch = curl_init(); 
        $curlParams = [
                CURLOPT_URL => $targetURL,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => $apiInfo['encoding'],
                CURLOPT_MAXREDIRS => $apiInfo['maxdirs'],
                CURLOPT_TIMEOUT => $apiInfo['timeout'],
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => $apiInfo['user_agent'],
            ];
        if($headers) {
            $curlParams[CURLOPT_HTTPHEADER] = $headers;
        }
        if(USE_CURL_HEADERS) {
            curl_setopt($cURLConnection, CURLOPT_HEADER, true);
        }

        //POST BODY - //http_build_query($params)
        if($useJSON) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deleteData));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $deleteData);
        }

        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $response = ["error"=>true, "response"=> $response, "error_msg"=> $error_msg];
        } else {
            $response = ["error"=>false, "response"=> $response];
        }
        
        return $response;
    }
}
?>
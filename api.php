<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("apibox_list")) {
    
    include_once __DIR__."/curl.php";

    function apibox_info($uriID) {
        if(is_array($uriID)) return $uriID; // Is URI Data

        if(isset($_ENV["APIBOX_INFO_{$uriID}"])) return $_ENV["APIBOX_INFO_{$uriID}"];
        
        if(strlen($uriID)==32) {
            $uriInfo = _db()->_selectQ("apibox_tbl", "*", [
                "md5(id)"=>$uriID,
                "blocked"=>"false"
            ])->_GET();
        } else {
            $uriInfo = _db()->_selectQ("apibox_tbl", "*", [
                "id"=>$uriID,
                "blocked"=>"false"
            ])->_GET();
        }
        
        if($uriInfo) $uriInfo = $uriInfo[0];
        else return false;
        
        if(strlen($uriInfo["end_point"])<=1) return false;
        
        if(substr($uriInfo["end_point"], strlen($uriInfo["end_point"])-1,1) == "/") {
            $uriInfo["uri"] = substr($uriInfo["end_point"], 0, strlen($uriInfo["end_point"])-1);
        } else {
            $uriInfo["uri"] = $uriInfo["end_point"];
        }
        
        $uriInfo["method"] = strtoupper($uriInfo["method"]);
        
        if(!$uriInfo["authorization"] || strlen($uriInfo["authorization"])<=0 || $uriInfo["authorization"]=="none") {
            $uriInfo["authorization"] = false;
        } else {
            $uriInfo["authorization"] = [
                "type"=> $uriInfo["authorization"],
                "data"=> explode(":", $uriInfo["authorization_token"])
            ];
        }
        unset($uriInfo["authorization_token"]);

        if(!$uriInfo["input_validation"] || strlen($uriInfo["input_validation"])<=0) $uriInfo["input_validation"] = false;
        else {
            if(substr(strtoupper($uriInfo["input_validation"]), 0, 8)=='PHPFUNC:') {
                $uriInfo["input_validation"] = [
                    "type"=> "function",
                    "data"=> substr($uriInfo["input_validation"], 8),
                ];
            } else {
                try {
                    $temp = json_decode($uriInfo["input_validation"], true);

                    if(count($temp)>0) {
                        $uriInfo["input_validation"] = [
                            "type"=> "rules",
                            "data"=> $temp
                        ];
                    } else {
                        $uriInfo["input_validation"] = false;
                    }
                } catch(Exception $e) {
                    $uriInfo["input_validation"] = false;
                }
            }
        }

        if(!$uriInfo["params"] || strlen($uriInfo["params"])<=0) $uriInfo["params"] = false;
        else {
            if(substr(strtoupper($uriInfo["params"]), 0, 8)=='PHPFUNC:') {
                $uriInfo["params"] = [
                    "type"=> "function",
                    "data"=> substr($uriInfo["params"], 8),
                ];
            } else {
                try {
                    $temp = json_decode($uriInfo["params"], true);

                    if(count($temp)>0) {
                        $uriInfo["params"] = [
                            "type"=> "rules",
                            "data"=> $temp
                        ];
                    } else {
                        $uriInfo["params"] = false;
                    }
                } catch(Exception $e) {
                    $uriInfo["params"] = false;
                }
            }
        }

        if(!$uriInfo["headers"] || strlen($uriInfo["headers"])<=0) $uriInfo["headers"] = false;
        else {
            if(substr(strtoupper($uriInfo["headers"]), 0, 8)=='PHPFUNC:') {
                $uriInfo["headers"] = [
                    "type"=> "function",
                    "data"=> substr($uriInfo["headers"], 8),
                ];
            } else {
                try {
                    $temp = json_decode($uriInfo["headers"], true);

                    if(count($temp)>0) {
                        $uriInfo["headers"] = [
                            "type"=> "rules",
                            "data"=> $temp
                        ];
                    } else {
                        $uriInfo["headers"] = false;
                    }
                } catch(Exception $e) {
                    $uriInfo["headers"] = false;
                }
            }
        }
        
        if(!$uriInfo["body"] || strlen($uriInfo["body"])<=0) $uriInfo["body"] = false;
        else {
            if(substr(strtoupper($uriInfo["body"]), 0, 8)=='PHPFUNC:') {
                $uriInfo["body"] = [
                    "type"=> "function",
                    "data"=> substr($uriInfo["body"], 8),
                ];
            } else {
                try {
                    $temp = json_decode($uriInfo["body"], true);

                    if(count($temp)>0) {
                        $uriInfo["body"] = [
                            "type"=> "rules",
                            "data"=> $temp
                        ];
                    } else {
                        $uriInfo["body"] = false;
                    }
                } catch(Exception $e) {
                    $uriInfo["body"] = false;
                }
            }
        }
        
        if(!$uriInfo["output_transformation"] || strlen($uriInfo["output_transformation"])<=0) $uriInfo["output_transformation"] = false;
        else {
            if(substr(strtoupper($uriInfo["output_transformation"]), 0, 8)=='PHPFUNC:') {
                $uriInfo["output_transformation"] = [
                    "type"=> "function",
                    "data"=> substr($uriInfo["output_transformation"], 8),
                ];
            } else {
                try {
                    $temp = json_decode($uriInfo["output_transformation"], true);

                    if(count($temp)>0) {
                        $uriInfo["output_transformation"] = [
                            "type"=> "rules",
                            "data"=> $temp
                        ];
                    } else {
                        $uriInfo["output_transformation"] = false;
                    }
                } catch(Exception $e) {
                    $uriInfo["output_transformation"] = false;
                }
            }
        }

        $uriInfo['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
        $uriInfo['encoding'] = "utf8";
        $uriInfo['maxdirs'] = 10;
        $uriInfo['timeout'] = 300;
        
        $_ENV["APIBOX_INFO_{$uriID}"] = $uriInfo;

        return $uriInfo;
    }

    function apibox_uri($uriID, $slug = "/") {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return false;
        
        if(!$slug) $slug = "/";
        
        if(substr($slug, 0, 1)!="/") $slug="/{$slug}";

        return $apiInfo['uri'].$slug;
    }
    
    function apibox_run($uriID, $getParams, $postData = [], $addonParams = []) {
        $apiInfo = apibox_info($uriID);
        if(!$apiInfo) return ["error"=>"URI ID not found"];
        
        //Validate Get Params : If Applicable
        //$getParams = 
        
        //Validate Post Params : If Applicable
        //$postData = 

        if($postData && is_array($postData)) {
            if(isset($postData['data']) && isset($postData['type']) && in_array($postData['type'], ["rules", "phpfunc", "function"])) {
                $postData = apibox_process_dataobject($postData);
            }
        }
        
        $data = false;
        switch($apiInfo['method']) {
            case "GET":
                $data = apibox_curl_get($uriID, $getParams, $addonParams);
                break;
            case "POST":
                $data = apibox_curl_post($uriID, $getParams, $postData, $addonParams);
                break;
            case "PUT":
                $data = apibox_curl_put($uriID, $getParams, $postData, $addonParams);
                break;
            case "DELETE":
                $data = apibox_curl_delete($uriID, $getParams, $postData, $addonParams);
                break;
            case "PATCH":
                $data = apibox_curl_patch($uriID, $getParams, $postData, $addonParams);
                break;
        }
        $dataOriginal = $data;

        //Log API Call
        apibox_log($uriID, $getParams, $postData, $addonParams, $dataOriginal);

        if(!$data) return ["error"=>"Method not supported"];
        
        //Transform Data : If Applicable
        //$data = 

        
        return $data;
    }

    //Array/String Output
    function apibox_process_dataobject($dataObject) {
        switch($dataObject['type']) {
            case "data":
                return $dataObject['data'];
            break;
            case "rules":
                $temp = json_encode($dataObject['data']);
                $temp = _replace($temp);
                return json_decode($temp, true);
            break;
            case "phpfunc":
            case "function":
                return call_user_func($dataObject['data']);
            break;
        }
    }

    function apibox_log($uriID, $getParams, $payload, $addonParams, $responseData) {
        // $dated = date("Y-m-d H:i:s");
        // _db("log")->_insertQ1("apibox_logs",[
        //         "guid"=>$_SESSION['SESS_GUID'],
        //         "groupuid"=>$_SESSION['SESS_GROUP_NAME'],
        //         "reference"=>$callReference,
        //          "status"=>$status,
        //         "service_id"=>$cmdScript['id'],
        //         "output_log"=>$response,
        //         "payload"=>json_encode($payload),
        //         "created_by"=>$_SESSION['SESS_USER_ID'],
        //         "created_on"=>$dated,
        //         "edited_by"=>$_SESSION['SESS_USER_ID'],
        //         "edited_on"=>$dated,
        //     ])->_RUN();
        
        // return $response;
    }
}
?>
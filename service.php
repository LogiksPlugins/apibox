<?php
if(!defined('ROOT')) exit('No direct script access allowed');
//checkServiceAccess();

loadModuleLib("apibox", "api");

handleActionMethodCalls([]);

//Write your service execution code here
function _service_runAPI() {
    if(!isset($_POST['API_ID'])) {
        echo "Error finding API Reference";
        exit();
    }

    if(!isset($_POST['debug_api']) || $_POST['debug_api']!="true") $_POST['debug_api'] = false;
    else $_POST['debug_api'] = true;

    $uriID = $_POST['API_ID'];

    $uriInfo = apibox_info($uriID);
    if(!$uriInfo) {
        echo "Error finding API";
        exit();
    }

    if(isset($_POST['show_mock']) && $_POST['show_mock']=="true") {
        // try {
        //     echo json_encode(json_decode($uriInfo['mockdata'], true));
        // } catch($e) {
        //     echo $uriInfo['mockdata'];
        // }
        echo $uriInfo['mockdata'];
        exit();
    }
    
    
    $uriInfoNew = $uriInfo;
    if(isset($_POST['data'])) {
        foreach($_POST['data'] as $key => $value) {
            $key1 = str_replace('"', '', $key);
            if($uriInfoNew[$key1] && isset($uriInfoNew[$key1]['data'])) {
                $uriInfoNew[str_replace('"', '', $key1)] = [
                    "type"=>"rules",
                    "data"=>array_merge($uriInfoNew[$key1]['data'], json_decode($value, true))
                ];
            } else {
                $uriInfoNew[$key1] = [
                    "type"=>"rules",
                    "data"=>json_decode($value, true)
                ];
            }
        }
    }


    if($_POST['debug_api']) {
        printArray([
            "POST_DATA"=>$_POST,
            "URI_INFO"=>$uriInfo,
            "URI_INFO_NEW"=>$uriInfoNew
        ]);
    }
    //echo apibox_uri($uriID, "/a/b");

    //$uriID, $slug = "/", $getParams, $postData = [], $addonParams = []
    $responseData = apibox_run($uriID, $uriInfoNew['params'], $uriInfoNew['body'], $uriInfoNew);
    //printArray($responseData);

    if(!$responseData['error']) {
        echo $responseData['response'];
    } else {
        echo "ERROR:\n{$responseData['error_msg']}";
    }
}

function _service_cloneAPI() {
    if(!isset($_REQUEST['API_ID'])) {
        echo "Error finding API Reference";
        exit();
    }

    $a = _db()->cloneRow("apibox_tbl", ["id"=>$_REQUEST['API_ID']]);
    if($a) return true;
    else return false;
}
?>
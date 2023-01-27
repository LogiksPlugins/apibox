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

    if(isset($_SESSION['APIBOX_ENIVRONMENT'])) {
        unset($_SESSION['APIBOX_ENIVRONMENT']);
    }

    //echo apibox_uri($uriID, "/a/b");

    //$uriID, $slug = "/", $getParams, $postData = [], $addonParams = []
    $responseData = apibox_run($uriID, $uriInfoNew['params'], $uriInfoNew['body'], $uriInfoNew, $_POST['enviroment']);
    //printArray($responseData);

    if(!$responseData['error']) {
        echo $responseData['response'];
    } else {
        echo "ERROR:\n{$responseData['error_msg']}";
    }

    if($_POST['debug_api']) {
        echo "<hr>";
        printArray([
            "TARGET_URL"=>isset($_SESSION['APIBOX_LAST_URL'])?$_SESSION['APIBOX_LAST_URL']:apibox_uri($uriInfo, $uriInfo['subpath']),
            "POST_DATA"=>$_POST,
            "URI_INFO"=>$uriInfo,
            "URI_INFO_NEW"=>$uriInfoNew,
            "ENIVRONMENT_KEY"=> $_POST['enviroment'],
            "ENIVRONMENT"=>apibox_environment($_POST['enviroment']),
        ]);
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

function _service_envList() {
    $file = (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT)."config/apibox.json";

    $APIBOX_ENVIRONMENT = [];
    if(file_exists($file)) {
        $configJSON = json_decode(file_get_contents($file), true);
        if($configJSON && isset($configJSON['environment'])) {
            $APIBOX_ENVIRONMENT = $configJSON['environment'];
        } else {
            $APIBOX_ENVIRONMENT = [];
        }
    }

    $_SESSION['APIBOX_ENIVRONMENT'] = $APIBOX_ENVIRONMENT;

    return array_keys($_SESSION['APIBOX_ENIVRONMENT']);
}

function _service_envCreate() {
    $validationError = LogiksValidator::validate($_POST, [
        "new_env"=>"required"
    ]);
    if($validationError!==true) {
        return [
            "status"=>"error",
            "msg"=>"Required Params Missing",
            "errors"=>$validationError
        ];
    }
    $_SESSION['APIBOX_ENIVRONMENT'][strtolower(_slugify($_POST['new_env']))] = [];
    
    saveEnvironmentFile();
    return [
        "status"=>"success",
        "items"=>array_keys($_SESSION['APIBOX_ENIVRONMENT'])
    ];
}

function _service_envData() {
    $validationError = LogiksValidator::validate($_POST, [
        "env"=>"required"
    ]);
    if($validationError!==true) {
        return "{}";
    }

    $data = stripslashes(json_encode($_SESSION['APIBOX_ENIVRONMENT'][$_POST['env']], JSON_PRETTY_PRINT));

    if($data=="[]") $data = "{}";

    return $data;
}

function _service_saveData() {
    $validationError = LogiksValidator::validate($_POST, [
        "env"=>"required",
        "data"=>"required",
    ]);
    if($validationError!==true) {
        return "{}";
    }

    $_SESSION['APIBOX_ENIVRONMENT'][$_POST['env']] = json_decode($_POST['data'], true);

    
    saveEnvironmentFile();
    return [
        "status"=>"success",
        "msg"=>"Successfully saved environment data"
    ];
}

function _service_exportAPI() {
    $noInclude= ["id", "guid", "groupuid", "created_on", "created_by", "edited_on", "edited_by", "last_run"];
    $base64Data = ["input_validation", "params", "headers", "body", "output_transformation", "mockdata", "remarks"];

    $dbData = _db()->_selectQ("apibox_tbl", "*")->_GET();

    foreach($dbData as $a=>$row) {
        foreach($noInclude as $k) {
            unset($dbData[$a][$k]);
        }
        foreach($base64Data as $k) {
            if(isset($row[$k])) $dbData[$a][$k] = base64_encode($row[$k]);
        }
    }

    $file = (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT).".install/plugins/apibox.json";

    if(!is_dir(dirname($file))) {
        mkdir(dirname($file), 0777, true);
    }
    $a = file_put_contents($file, stripslashes(json_encode($dbData, JSON_PRETTY_PRINT)));

    if($a)
        return "Successfully exported apis";
    else
        return "Error exporting apis";
}

//Import Directly into DB
function _service_importAPI() {
    $base64Data = ["input_validation", "params", "headers", "body", "output_transformation", "mockdata", "remarks"];

    $fs = [
        (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT).".install/",
        (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT)."plugins/",
        (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT)."pluginsDev/",
    ];
    $files = [];

    foreach($fs as $f) {
        if(is_dir($f)) {
            $files1 = rsearch($f, '/.*\/apibox\.json/');

            $files = array_merge($files, $files1);
        }
    }

    $finalData = [];
    foreach($files as $f) {
        $data = json_decode(file_get_contents($f), true);

        $finalData = array_merge($finalData, $data);
    }

    $data = $finalData;

    $dated = date("Y-m-d H:i:s");
    foreach($data as $a=>$row) {
        $data[$a] = array_merge($row, [
                "guid" => $_SESSION['SESS_GUID'],
                "groupuid" => $_SESSION['SESS_GROUP_NAME'],
                "created_on" => $dated,
                "created_by" => $_SESSION['SESS_USER_ID'],
                "edited_on" => $dated,
                "edited_by" => $_SESSION['SESS_USER_ID'],
            ]);
        foreach($base64Data as $k) {
            if(isset($row[$k])) $data[$a][$k] = base64_decode($row[$k]);
        }
    }

    $a = _db()->_insert_batchQ("apibox_tbl", $data)->_RUN();

    if($a) return "Successfully imported all API across app and plugins";
    else return "Error importing api from apibox file";
}

//Import Directly into DB after truncating
function _service_importAPITruncate() {
    $file = (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT).".install/plugins/apibox.json";
    if(!file_exists($file)) return "Target Import File Not Found";

    _db()->_raw("TRUNCATE apibox_tbl")->_RUN();

    return _service_importAPI();
}
function saveEnvironmentFile() {
    $file = (defined("CMS_APPROOT")?CMS_APPROOT:APPROOT)."config/apibox.json";

    $configJSON = [];
    if(file_exists($file)) {
        $configJSON = json_decode(file_get_contents($file), true);
        if(!$configJSON) $configJSON = [];
    }
    $configJSON['environment'] = $_SESSION['APIBOX_ENIVRONMENT'];

    $a = file_put_contents($file, stripslashes(json_encode($configJSON, JSON_PRETTY_PRINT)));

    return ($a>0);
}

function rsearch($folder, $regPattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $regPattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}
?>
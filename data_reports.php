<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!$reportConfig) return [];

include_once __DIR__."/api.php";

//FETCH REMOTE DATA FROM SERVER USING API

//$report_type= $reportConfig['RPTKEY'];

if(!isset($reportConfig['json_object'])) $reportConfig['json_object'] = false;
$queryData = [
        //"geolocation"=>$reportConfig['PARAMS']['geocordinates'],
        //"filter"=>[],
        //"match"=>[],
    ];

if(isset($reportConfig['source']['findquery'])) {
    $queryData['findQuery']=$reportConfig['source']['findquery'];
}

if(isset($reportConfig['source']['where'])) {
    $queryWhere = $reportConfig['source']['where'];
    foreach($queryWhere as $a=>$b) {
        if(strlen($b)>0) {
            $queryData['filter'][$a] = _replace($b);
        }
    }
}
if(isset($reportConfig['source']['match'])) {
    $queryWhere = $reportConfig['source']['match'];
    
    foreach($queryWhere as $a=>$b) {
        if(strlen($b)>0) {
            $queryData['match'][$a] = _replace($b);
        }
    }
}

if(isset($_POST['cols'])) {
    $queryData['cols'] = $_POST['cols'];
}
    
if(isset($_POST['orderby'])) {
    $queryData['orderby'] = $_POST['orderby'];
}

if(isset($_POST['search']) && isset($_POST['search']['q'])) {
    $sCols = explode(",", $queryData['cols']);
    //printArray($sCols);
    //array_filter($sCols, );
    $a = array_search("updatedAt",$sCols);
    if($a) {
        unset($sCols[$a]);
    }
    $queryData['search'] = [
            "query"=>$_POST['search']['q'],
            "cols"=>implode(",",$sCols),
        ];
        
}

if(isset($_POST['filter'])) {
    foreach($_POST['filter'] as $a=>$b) {
        if(isset($queryData['filter'][$a])) continue;
        $queryData['filter'][$a] = $b;
    }
}

if(isset($_REQUEST['page'])) {
    $queryData['page'] = $_REQUEST['page'];
}

if(isset($_REQUEST['limit'])) {
    $queryData['limit'] = $_REQUEST['limit'];
}
// printArray($reportConfig['datagrid']);
foreach($reportConfig['datagrid'] as $rowKey=>$row) {
    if(isset($row['noshow']) && $row['noshow']) {
        $queryData['cols'].=",{$rowKey}";
    }
}

//Flatten post
foreach($queryData['filter'] as $a=>$b) {
    $queryData[$a] = $b;
}

$apiKey = $reportConfig['source']['apikey'];
$response = apibox_run($apiKey, [], $queryData);
$jsonData = json_decode($response['response'], true);

// printArray($jsonData);exit();

if(isset($reportConfig['DEBUG']) && $reportConfig['DEBUG']) {
    printArray([
            "DATA"=>$jsonData,
            "PARAMS"=> $queryData,
            "URI"=> apibox_info($apiKey)
        ]);//exit();
}

if(!isset($jsonData['data'])) $jsonData['data'] = [];

// foreach($jsonData['data'] as $k=>$row) {
// }

return $jsonData['data'];
?>
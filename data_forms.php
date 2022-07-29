<?php
if (!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

//SUBMIT DATA INTO REMOTE SERVER USING API

if(!isset($source['apikey_create'])) {
    if(isset($source['apikey'])) $source['apikey_create'] = $source['apikey'];
    else displayFormMsg("Sorry, Form Submit - APIKEY Not Found");
}

if(!isset($source['apikey_update'])) {
    if(isset($source['apikey'])) $source['apikey_update'] = $source['apikey'];
    else displayFormMsg("Sorry, Form Submit - APIKEY Not Found");
}

$finalData = array_merge($cols, $where);

if(isset($formConfig['DEBUG']) && $formConfig['DEBUG']) {
    printArray([
            "MODE"=>$formConfig['mode'],
            "COLS"=> $cols, 
            "WHERE"=> $where,
            "DATA_POST"=> $finalData,
        ]);//exit();
}

switch ($formConfig['mode']) {
    case 'new':
    case 'insert':
    case 'create':
        $apiKey = $source['apikey_create'];
        $response = apibox_run($apiKey, [], $finalData);
        $jsonData = json_decode($response['response'], true);
    break;

    case 'edit':
    case 'update':
        $apiKey = $source['apikey_update'];
        $response = apibox_run($apiKey, [], $finalData);
        $jsonData = json_decode($response['response'], true);
    break;

    default:
        displayFormMsg("Form mode could not be detected",'error');
    break;
}

if($jsonData && isset($jsonData['status'])) {
    if($jsonData['status']=="success") {
        return "form submitted successfully";
    } elseif(isset($jsonData['message'])) {
        return $jsonData['message'];
    } elseif(isset($jsonData['msg'])) {
        return $jsonData['msg'];
    } else {
        return "Sorry, Form Submit Failed at Source";
    }
} else {
    return $jsonData;
}
?>
<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
$useDB = $db;
if (getConfig('main_server')) {
    $useDB = $serverDB;
}
$data['resourceData'] = array_filter($data['resourceData'], fn($key) => !str_ends_with($key, '_translated'),ARRAY_FILTER_USE_KEY);
$useDB->insert($data['table'],$data['resourceData']);
?>
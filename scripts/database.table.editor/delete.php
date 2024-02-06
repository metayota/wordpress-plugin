<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
$useDB = $db;
if (getConfig('main_server')) {
    $useDB = $serverDB;
}
$useDB->deleteById($data['table'],$data['row_id']);
?>
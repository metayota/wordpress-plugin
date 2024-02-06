<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   
    $info = ['main_server'=>getConfig('main_server')];
    echo json_encode($info);
?>
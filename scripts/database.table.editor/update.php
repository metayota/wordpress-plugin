<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   
    $useDB = $db;
    if (getConfig('main_server')) {
        $useDB = $serverDB;
    }  
    $data['data'] = array_filter(
        $data['data'],
        fn($key) => !str_ends_with($key, '_translated'),
        ARRAY_FILTER_USE_KEY
    );
    $useDB->update($data['table'],$data['data'],array('id'=>$data['rowid']));
?>
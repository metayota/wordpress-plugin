<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    sendCacheHeaders();
    $languages = ['de','fr','es','ja','ru','zh','en','tr','pt_br'];
    $language=  in_array($GLOBALS['language'],$languages) ? $GLOBALS['language'] : 'en';
    $functions = $db->getAll('php_function_'.$language);
    echo json_encode($functions);

/*
// to get functions:
*/
?>
<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   
    $yotaMetaFunctions = $db->getAll('php_function_en',['version'=>'YOT']);
    $languages = ['de','fr','es','ja','ru','zh'];
    foreach($languages as $language) {
        foreach($yotaMetaFunctions as $yotaMetaFunction) {
            unset($yotaMetaFunction['id']);
            $db->insert('php_function_'.$language,$yotaMetaFunction);
        }
    }

?>
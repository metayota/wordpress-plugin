<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    sendCacheHeaders(86400);

    function translateParams($parameters) {
        if (empty($parameters)) {
            return [];
        }
        foreach($parameters as &$parameter) {
            $parameter['title_translated'] = softTranslate($parameter['title']);
            if (!empty($parameter['documentation'])) {
                $parameter['documentation_translated'] = softTranslate($parameter['documentation']);            
            }
        }
        return $parameters;
    }

    if (!empty($serverDB)) {
	    $tags = $serverDB->getAllByQuery("SELECT name, parameters FROM resource WHERE type='tag'", array());
        $db = $serverDB;
        foreach($tags as &$tag) {
            if (!empty($tag['parameters'])) {
                $tag['parameters'] = translateParams(json_decode($tag['parameters'],true));
            }
        }
    } else {
        $tags = array();    
    }/*else {
        $tags = $serverDB->getAllByQuery("SELECT name, parameters FROM resource WHERE type='tag' AND v", array());
    }*/
	echo json_encode($tags);
?>
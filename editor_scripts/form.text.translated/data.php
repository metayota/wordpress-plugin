<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../deepl/deepl.php');   include_once('../chat-gpt/chat-gpt.php');   
    $info = [];
    $info['main_language'] = getConfig('main_language');
    $info['current_language'] = $GLOBALS['language'];
    $info['auto_translate'] = !empty(getConfig('deepl_api_key'));
    echo json_encode($info);
?>
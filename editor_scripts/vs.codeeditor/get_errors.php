<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    if (!empty($serverDB)) { 
        if (str_ends_with($data['tab'], '.php')) {
            $data['tab'] = str_replace('.php', '', $data['tab']);
        }
        $errors = $serverDB->getAll('error',array('resource'=>$data['resource'],'tab'=>$data['tab']));
        echo json_encode($errors);
    } else {
        echo json_encode([]);
    }
?>
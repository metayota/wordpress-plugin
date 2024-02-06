<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    if ($data['action'] == 'load') {
        $result = $db->get($data['table'],array('id'=>$data['rowid']));
        echo json_encode($result);
    }
    
    if ($data['action'] == 'save') {
        $db->update($data['table'],$data['data'],array('id'=>$data['rowid']));
        msg('msg_data_updated',[],'success');
    }
?>
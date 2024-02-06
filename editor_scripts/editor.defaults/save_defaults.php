<?php namespace Metayota; include_once('../metayota/library.php');  
    $serverDB->update('resource',array('configuration' => $data['defaults']),array('name'=>$data['resourcename']));
    success();
?>
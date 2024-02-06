<?php namespace Metayota; include_once('../metayota/library.php');  
    $serverDB->update('todo', array('status' => 'done'),array('id' => $data['id']));
    success();
?>
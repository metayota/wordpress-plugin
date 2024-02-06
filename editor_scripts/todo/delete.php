<?php namespace Metayota; include_once('../metayota/library.php');  
    $serverDB->query('DELETE FROM todo WHERE id=:id',array('id' => $data['id']));
    success();
?>
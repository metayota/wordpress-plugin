<?php namespace Metayota; include_once('../metayota/library.php');  
    $resource = $serverDB->fetch('resource',array('name'=>$data['resourcename']));
    echo empty($resource['configuration']) ? '{}' : $resource['configuration'];
?>
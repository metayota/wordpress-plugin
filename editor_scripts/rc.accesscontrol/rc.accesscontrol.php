<?php namespace Metayota; include_once('../metayota/library.php');  
    $parameters = array('resource_name'=>$resource->name);
    $query = "SELECT * FROM access WHERE resource_name=:resource_name";
    echo json_encode($db->fetchAllQuery($sql,$parameters));
?>
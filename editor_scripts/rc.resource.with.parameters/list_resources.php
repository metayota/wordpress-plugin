<?php namespace Metayota; include_once('../metayota/library.php');   
    $resources = $db->getAllByQuery("select title as name, name as value, type, project_id from resource order by name");
    echo json_encode($resources);
?>
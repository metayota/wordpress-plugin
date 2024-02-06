<?php namespace Metayota; include_once('../metayota/library.php');   
    $resourceTypes = $db->getAllByQuery('SELECT * FROM resource_type ORDER BY title');
    echo json_encode($resourceTypes);
?>
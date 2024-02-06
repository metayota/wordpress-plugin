<?php namespace Metayota; include_once('../metayota/library.php');  
    $where = isset($data['where']) ? $data['where'] : null;
    echo json_encode($db->fetchAll($data['table'],$where));
?>
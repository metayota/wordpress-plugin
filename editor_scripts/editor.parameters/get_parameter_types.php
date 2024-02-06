<?php namespace Metayota; include_once('../metayota/library.php');   
    $useDB = empty($serverDB) ? $db : $serverDB;
    $parameterTypes = $useDB->getAllByQuery('SELECT * FROM parametertype ORDER BY title');
    echo json_encode($parameterTypes);
?>
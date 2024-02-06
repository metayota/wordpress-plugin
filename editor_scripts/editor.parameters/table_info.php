<?php namespace Metayota; include_once('../metayota/library.php');   namespace Metayota; include_once('../metayota/library.php');   
    try {
        $table = $serverDB->quoteIdentifier($serverDB->prefixTable($data['table']));
        echo json_encode($serverDB->fetchAllQuery("SHOW columns FROM $table"));
    } catch (\PDOException $e) {
        echo json_encode(['table_does_not_exist'=>true]);
    }
?>
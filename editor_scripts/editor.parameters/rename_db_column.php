<?php namespace Metayota; include_once('../metayota/library.php');   
    $oldName = $data['old_name'];
    $newName = $data['new_name'];
    $table = $data['table_name'];
    $serverDB->query("ALTER TABLE $table RENAME COLUMN $oldName TO $newName");
?>
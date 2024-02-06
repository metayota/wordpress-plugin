<?php namespace Metayota; include_once('../metayota/library.php');  
$table = $data['table'];
//if (hasAccess('db.table.get_all',$table)) {
    $data = $serverDB->getAll($table);
    JsonResult($data);
//}
?>
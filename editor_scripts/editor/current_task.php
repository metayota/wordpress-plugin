<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
if (!empty($_SESSION['current_task'])) {
    $task = $db->fetchById('task',$_SESSION['current_task']);
    echo json_encode($task);
} else {
    success();
}
?>
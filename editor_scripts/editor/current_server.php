<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
if (!empty($_SESSION['server'])) {
    echo json_encode($db->fetch('server',array('user_id'=>$user_id,'id'=>$_SESSION['server'])));
} else {
    success();
}
?>
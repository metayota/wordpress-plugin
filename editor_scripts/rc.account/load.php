<?php namespace Metayota; include_once('../metayota/library.php');  
	$loggedInUser = $GLOBALS['loggedInUser'];
	$result = $db->fetchQuery("SELECT * FROM account WHERE user_id=:user_id",array('user_id'=>$loggedInUser['id']));
    if (!$result) {
        echo('{}');
    } else {
	    echo json_encode($result);
    }
?>
<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if ($loggedInUser == null) {
        return;
    }
	$server = $db->fetchQuery("SELECT * FROM server WHERE id=:server AND user_id=:user_id",array('server'=>$data['server'],'user_id'=>$user_id));
	$_SESSION['server'] = $server['id'];
    $expires30days = time() + 3600 * 24  * 30;
	setcookie ('server_id', $server['id'], $expires30days, '/', $_SERVER['HTTP_HOST'], false, true );
	echo json_encode($server);
?>
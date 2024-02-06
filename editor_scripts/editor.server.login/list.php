<?php namespace Metayota; include_once('../metayota/library.php');  
$result = $db->fetchAllQuery("SELECT * FROM server WHERE user_id=:user_id", array('user_id'=>$loggedInUser['id']) );

echo json_encode($result);
?>
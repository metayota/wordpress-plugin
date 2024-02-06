<?php namespace Metayota; include_once('../metayota/library.php');  
$result = $db->fetchAllQuery("SELECT * FROM server WHERE user_id=:user_id", array('user_id'=>$loggedInUser['id']) );
// $result['domains'] = array();
 foreach($result as &$server) {
$server['domains'] = $db->getAll('domain',array('server_id'=>$server['id'],'user_id'=>$user_id));

}
echo json_encode($result);
?>
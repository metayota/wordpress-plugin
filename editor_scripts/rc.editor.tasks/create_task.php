<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
$account = $db->fetch('account',array('user_id'=>$user_id));
if ($account['value'] < $data['max_price']) {
    errorMsg('You do not have enough RC to add this task');
}
$server_id = $_SESSION['server'];

$data = array('owner_user_id'=>$user_id, 'server_id' => $server_id, 'task_state'=>2, 'title'=>$data['title'], 'description'=>$data['description'], 'max_price'=>$data['max_price'], 'resource'=>$data['resource'], 'skill_id'=>$data['skill_id']);
$db->insert('task', $data);
$db->query("UPDATE account SET value = (value - :price) WHERE user_id=:user_id",array('price'=>$data['max_price'],'user_id'=>$user_id));
success();
?>
<?php namespace Metayota; include_once('../metayota/library.php');  
    if (isset($data['name'])) {
        $resourceData = array('name' => $data['name'],'vendor'=>$loggedInUser['username'],'user_id'=>$loggedInUser['id'],'type' => $data['type'],'title' => $data['title'],'project_id' => $data['project_id']);
        $serverDB->query("INSERT INTO resource SET user_id=:user_id, vendor=:vendor, name=:name, type=:type, title=:title, project_id=:project_id", $resourceData);
        success();
	} 
?>
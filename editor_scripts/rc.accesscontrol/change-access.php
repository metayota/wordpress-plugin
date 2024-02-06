<?php namespace Metayota; include_once('../metayota/library.php');  
    if (!empty($serverDB)) {
        if ($data['access']) {
            $parameters = array('access_right'=>$data['access_right'], 'usergroup_id'=>$data['usergroup_id'], 'resource_name' => $data['resource']);
            $serverDB->query("INSERT INTO access SET access_right=:access_right, usergroup_id=:usergroup_id, resource_name=:resource_name", $parameters);
        } else {
            $parameters = array('access_right'=>$data['access_right'], 'usergroup_id'=>$data['usergroup_id'], 'resource_name' =>$data['resource'] );
            $serverDB->query("DELETE FROM access WHERE access_right=:access_right AND usergroup_id=:usergroup_id AND resource_name=:resource_name", $parameters);
        }
    }
    success();
?>
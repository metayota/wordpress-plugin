<?php namespace Metayota; include_once('../metayota/library.php');  

   

    if (!empty($data['version_a'])) {
        if ($data['version_a'] == 'current') {
            $recentResource = $serverDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
        } else {
            $recentResource = $serverDB->fetchQuery("SELECT * FROM resource_archive WHERE name=:name AND version=:version",array('name' => $data['resourcename'],'version'=>$data['version_a']));
        }
    } else {
        $recentResource = $serverDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
    }
    if (!empty($data['version_b'])) {
        if ($data['version_b'] == 'current') {
            $newResource = $serverDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
        } else {
            $newResource = $serverDB->fetchQuery("SELECT * FROM resource_archive WHERE name=:name AND version=:version",array('name' => $data['resourcename'],'version'=>$data['version_b']));
        }
    } else {
        if (!empty($_SESSION['current_task'])) {

            $task_id = $_SESSION['current_task'];
            $task = $db->getById('task', $task_id);

            $workServer = $db->get( 'server', array( 'task_id' => $task_id));
            $mainServer = $db->getById('server', $task['server_id']);

            $mainServerLogin = array('username' => $mainServer['username'], 'password' => $mainServer['password'], 'host' => $mainServer['host'], 'db' => $mainServer['db']);
            $mainServerDB = new PDORepository($mainServerLogin);

            $workServerLogin = array('username' => $workServer['username'], 'password' => $workServer['password'], 'host' => $workServer['host'], 'db' => $workServer['db']);
            $workServerDB = new PDORepository($workServerLogin);

        
            $newResource = $workServerDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
            $recentResource = $mainServerDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
        } else {
            $newResource = $serverDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));
        }
    }

    if (empty($recentResource['implementation'])) {
        $recentResource['implementation'] = '{}';
    }
    if (empty($newResource['implementation'])) {
        $newResource['implementation'] = '{}';
    }
    

    echo json_encode(array('recentResource' => $recentResource, 'newResource' => $newResource));
?>
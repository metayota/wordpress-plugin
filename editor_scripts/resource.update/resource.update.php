<?php namespace Metayota; include_once('../metayota/library.php');  
    if (empty($serverDB)) {
        errorMsg('You are not logged in to a server!', 403);
    }
    

    if (isset($data['id'])) {
        
        $resource = $serverDB->fetchById('resource',$data['id']);

        if ($resource['hash'] != $data['hash']) {
            errorMsg('Could not save the file! It has been edited in another tab, window, or by another user, since you last opened it!');
        }

        $args = array(
            'title' => $data['title'],
            'version' => $data['version'],
            'extends_resource' => $data['extends_resource'],
            'documentation' => $data['documentation'],
            'dependencies' => $data['dependencies'],
            'parameters' => $data['parameters'],
            'license' => $data['license'],
            'implementation' => $data['implementation']
        );
        
        $where = array('id' => $data['id']);
        $serverDB->update('resource',$args,$where);
 
        $serverDB->query("DELETE FROM error WHERE resource=:resource",array('resource'=>$resource['name']));
        
        if ($resource['type'] == 'java-app') {
            callAction('rc.java.build','build',array('resource'=>$resource['name']));
        }
        
      //  echo $server['http_host'].'call/metayota/write_script?resourcename='.$resource['name'];
        if (getConfig('wordpress')) {
            $data = ['resourcename'=>$resource['name']];
            include('../metayota/write_script.php');
        } else {
            $server = $db->getById('server',$_SESSION['server']);
            if ($server['software'] == 'wordpress') {
                file_get_contents($server['http_host'].'wp-content/plugins/metayota/scripts/metayota/write_script.php?resourcename='.$resource['name']);//?==
            } else {
                file_get_contents($server['http_host'].'call/metayota/write_script?resourcename='.$resource['name']);//?==
            }
        }
        
        callFn("rc.resource.updated", array('id'=>$data['id'])); 
        echo json_encode(['new_hash'=>$GLOBALS['new_hash']]);//miau
        die();
    } 

    success();
?>
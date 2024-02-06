<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../rc.resource.get.for.edit/rc.resource.get.for.edit.php');   include_once('../translation-service/translation-service.php');  
    if (!empty($serverDB)) {
        $resource = array(
            'title' => $data['title'], 
            'name' => $data['name'],
            'vendor' => $data['vendor'], 
            'version' => $data['version'], 
            'documentation' => $data['documentation'], 
            'extends_resource' => $data['extends_resource'], 
            'type' => $data['type'], 
            'license' => $data['license'], 
            'project_id' => $data['project_id'],
            'allowed_subelements' => $data['allowed_subelements'],
            'visibility' => $data['visibility'],
            'cachetime' => $data['cachetime']
        );
        $resourceToEdit = $serverDB->getById('resource',$data['id']);
        // TODO: Check if name is available
        $serverDB->update('resource',$resource,array('id'=>$resourceToEdit['id']));
        if ($resourceToEdit['name'] != $resource['name']) {
            $serverDB->update('access',array('resource_name'=>$resource['name']),array('resource_name'=>$resourceToEdit['name']));
        }
    }
    success();
?>
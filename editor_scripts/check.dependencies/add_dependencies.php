<?php namespace Metayota; include_once('../metayota/library.php');   
    $resource = $serverDB->getByName('resource',$data['resource']);
    $dependencies = json_decode($resource['dependencies']);
    foreach($data['dependencies'] as $toAdd) {
        $toAddRow = $serverDB->getByName('resource',$toAdd);
        $dependencies[] = array('name'=>$toAddRow['name'],'type'=>$toAddRow['type'],'version'=>$toAddRow['version']);
    }
    $newDependencies = json_encode($dependencies);
    $serverDB->update('resource',['dependencies'=>$newDependencies],['name'=>$resource['name']]);
?>
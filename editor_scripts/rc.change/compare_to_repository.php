<?php namespace Metayota; include_once('../metayota/library.php');   
    $repository_id = $data['repository'];
    $repository = $serverDB->getById('repository',$repository_id);
    $repositoryURL = $repository['url'];
    $recentResource = $serverDB->fetchQuery("SELECT * FROM resource WHERE name=:name",array('name' => $data['resourcename']));

    $url = "$repositoryURL/call/rc.sync/get_resource?resourcename=".$data['resourcename'];
    $newResource = (array) json_decode(file_get_contents($url));

    if (empty($recentResource['implementation'])) {
        $recentResource['implementation'] = '{}';
    }
    if (empty($newResource['implementation'])) {
        $newResource['implementation'] = '{}';
    }

    echo json_encode(array('recentResource' => $recentResource, 'newResource' => $newResource));
?>
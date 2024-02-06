<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
if ($loggedInUser == null) {
    return;
}
if (isset($data['id'])) {
    
    $resource = $serverDB->getById('resource',$data['id']);
    
    $serverDB->query("DELETE FROM resource WHERE id=:id", array(':id' => $data['id']));
    
    if (getConfig('wordpress')) {
        $data = ['resourcename'=>$resource['name']];
        include('../metayota/write_script.php');
    } else {
        $server = $db->getById('server',$_SESSION['server']);
        file_get_contents($server['http_host'].'call/metayota/write_script?resourcename='.$resource['name']);//?==
    }
    
    
    
    echo 'executed delete query'.$data['id'];
    
}
?>
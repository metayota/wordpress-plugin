<?php
include_once('../metayota/library.php');

$server = $db->getById('server',$_SESSION['server']);

var_dump($_SESSION['server']);
$resources = $serverDB->getAll('resource');

$httpHost = str_replace('https://','http://',$server['http_host']);
foreach($resources as $resource) {
    if ($server['software'] == 'wordpress') {
        echo file_get_contents($httpHost.'wp-content/plugins/metayota/scripts/metayota/write_script.php?resourcename='.$resource['name']);//?==
    } else {
        echo file_get_contents($httpHost.'call/metayota/write_script?resourcename='.$resource['name']);//?==
        
    }
}
?>
<?php namespace Metayota; include_once('../metayota/library.php');   
    
    $dependencies = $serverDB->getByQuery("SELECT dependencies FROM resource WHERE name=:name",array('name'=>$data));
    echo $dependencies;
?>
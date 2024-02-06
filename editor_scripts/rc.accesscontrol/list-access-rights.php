<?php namespace Metayota; include_once('../metayota/library.php');  
    
    $parameters = array('resource_name'=>$data['resource']);
    if (!empty($serverDB)) {    
        echo json_encode($serverDB->getAll('access',$parameters));
    } else {
        echo "[]";
    }
?>
<?php namespace Metayota; include_once('../metayota/library.php');   
if (!empty($serverDB)) {
    $dependencies = isset($data['dependencies']) ? $data['dependencies'] : array();
    for($i = 0 ; $i < count($dependencies) ; $i++) {
        $dependency = $dependencies[$i];
        $res = $serverDB->get('resource',array('name' =>$dependency['name']));
        $dependencies[$i]['title'] = $res['title'];
    }
    echo json_encode($dependencies);
} else {
    errorMsg('Not connected to server');
}
?>
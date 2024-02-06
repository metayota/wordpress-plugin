<?php namespace Metayota; include_once('../metayota/library.php');  
    $where = array();
    if (!empty($data['version'])) {
        $where['version'] = $data['version'];
    }
    if (!empty($data['status'])) {
        $where['status'] = $data['status'];
    }
    if (!empty($data['todoType'])) {
        $where['type'] = $data['todoType'];
    }
    if (!empty($data['priority'])) {
        $where['priority'] = $data['priority'];
    }
    if (empty($data['listall'])) {
        $where['resource'] = $data['resourcename'];
    }
    $result = $serverDB->getAll('todo', $where);
    echo json_encode($result);
?>
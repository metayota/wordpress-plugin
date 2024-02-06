<?php namespace Metayota; include_once('../metayota/library.php');  
	if ($data['action'] == 'save_as_testcase') {
		$title = $data['title'];
		$parameters = $data['parameters'];
        $resource_id = $data['resource_id'];
		$db->insert('testcase',array('title'=>$title,'parameters'=>$parameters,'resource_id'=>$resource_id));
	}
?>
<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
    if ($loggedInUser == null) {
        return;
    }
	$project = $data['project'];
	$serverDB->insert('project',array('title' => $project['title'], 'name' => $project['name'], 'description' => $project['description']));
	success();
?>
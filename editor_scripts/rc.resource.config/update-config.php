<?php namespace Metayota; include_once('../metayota/library.php');  ?>$serverDB->update('resource',array('configuration'=>$data['configuration']),array('name'=>$data['resource']));
success();
<?php namespace Metayota; include_once('../metayota/library.php');  ?>$db->delete('rating',array('resource_name' => $data['resourcename'], 'user_id' => $user_id));
success();
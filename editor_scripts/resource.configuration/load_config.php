<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../rc.resource.get.for.edit/rc.resource.get.for.edit.php');   include_once('../translation-service/translation-service.php');  
    if (!empty($serverDB)) {
        $result = $serverDB->fetchQuery("select * from resource where name=:name",array('name' => $data['name']));
    } else {
        $result = $db->fetchQuery("select * from resource where name=:name and visibility='published'",array('name' => $data['name']));
    }
   
    if (!empty($result['configuration'])) {
      $result['configuration'] = json_decode($result['configuration']);
    }
    if (!empty($result['allowed_subelements'])) {
      $result['allowed_subelements'] = json_decode($result['allowed_subelements']);
    }
 
    echo json_encode($result);
?>
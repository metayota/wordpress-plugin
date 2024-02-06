<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    $skillsJoin = " LEFT OUTER JOIN skills ON ta.skill_id = skills.id LEFT OUTER JOIN translation t ON t.translation_key = skills.skill_name AND t.language=:language";
    $sql = "SELECT ta.*,state.title as status,t.translation as skill_name FROM `task` ta LEFT OUTER JOIN task_state state ON state.id = ta.task_state $skillsJoin WHERE resource=:resource AND server_id=:server_id";
    echo json_encode($db->fetchWithParameters($sql, array('resource'=>$data['resource'],'server_id'=>$_SESSION['server'],'language'=>$GLOBALS['language'])));
?>
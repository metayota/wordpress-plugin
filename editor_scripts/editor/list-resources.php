<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  


$language = $GLOBALS['language'];  // Assuming you've sanitized this value!

if (!empty($serverDB)) {
    if (isset($data['project_id'])) {
        $result = $serverDB->getAllByQuery("
            SELECT 
                r.name, 
                COALESCE(t.translation, r.title) AS title, 
                COALESCE(t2.translation, r.documentation) AS documentation, 
                r.type, 
                r.project_id 
            FROM resource r
            LEFT JOIN translation t ON r.title = t.translation_key AND t.language = :language 
            LEFT JOIN translation t2 ON r.documentation = t2.translation_key AND t2.language = :language 
            WHERE r.project_id = :project_id 
            ORDER BY title
        ", ['project_id' => $data['project_id'], 'language' => $language]);
    } else {
        $result = $serverDB->getAllByQuery("
            SELECT 
                r.name, 
                COALESCE(t.translation, r.title) AS title, 
                COALESCE(t2.translation, r.documentation) AS documentation, 
                r.type, 
                r.project_id 
            FROM resource r
            LEFT JOIN translation t ON r.title = t.translation_key AND t.language = :language 
            LEFT JOIN translation t2 ON r.documentation = t2.translation_key AND t2.language = :language 
            ORDER BY title
        ", ['language' => $language]);
    }
} else {

    $result = $db->getAllByQuery("
        SELECT 
            r.name, 
            COALESCE(t.translation, r.title) AS title, 
            COALESCE(t2.translation, r.documentation) AS documentation, 
            r.type, 
            r.project_id 
        FROM resource r
        LEFT JOIN translation t ON r.title = t.translation_key AND t.language = :language 
        LEFT JOIN translation t2 ON r.documentation = t2.translation_key AND t2.language = :language 
        WHERE r.visibility='published' 
        ORDER BY r.title
    ", ['language' => $language]);


}
    
        echo json_encode($result); 
  
?>
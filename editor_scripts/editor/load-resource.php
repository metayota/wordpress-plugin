<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
function translateParams($parameters) {
    if (empty($parameters)) {
        return [];
    }
    foreach($parameters as &$parameter) {
        $parameter['title_translated'] = softTranslate($parameter['title']);
        $parameter['documentation_translated'] = softTranslate($parameter['documentation']);
    }
    return $parameters;
}

if (!empty($serverDB)) {
    $name = $data['name'];
    if ($loggedInUser == null) {
        $result = $db->getByQuery("SELECT * FROM resource WHERE name=:name AND visibility='public'", array('name'=>$name));
    } else {
        $result = $serverDB->getByQuery("SELECT * FROM resource WHERE name=:name ORDER BY name", array('name'=>$name));
    }
    $db = $serverDB;
} else {
    $name = $data['name'];
    $result = $db->getByQuery("SELECT * FROM resource WHERE name=:name AND visibility='published'", array('name'=>$name));
}


if (!empty($result)) {
    $result['allowed_subelements'] = !empty($result['allowed_subelements']) ? json_decode($result['allowed_subelements']) : null;
    $result['configuration'] = !empty($result['configuration']) ? json_decode($result['configuration']) : null;
    $result['title_translated'] = translate($result['title']);
    $result['documentation_translated'] = formatText(translate($result['documentation']));
    if (!empty($result['parameters'])) {
        $result['parameters'] = json_encode( translateParams(json_decode($result['parameters'],true)));
    }
}

echo json_encode($result);
?>
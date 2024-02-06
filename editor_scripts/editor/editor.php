<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   include_once('../format-text/format-text.php');  
if ($data['action'] == 'list-resources') {
    if (!empty($serverDB)) {
        $res = $serverDB->query("SELECT name, title, documentation, type, project_id FROM resource order by title");
        $result = $res->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    } else {
        echo '[]';
    }
} else if ($data['action'] == 'load-resource') {
    if (isset($serverDB) && !empty($serverDB)) {
        $useDB = $serverDB;
        $name = $data['name'];
        $res = null;
        $res = $useDB->query("SELECT * FROM resource WHERE name=:name", array('name'=>$name));
        $result =$res->fetch(\PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $result['allowed_subelements'] = !empty($result['allowed_subelements']) ? json_decode($result['allowed_subelements']) : null;
            $result['configuration'] = !empty($result['configuration']) ? json_decode($result['configuration']) : null;
        }
        echo json_encode($result);
    } else {
        echo '[]';
    }
} else if ($data['action'] == 'delete_resource') {
    if (isset($data['id'])) {
        $serverDB->query("DELETE FROM resource WHERE id=:id", array(':id' => $data['id']));
    }
}
?>
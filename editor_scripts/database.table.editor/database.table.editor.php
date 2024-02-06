<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    $useDB = $db;
    if (getConfig('main_server')) {
        $useDB = $serverDB;
    }

    $page = empty($data['page']) ? 0 : $data['page'] - 1;
    $table = $data['table'];
    $count = !empty($data['count']);
    $search = !empty($data['search']) ? $data['search'] : null;
    $itemsPerPage = 15;
    $start = $page * $itemsPerPage;
    $selectWhat = $count ? "count(*) OVER() as count" : "$table.*";
    $orderBy = empty($data['sort']) ? "$table.id" : $data['sort'];
    $sortOrder = empty($data['order']) ? 'ASC' : $data['order'];

    $whereStatement = "";
    if ($search != null) {
        $columns = $useDB->fetchAllQuery("SHOW columns FROM $table;");
        $wheres = [];
        foreach($columns as $column) {
            $wheres[] = $table.'.'.$column['Field']." LIKE '%$search%'";
            
        }
    }

    $joins = '';
    if (!empty($data['resourcetype'])) {
        $resource = $useDB->getByName('resource', $data['resourcetype']);
        
        $parameters = json_decode($resource['parameters']);
        $i = 0;
        foreach($parameters as $parameter) {
            $alias = "a$i";
            $i++;
            if ($parameter->type == 'db_row') {
                $dbtable = $parameter->options->dbtable;
                $titlefield = $parameter->options->titlefield;
                $idfield = $parameter->options->idfield;
                $joins.= "LEFT JOIN ".$dbtable." AS $alias ON ".$table.".".$parameter->name." = ".$alias.".".$idfield." ";
                $wheres[] = "$alias.$titlefield LIKE '%$search%'";
            }
            if ($parameter->type == 'translated_string') {
                $joins.= "LEFT JOIN translation AS $alias ON ".$table.".".$parameter->name." = ".$alias.".translation_key AND ".$alias.".language = '".$GLOBALS['language']."' ";
                $wheres[] = "$alias.translation LIKE '%$search%'";
                $selectWhat.=", $alias.translation as ".$parameter->name."_translated";
            }
        }
    }

    if ($search != null) {
        $whereStatement = "WHERE ".join(' OR ', $wheres);
    }
    $sql = "SELECT $selectWhat FROM $table $joins $whereStatement ORDER BY $orderBy $sortOrder LIMIT $start, $itemsPerPage";
    
    if ($count) {
        $result = $useDB->fetchQuery($sql);
        $result = $result['count'];
    } else {
        
        $result = $useDB->fetchAllQuery($sql);
    }
    echo json_encode($result);
?>
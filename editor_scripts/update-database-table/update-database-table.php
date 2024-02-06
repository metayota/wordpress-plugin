<?php namespace Metayota; include_once('../metayota/library.php');   
    
    function getSqlType($param) { // copy-paste
        $typename = $param->type;
        global $parameterTypes;
            
        foreach($parameterTypes as $paramType) {
            if ($typename == $paramType['name']) {
                $sqltype = $paramType['sqltype'];
                if (!empty($param->options)) {
                    foreach ($param->options as $key => $value) {
                        if (is_string($value)) {
                            $sqltype = str_replace("{" . $key . "}", $value, $sqltype);
                        }
                    }
                }
                return $sqltype;
            }
        }
        return 'UNKNOWN';
    }

    function getColumn($columns,$name) {
        foreach($columns as $column) {
            if ($column['Field'] == $name) {
                return $column;
            }
        }
        return NULL;
    }


    $resourcetype = $data['resourcetype'];
    $tablename = $data['resourcetype'];

    $columns = $serverDB->fetchAllQuery("SHOW columns FROM $tablename;");
    $resource = $serverDB->fetchQuery('SELECT * FROM resource WHERE name = :name' , array('name' => $resourcetype) );
    global $parameterTypes;
    $parameterTypes = $serverDB->fetchAllQuery('SELECT * FROM parametertype');
    
    $parameters = json_decode($resource['parameters']);
    
    $fields = array();
    foreach($parameters as $param) {

        $column = getColumn($columns,$param->name);
        if ($column == null) {
            var_dump($param);
            $sql = "ALTER TABLE ".$tablename." ADD COLUMN  ";
            $required = $param->required ? ' NOT NULL' : '';
            $sql.= $param->name." ".getSqlType($param).$required;
            $sql.= ";";
            echo $sql;
            $serverDB->query($sql);
        } else {
            $sqltype = strtolower(getSqlType($param));
            if ($sqltype == 'bool') {
                $sqltype = 'tinyint(1)';
            }
            if ($sqltype == 'integer(32)') {
                $sqltype = 'int';
            }
            
            if ($sqltype != $column['Type']) {
                $columnname = $param->name;
                $type = getSqlType($param);
                $sql = "ALTER TABLE $tablename ";
                $sql.= "MODIFY $columnname $type";  
                echo $sql;
                $serverDB->query($sql);
            }
        }
        // = $param->name = ;
    }

    foreach($columns as $column) {
        $found = false;
        if ($column['Field'] == 'id') {
            $found = true;
        }
        foreach($parameters as $parameter) {
            if ($parameter->name == $column['Field']) {
                $found = true;
            }
        }
        if (!$found) {
            $serverDB->query("ALTER TABLE $tablename DROP COLUMN ".$column['Field']);
        }
    }
    
    success();
?>
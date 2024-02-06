<?php namespace Metayota; include_once('../metayota/library.php');   
   function getSqlType($param) {
        $typename = $param->type;
        global $parameterTypes;
            
        foreach($parameterTypes as $paramType) {
            if ($typename == $paramType['name']) {
                $sqltype = $paramType['sqltype'];
                if (!empty($param->options)) {
                    foreach ($param->options as $key => $value) {
                        $sqltype = str_replace("{" . $key . "}", $value, $sqltype);
                    }
                }
                return $sqltype;
            }
        }
        return 'UNKNOWN';
    }

    function createDatabaseTable($tablename,$resourcetype) {

        global $serverDB;
        $tablename = $serverDB->quoteIdentifier($serverDB->prefixTable($tablename));
        

        $resourceQuery = $serverDB->query('SELECT * FROM resource WHERE name = :name' , array('name' => $resourcetype) );
        $parameterTypesQuery = $serverDB->query('SELECT * FROM parametertype');
        global $parameterTypes;
        $parameterTypes = $parameterTypesQuery->fetchAll(\PDO::FETCH_ASSOC);
        $resource = $resourceQuery->fetch(\PDO::FETCH_ASSOC);
        $parameters = json_decode($resource['parameters']);
        $sql = "CREATE TABLE ".$tablename."( ";
        
        $sql.="`id` int NOT NULL,";
        $fields = array();
        foreach($parameters as $param) {
            $required = $param->required ? ' NOT NULL' : '';
            $fields[] = $param->name." ".getSqlType($param).$required;
            // = $param->name = ;
        }
        $sql.= join(', ',$fields);
        $sql.= ");";
        $serverDB->query($sql);
        $serverDB->query("ALTER TABLE $tablename ADD PRIMARY KEY (`id`);");
        $serverDB->query("ALTER TABLE $tablename MODIFY `id` int NOT NULL AUTO_INCREMENT;");
        
        
        

    }

    createDatabaseTable($data['resourcetype'],$data['resourcetype']);

    success();
?>
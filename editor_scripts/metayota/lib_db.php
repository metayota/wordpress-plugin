<?php

namespace Metayota;
function mapParameters($v) {
    $vd = str_replace('.','_',$v);
    return($v."=:".$vd);
}
function mapSetParameters($v) {
    $vd = str_replace('.','_',$v);
    return($v."= :set_".$vd);
}
class PDORepository {
    
    function __construct($login = NULL) {
        if ($login == NULL) {
            $login = array();
            $login['username'] = $GLOBALS['DB_USERNAME'];
            $login['password'] = $GLOBALS['DB_PASSWORD'];
            $login['host'] = $GLOBALS['DB_HOST'];
            $login['db'] = $GLOBALS['DB_NAME'];
        }
        $this->connection = $this->getConnection($login);
        if (!empty($login['table_prefix'])) {
            $this->table_prefix = $login['table_prefix'];
        } else {
            $this->table_prefix = getConfig('db_table_prefix','');
        }
    }
    
    public function prefixTable($table) {
        return $this->table_prefix.$table;
    }
    
    public function getByName($table,$name) {
        return $this->fetchByName($table,$name);
    }
    
    public function getById($table,$id) {
        return $this->fetchById($table,$id);
    }
    
    public function getByQuery($query,$arguments=array()) {
        return $this->fetchQuery($query,$arguments);
    }
    
    public function getColumnByQuery($query,$arguments) {
        $result = $this->fetchQuery($query,$arguments);
        if (empty($result)) {
            return null;
        } else {
            return reset($result);
        }
    }
    
    public function getColumnByName($table,$column,$name) {
        return $this->getColumn($table,$column,['name'=>$name]);
    }
    
    public function getColumnByID($table,$column,$name) {
        return $this->getColumn($table,$column,['id'=>$name]);
    }
    
    public function get($table,$where=array()) {
        return $this->fetch($table,$where);
    }
    
    public function getAllByQuery($sql,$arguments=array()) {
        return $this->fetchAllQuery($sql,$arguments);
    }
    
    public function getAll($table,$where=array(),$attributes=array()) {
        return $this->fetchAll($table,$where,$attributes);
    }
    public function addPrefixToSQL($sql) {
        $prefix = $this->table_prefix;
        
        $pattern = '/\b(FROM|JOIN|LEFT JOIN|RIGHT JOIN|INNER JOIN|OUTER JOIN|INSERT INTO|INSERT IGNORE INTO|ALTER TABLE)\s+(`?)([a-zA-Z0-9_]+)(`?)\b/i';
        $replacedTables = [];
        $replacedSql = preg_replace_callback($pattern, function ($matches) use ($prefix, &$replacedTables) {
            $keyword = $matches[1];
            $backtickStart = $matches[2];
            $table = $matches[3];
            $backtickEnd = $matches[4];
            
            // Check if the table name already has the prefix
            if (strpos($table, $prefix) === 0) {
                return $matches[0];
            }
            
            $replacedTables[] = $table;
            return $keyword . ' ' . $backtickStart . $prefix . $table . $backtickEnd;
        }, $sql);
        
        $replacedTables = array_unique($replacedTables);
        foreach($replacedTables as $replacedTable) {
            $replacedSql = str_replace(" ".$replacedTable.'.', " ".$prefix . $replacedTable.'.', $replacedSql);
            $replacedSql = str_replace(" `".$replacedTable.'`.', " `".$prefix . $replacedTable."`.", $replacedSql);
        }
        
        return $replacedSql;
    }
    
    private function getConnection($login){
        $username = $login['username'];
        $password = $login['password'];
        $host = $login['host'];
        $db = $login['db'];
        $connection = new \PDO("mysql:dbname=$db;host=$host;charset=utf8mb4", $username, $password);
        return $connection;
    }
    public function fetchAllQuery($sql, $args=[]) {
        if (!empty($this->table_prefix)) {
            $sql = $this->addPrefixToSQL($sql);
        }
        $stmt = $this->queryNoPrefix($sql, $args);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function fetchQuery($sql, $args=[]) {
        if (!empty($this->table_prefix)) {
            $sql = $this->addPrefixToSQL($sql);
        }
        $stmt = $this->queryNoPrefix($sql, $args);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    public function queryNoPrefix($sql, $args=[]){
        try {
            $stmt = $this->getPreparedStatement($sql);
            if (!($result = $stmt->execute($this->getSQLValues($args)))) {
                echo "Error in statement $sql: ";
                var_dump($stmt->errorInfo());
            }
        } catch(\Exception $e) {
            echo "\n\nError in SQL statement:\n$sql\n\n";
            echo "Error: ".$e->getMessage();
        }
        return $stmt;
    }
    public function query($sql, $args=[]){
        if (!empty($this->table_prefix)) {
            $sql = $this->addPrefixToSQL($sql);
        }
        try {
            $stmt = $this->getPreparedStatement($sql);
            if (!($result = $stmt->execute($this->getSQLValues($args)))) {
                echo "Error in statement $sql: ";
                var_dump($stmt->errorInfo());
            }
        } catch(\Exception $e) {
            echo "\n\nError in SQL statement:\n$sql\n\n";
            echo "Error: ".$e->getMessage();
        }
        return $stmt;
    }
    public function getId($table, $name) {
        $table = $this->table_prefix.$table;
        $tableEscaped = $this->quoteIdentifier($table);
        $res = $this->getPreparedStatement("SELECT id FROM $tableEscaped WHERE name=:name");
        $res->execute(array('name'=>$name));
        $result = $res->fetch(\PDO::FETCH_ASSOC)['id'];
        return $result;
    }
    public function fetchWithParameters($query,$parameters) {
        $res = $this->getPreparedStatement($query);
        $res->execute($this->getSQLValues($parameters));
        return $res->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getPreparedStatement($query) {
        $queryHash = md5($query);
        if (!isset($this->stmtCache[$queryHash])) {
            $this->stmtCache[$queryHash] = $this->connection->prepare($query);
        }
        return $this->stmtCache[$queryHash];
    }
    public function fetchOneWithParameters($query,$parameters) {
        $res = $this->getPreparedStatement($query);
        $res->execute($this->getSQLValues($parameters));
        return $res->fetch(\PDO::FETCH_ASSOC);
    }
    public function fetchAll($table,$where=array(),$attributes=array()) {
        $table = $this->table_prefix.$table;
        $whereStatement = '';
        if ($where != null) {
            $whereStatement = $this->getWhereStatement($where);
        }
        $moreStatement = "";
        if (isset($attributes['orderBy'])) {
            
            $moreStatement.= " ORDER BY ".$this->quoteIdentifier($attributes['orderBy']);
        }
        if (isset($attributes['orderByDesc'])) {
            $moreStatement.= " ORDER BY ".$this->quoteIdentifier($attributes['orderByDesc'])." DESC"; // sql injection
        }
        $fields = "*";
        if (isset($attributes['fields'])) {
            $fields = implode(', ', $attributes['fields']);
        }
        $tableEscaped = $this->quoteIdentifier($table);
        $res = $this->getPreparedStatement("SELECT $fields FROM $tableEscaped $whereStatement $moreStatement");
        if (!$res->execute($this->getSQLValues($where))) {
            echo 'db.php:fetchAll() Error:';
            var_dump($res->errorInfo());
        }
        return $res->fetchAll(\PDO::FETCH_ASSOC);
    }
    private function getSetStatement($values) {
        
        $arrKeys = array_keys($values);
        $setStatements = array_map("\Metayota\mapParameters",$arrKeys);
        
        $setStatement = implode(", ",$setStatements);
        return $setStatement;
    }
    public function getWhereStatement($values) {
        if (!is_array($values)) {
            throw new \Exception('Must be countable.');
        }
        if (count($values) == 0) {
            return '';
        }
        $arrKeys = array_keys($values);
        $setStatements = array_map("\Metayota\mapParameters",$arrKeys);
        
        $setStatement = implode(" AND ",$setStatements);
        return 'WHERE '.$setStatement;
    }
    private function getSQLValues($values) {
        if (!$values) {
            return array();
        }
        $sqlValues = array();
        $arrKeys = array_keys($values);
        foreach($arrKeys as $arrKey) {
            $val = $values[$arrKey];
            $arrKey = str_replace('.','_',$arrKey);
            if (is_array($val) || is_object($val)) {
                $sqlValues[$arrKey] = (string) json_encode($val); //':'.$
            } else if (is_bool($val)) {
                $sqlValues[$arrKey] = $val ? 1 : 0; //':'.
            } else if (is_null($val)) {
                $sqlValues[$arrKey] = NULL; // ':'.
            } else {
                $sqlValues[$arrKey] = (string) $val; //':'.
            }
        }
        return $sqlValues;
    }
    // get the table identifier
    public function deleteById($table,$id) {
        $table = $this->table_prefix.$table;
        $table = preg_replace('/[^A-Za-z0-9_]+/', '', $table);
        $res = $this->getPreparedStatement( "DELETE FROM $table WHERE id=:id");
        if (!$res->execute(array(':id'=>$id))) {
            var_dump($res->errorInfo());
        }
        
    }
    public function delete($table,$where) {
        $table = $this->table_prefix.$table;
        $whereStatement = $this->getWhereStatement($where);
        $tableEscaped = $this->quoteIdentifier($table);
        $sql = "DELETE FROM $tableEscaped $whereStatement";
        $stmt = $this->queryNoPrefix($sql,$this->getSQLValues($where));
        return $stmt->rowCount() > 0;
    }
    public function fetchById($table,$id) {
        return $this->fetch($table,array('id'=>$id));
    }
    public function fetchByName($table,$name) {
        return $this->fetch($table,array('name'=>$name));
    }
    public function update($table,$values,$where) {
        $table = $this->table_prefix.$table;
        $arrKeys = array_keys($values);
        $setStatements = array_map("\Metayota\mapSetParameters",$arrKeys);
        $setStatement = implode(", ",$setStatements);
        $prefixedKeys = array_map(function($key) {
            return 'set_' . $key;
        }, array_keys($values));
        
        $values = array_combine($prefixedKeys, $values);
        
        $whereStatement = $this->getWhereStatement($where);
        $sql = "UPDATE $table SET $setStatement $whereStatement";
        
        try {
            $res = $this->getPreparedStatement($sql);
            $sqlValues = array_merge($this->getSQLValues($values),$this->getSQLValues($where));
            if (!$res->execute($sqlValues)) {
                var_dump($sql);
                var_dump($values);
                var_dump($res->errorInfo());
            }
        } catch(\Exception $e) {
            echo "\n\nError in SQL: $sql\n";
            echo $e->getMessage();
        }
    }
    function quoteIdentifier($identifier) {
        if (!preg_match('/^[a-zA-Z0-9$_\.]+$/', $identifier)) {
            throw new \Exception("Invalid identifier: $identifier");
        }
        return "`$identifier`";
    }
    public function getColumn($table,$column,$values) {
        $table = $this->table_prefix.$table;
        $whereStatement = $this->getWhereStatement($values);
        $columnEscaped = $this->quoteIdentifier($column);
        $tableEscaped = $this->quoteIdentifier($table);
        $res = $this->getPreparedStatement("SELECT $columnEscaped FROM $tableEscaped $whereStatement");
        $res->execute($this->getSQLValues($values));
        $result = $res->fetch(\PDO::FETCH_ASSOC);
        if (!empty($result)) {
            return $result[$column];
        }
        return null;
    }
    public function fetch($table,$values) {
        $whereStatement = $this->getWhereStatement($values);
        $table = $this->table_prefix.$table;
        $tableEscaped = $this->quoteIdentifier($table);
        $res = $this->getPreparedStatement("SELECT * FROM $tableEscaped $whereStatement");
        $res->execute($values);
        return $res->fetch(\PDO::FETCH_ASSOC);
    }
    public function insertIgnore($table,$values) {
        $setStatement = $this->getSetStatement($values);
        $table = $this->table_prefix.$table;
        $tableEscaped = $this->quoteIdentifier($table);
        $sql = "INSERT IGNORE INTO $tableEscaped SET $setStatement";
        $res = $this->getPreparedStatement($sql);
        $result = $res->execute($this->getSQLValues($values));
        if (!$result) {
            echo $sql;
            var_dump($res->errorInfo());
        }
    }
    public function insert($table,$values) {
        $table = $this->table_prefix.$table;
        $setStatement = $this->getSetStatement($values);
        $tableEscaped = $this->quoteIdentifier($table);
        $sql = "INSERT INTO $tableEscaped SET $setStatement";
        $res = $this->getPreparedStatement($sql);
        $result = $res->execute($this->getSQLValues($values));
        if (!$result) {
            echo $sql;
            var_dump($res->errorInfo());
        }
    }
}
?>
<?php
class MongoDao {
	private $mongo = null;
	private $dbName = '';
    public function __construct($daoCfg) {
        $mongo = new MongoClient($daoCfg['DSN']);
        if ($daoCfg['USER']) {
        	$mongo->authenticate($daoCfg['USER'], $daoCfg['PASS']);
        }
        $this->selectDb($daoCfg['DBNAME']);
        $this->mongo = $mongo;
    }
    
    public function selectDb($dbName) {
    	$this->dbName = $dbName;
    }

    public function condition($condition) {
    	if (is_array($condition)) {
    		if($condition['_id']) {
    			$condition['_id'] = new MongoId($condition['_id']);
    		}
    	}
        return $condition;
    }
    
    private function table($tableName) {
    	return $this->mongo->selectCollection($this->dbName, $tableName);
    }
    
    public function count($tableName, $condition) {
    	$table = $this->table($tableName);
    	$condition = $this->condition($condition);
    	return $table->count($condition);
    }

    public function getRow($tableName, $condition, $sort=array()) {
        return $this->getLimit($tableName, $condition, $sort, 1, 1);
    }
    
    private function recordToArray($record) {
    	$record['_id'] = (string)$record['_id'];
    	return $record;
    }
    
    private function cursor($tableName, $condition, $sort=array()) {
    	$table = $this->table($tableName);
        $condition = $this->condition($condition);
        $cursor = $table->find($condition);
        if($sort) {
        	$cursor->sort($sort);
        }
        return $cursor;
    }
    
    public function getAll($tableName, $condition, $sort=array()) {
    	$cursor = $this->cursor($tableName, $condition, $sort);
    	$dataArray = array();
    	foreach ($cursor as $row) {
    		$dataArray[] = $this->recordToArray($row);
    	}
    	unset($cursor);
    	return $dataArray;
    }
    
    public function getLimit($tableName, $condition, $sort, $currentPage, $pageSize) {
    	$cursor = $this->cursor($tableName, $condition, $sort);
    	$cursor->skip(($currentPage - 1) * $pageSize)->limit($pageSize);
    	$dataArray = array();
    	foreach ($cursor as $row) {
    		$dataArray[] = $this->recordToArray($row);
    	}
    	unset($cursor);
    	return $dataArray;
    }

    public function save($tableName, $data, $condition=null) {
    	$table = $this->table($tableName);
        if($condition) {
        	$condition = $this->condition($condition);
        	$result = $table->update($condition, $data);
        } else {
        	$result = $table->insert($data);
        }
        if ($result['err']) {
        	return false;
        }
        return true;
    }
    
    public function delete($tableName, $condition) {
    	$table = $this->table($tableName);
    	$condition = $this->condition($condition);
    	$table->remove($condition);
    }
};
?>
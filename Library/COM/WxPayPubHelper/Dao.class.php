<?php
class NCache {
    private static $cachePools = array();
    private $instance = null;

    /**
     *
     * @param Array $cacheConfig
     * @return NCache
     * @throws Exception
     */
    public static  function getCache($cacheConfig=array()) {
        if (false == $cacheConfig) {
            $cacheConfig = C('CACHE_CONFIG');
        }
        $confKey = md5(serialize($cacheConfig));
        if (false == self::$cachePools[$confKey]) {
            $nCache = new NCache();
            try{
                $cache = Cache::getInstance($cacheConfig['cacheType'], $cacheConfig);
                $nCache->instance = $cache;
                self::$cachePools[$confKey] = $nCache;
            } catch(Exception $e){
                throw new Exception('缓存设置错误，无法获取缓存实例');
            }
        }
        return self::$cachePools[$confKey];
    }

    public function set($prefix, $name, $value) {
        $key = $this->getKey($prefix, $name);
        $value = array('time'=>time(), 'value'=>$value);
        $this->instance->set($key, $value);
    }

    public function get($prefix, $name) {
        $key = $this->getKey($prefix, $name);
        $value = $this->instance->get($key);
        if ($value && $value = $this->getValue($prefix, $value)) {
            return $value;
        }
        return null;
    }

    public function delete($prefix, $name=''){
        if ($name) {
            $key = $this->getKey($prefix, $name);
            $this->instance->rm($key);
        } else {
        	$prefix = $this->getPrefix($prefix);
            $nsKey = $this->getNsKey($prefix);
            $this->instance->set($nsKey, time());
        }
    }

    private function getValue($prefix, $value) {
        static $prefixTimes = array();
        $prefix = $this->getPrefix($prefix);
        if (false == $value['time']) {
            return $value;
        }

        if (false == $prefixTimes[$prefix]) {
            $nsKey = $this->getNsKey($prefix);
            $prefixTimes[$prefix] = $this->instance->get($nsKey);
        }
        $cacheTime = $value['time'];
        if ($cacheTime <= $prefixTimes[$prefix]) {
            return null;
        }
        return $value['value'];
    }

    private function getNsKey($prefix) {
        return strtoupper('NS_' . $prefix);
    }

    private function getPrefix($prefix) {
        return strtoupper(APP_NAME . '_' . $prefix);
    }

    private function getKey($prefix, $name) {
    	#return $this->getPrefix($prefix) . '_' . $name;
        return md5($this->getPrefix($prefix) . "\t" . strtoupper($name));
    }

};


class Dao {
	private static $daos = array ();
	public $pdo = null;
	private $dbPrefix = '';
	private $daoType = '';
	private $convertEncoding = false;
	private $affectRows = 0;
	private $isTranse = false;
	private static $tableColumns = array ();
	
	/**
	 *
	 * @param string $daoName        	
	 * @return Dao
	 */
	public static function getDao($daoName = '') {
		if (false == $daoName) {
			$daoName = C ( 'DEFAULT_CONN' );
		}
		$daoName = strtoupper ( $daoName );
		if (false == isset ( self::$daos [$daoName] )) {
			$connCfgs = C ( 'DB_CONN' );
			$daoCfg = $connCfgs [$daoName];
			$daoCfg ['NAME'] = $daoName;
			$daoType = strtoupper ( $daoCfg ['TYPE'] );
			if ($daoType != 'MONGODB') {
				self::$daos [$daoName] = new Dao ( $daoCfg );
			} else {
				import ( 'COM.Dao.MongoDao' );
				self::$daos [$daoName] = new MongoDao ( $daoCfg );
			}
		}
		return self::$daos [$daoName];
	}
	private function __construct($connCfg) {
		try {
			$this->pdo = new PDO ( $connCfg ['DSN'], $connCfg ['USER'], $connCfg ['PASSWORD'] );
			$this->pdo->setAttribute ( PDO::ATTR_CASE, PDO::CASE_LOWER );
		} catch ( Exception $e ) {
			die ( 'Database Connect failed “' . $connCfg ['NAME'] . '”' . $e->getMessage () );
		}
		$this->dbPrefix = $connCfg ['PREFIX'];
		$this->daoType = strtoupper ( $connCfg ['TYPE'] );
		if ('MYSQL' == $this->daoType && $connCfg ['CHARSET']) {
			$this->pdo->exec ( 'SET NAMES ' . $connCfg ['CHARSET'] );
		} else if ('MSSQL' == $this->daoType && $connCfg ['convertEncoding']) {
			$this->convertEncoding = true;
		}
	}
	public function quote($str) {
		return $this->pdo->quote ( $str );
	}
	public function tableName($tableName) {
		return $this->dbPrefix . $tableName;
	}
	public function getQuery($strQuery) {
		if ($this->daoType == 'MSSQL' && $this->convertEncoding) {
			$strQuery = $this->toGbk ( $strQuery );
		}
		return $strQuery;
	}
	public function execute($strQuery) {
		$strQuery = $this->getQuery ( $strQuery );
		$this->affectRows = 0;
		$stmt = $this->getStatement ( $strQuery, true );
		if ($stmt) {
			$this->affectRows = $stmt->rowCount ();
		}
		return $stmt;
	}
	public function begin() {
		/* $this->pdo->beginTransaction(); */
		$this->execute ( 'begin transaction' );
		$this->isTranse = true;
	}
	public function commit() {
		/* $this->pdo->commit(); */
		if ($this->isTranse) {
			$this->execute ( 'commit transaction' );
			$this->isTranse = false;
		}
	}
	public function rollback() {
		/* $this->pdo->rollback(); */
		if ($this->isTranse) {
			$this->execute ( 'rollback transaction' );
			$this->isTranse = false;
		}
	}
	public function affectRows() {
		return $this->affectRows;
	}
	public function lastInsertId($seq = '') {
		if ($this->daoType == 'MSSQL') {
			$strQuery = 'SELECT SCOPE_IDENTITY()';
			return $this->getOne ( $strQuery );
		} else {
			return $this->pdo->lastInsertId ();
		}
	}
	private function getStatement($strQuery, $return = false) {
		$strQuery = $this->getQuery ( $strQuery );
		$stmt = $this->pdo->prepare ( $strQuery );
		$ifSuccess = false;
		if ($stmt)
			$ifSuccess = $stmt->execute ();
		if (false === $ifSuccess || '00000' !== $stmt->errorCode ()) {
			$errorInfo = $stmt->errorInfo ();
			if (APP_DEBUG && false == $this->isTranse) {
				die ( 'SQL Execute Error:<br/><b>' . $strQuery . '</b><br /><br /><pre>' . var_export ( $errorInfo, true ) . '</pre>' );
			} else {
				$this->rollback ();
				if ($return) {
					return false;
				}
			}
		}
		return $stmt;
	}
	public function getAll($strQuery) {
		$stmt = $this->getStatement ( $strQuery );
		$rows = $stmt->fetchAll ( PDO::FETCH_ASSOC );
		if ($this->daoType == 'MSSQL' && $this->convertEncoding) {
			$rows = $this->toUtf8 ( $rows );
		}
		return $rows;
	}
	public function getRow($strQuery) {
		$stmt = $this->getStatement ( $strQuery );
		$row = $stmt->fetch ( PDO::FETCH_ASSOC );
		if ($this->daoType == 'MSSQL' && $this->convertEncoding) {
			$row = $this->toUtf8 ( $row );
		}
		return $row;
	}
	public function getOne($strQuery) {
		$row = $this->getRow ( $strQuery );
		if (false == is_array ( $row )) {
			$row = array ();
		}
		$row = array_values ( $row );
		return $row [0];
	}
	public function getLimit($strQuery, $currentPage, $pageSize, $order = '') {
		$offset = ($currentPage - 1) * $pageSize;
		$order = trim ( $order );
		if ($order) {
			$strQuery = str_ireplace ( $order, ' ', $strQuery );
		}
		if ($this->daoType == 'MSSQL') {
			if (false == $order) {
				DIE ( 'MSSQL server 排序需传入$ORDER参数' );
			}
			$strQuery = 'SELECT * FROM (
                SELECT ROW_NUMBER() OVER(' . $order . ') _rownum,* FROM (
                    ' . $strQuery . '
                ) tbl
            ) tbl WHERE _rownum >' . $offset . ' AND _rownum <=' . ($offset + $pageSize);
		} else {
			$strQuery .= $order . ' limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize . ' ';
		}
		
		return $this->getAll ( $strQuery );
	}
	private function toUtf8($var) {
		if (is_array ( $var )) {
			foreach ( $var as $k => $v ) {
				$var [$k] = $this->toUtf8 ( $v );
			}
		} else {
			$var = mb_convert_encoding ( $var, 'UTF-8', 'CP936' );
		}
		return $var;
	}
	private function toGbk($var) {
		if (is_array ( $var )) {
			foreach ( $var as $k => $v ) {
				$var [$k] = $this->toGbk ( $v );
			}
		} else {
			$var = mb_convert_encoding ( $var, 'CP936', 'UTF-8' );
		}
		return $var;
	}
	
	/**
	 * 条件构造方法，初期仅接受字符串
	 * 
	 * @param type $condition        	
	 * @return type
	 */
	public function condition($condition) {
		return $condition;
	}
	
	/**
	 * 保存数据到数据表，当有条件时进行更新操作
	 * 
	 * @param string $tableName        	
	 * @param Array $data        	
	 * @param String $condition        	
	 * @return Blooean
	 * @throws Exception
	 */
	public function save($tableName, $data, $condition = null) {
		if ($this->dbPrefix && substr ( $tableName, 0, strlen ( $this->dbPrefix ) ) != $this->dbPrefix) {
			$tableName = $this->dbPrefix . $tableName;
		}
		$columns = $this->getColumns ( $tableName );
		if (null !== $condition) {
			if ($columns ['update_at']) {
				$data ['update_at'] = date ( 'Y-m-d H:i:s' );
			}
			$strQuery = 'UPDATE ' . $tableName . ' SET ';
			$queryParts = array ();
			foreach ( $data as $column => $value ) {
				if (isset ( $columns [$column] )) {
					if ($column ['type'] == 'number') {
						$queryParts [] = $column . '=' . floatval ( $value );
					} else {
						$queryParts [] = $column . '=' . $this->quote ( $value );
					}
				}
			}
			if ($queryParts) {
				$strQuery .= implode ( ',', $queryParts ) . ' WHERE ' . $this->condition ( $condition );
			} else {
				throw new Exception ( '数据中不含相关字段信息' );
			}
		} else {
			if ($columns ['create_at']) {
				$time = date ( 'Y-m-d H:i:s' );
				$data ['create_at'] = $time;
				$data ['update_at'] = $time;
			}
			$strQuery = 'INSERT INTO ' . $tableName;
			$arrColumn = $arrColumuVal = array ();
			foreach ( $data as $key => $value ) {
				if (isset ( $columns [$key] )) {
					if ($columns [$key] ['type'] == 'number') {
						$arrColumn [] = $key;
						$arrColumuVal [] = floatval ( $value );
					} else {
						$arrColumn [] = $key;
						$arrColumuVal [] = $this->quote ( $value );
					}
				}
			}
			if ($arrColumn) {
				$strQuery .= ' (' . implode ( ',', $arrColumn ) . ') VALUES (' . implode ( ',', $arrColumuVal ) . ')';
			}
		}
		return $this->execute ( $strQuery );
	}
	public function getColumns($tableName) {
		if ($this->dbPrefix && substr ( $tableName, 0, strlen ( $this->dbPrefix ) ) != $this->dbPrefix) {
			$tableName = $this->dbPrefix . $tableName;
		}
		$daoType = $this->daoType;
		$cache = NCache::getCache ();
		$cachePrefix = 'TColumns';
		$cacheKey = $cachePrefix . '_' . $daoType . '_' . $tableName;
		if (false == APP_DEBUG) {
			$columns = $cache->get ( $cachePrefix, $cacheKey );
		}
		
		if (false == $columns) {
			$methodName = 'get' . ucfirst ( strtolower ( $daoType ) ) . 'Columns';
			$columns = $this->$methodName ( $tableName );
			$cache->set ( $cachePrefix, $cacheKey, $columns );
		}
		return $columns;
	}
	private function getMysqlColumns($tableName) {
		$strQuery = 'SHOW FULL COLUMNS FROM ' . $tableName;
		$columns = $this->getAll ( $strQuery );
		$columnArray = array ();
		foreach ( $columns as $column ) {
			if (preg_match ( '/num|int|bit|bool|real|float|dec/i', $column ['type'] )) {
				$columnArray [$column ['field']] = array (
						'name' => $column ['field'],
						'type' => 'number' 
				);
			} else {
				$columnArray [$column ['field']] = array (
						'name' => $column ['field'],
						'type' => 'string' 
				);
			}
		}
		return $columnArray;
	}
	private function getMssqlColumns($tableName) {
		$strQuery = 'SELECT column_name,data_type
                     FROM information_schema.columns
                     WHERE table_name=' . $this->quote ( $tableName );
		$columns = $this->getAll ( $strQuery );
		$columnArray = array ();
		foreach ( $columns as $column ) {
			if (preg_match ( '/num|int|bit|dec|float|money|real/i', $column ['data_type'] )) {
				$columnArray [$column ['column_name']] = array (
						'name' => $column ['column_name'],
						'type' => 'number' 
				);
			} else {
				$columnArray [$column ['column_name']] = array (
						'name' => $column ['column_name'],
						'type' => 'string' 
				);
			}
		}
		return $columnArray;
	}
	private function getPgsqlColumns($tableName) {
	}
	private function getSqliteColumns($tableName) {
	}
	public static function msStrtotime($datetime) {
		$datetime = preg_replace ( '/:\d{3}[AP]M/i', '', $datetime );
		return strtotime ( $datetime );
	}
	public function concatFields($fieldsArray) {
		$strParts = array ();
		if ($this->daoType == 'MSSQL') {
			foreach ( $fieldsArray as $field ) {
				$strParts [] = 'cast(' . $field . ' as nvarchar(255))';
			}
			return implode ( '+', $strParts );
		} else {
			return 'concat(' . implode ( ',', $fieldsArray ) . ')';
		}
	}
}
;
?>

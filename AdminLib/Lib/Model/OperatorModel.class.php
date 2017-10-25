<?php
class OperatorModel extends CommModel {
        public $dao = null;
        public $tableName = 's_employee';

        public function __construct() {
            $this->dao = Dao::getDao();
        }

        public function find($operCode) {
            $strQuery = 'SELECT scode,sname FROM ' . $this->tableName . '
                         WHERE scode=' . $this->dao->quote($operCode);
            return $this->dao->getRow($strQuery);
        }

}
?>
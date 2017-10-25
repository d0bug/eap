<?php
class ReserveCommAction extends AppCommAction{
	protected $tableName = '';
    protected $examTable = '';
    protected $dao = null;
    protected $operator = null;
    protected $cache = null;


	protected function notNeedLogin() {
		return array ();
	}
	/**
	 * 获取当前模块名称
	 *
	 * @return unknown
	 */
	protected function _currModule(){
		return GROUP_NAME . '-' . MODULE_NAME ;
	}


	protected function _currAction(){
		return GROUP_NAME . '-' . MODULE_NAME . '-' . ACTION_NAME;//当前方法
	}
    public function __construct() {
    	parent::__construct();
    	$this->tableName = 'res_active_list';
        $this->kyTable = 'res_table_info';
        $this->infoTable = 'res_class_info';
        $this->stuTable = 'res_student_list';

        $this->dao = Dao::getDao('MSSQL_CONN');
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
        $this->reserveModel = D('Reserve');

    }

    protected function getUserInfo($stucode = '') {
    	$results = $this->reserveModel->getUserInfo($stucode);
    	return $results;

    }
     protected function getFormStruct($id) {
       $result = $this->reserveModel->getReserveList($id);
       return $result;
    }
    protected function getFormInfo($id) {
        $active_info = $this->getFormStruct($id);
        $files = $this->reserveModel->getReserveInfo($id);
        $data = array();

        for($i = 1;$i <= $active_info['week_num']; $i++) {
            for($j = 1;$j <= $active_info['class_num'];$j++) {
                $data[$j][$i] = array();
            }
        }
        foreach($files as $value) {
            $data[$value['class_id']][$value['week_id']] = $value;

        }
        return $data;
    }



}

?>

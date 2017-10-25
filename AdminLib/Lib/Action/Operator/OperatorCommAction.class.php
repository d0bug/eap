<?php
include(CONF_PATH . '/const.php');
import('COM.SysUtil');
class OperatorCommAction extends Action {
	private static $operators = array();
	private static $students = array();
	protected $student = array();
	protected $operator = array();
	private $key = '';
	public function __construct() {
		parent::__construct();
		#echo md5('BJ11293_0612');exit;
		#print_r($_GET);exit;
		$this->check();
	}
	
	private function check() {
		if(false == $_GET['stu'] || false == $_GET['user'] || false == $_GET['key']) {
			die('error:参数不足');
		}
		if(md5($_GET['stu'] . '_' . $_GET['user']) != $_GET['key']) {
			die('error:参数不匹配');
		}
		$this->key = $_GET['key'];
		$this->student = $this->getStudent();
		$this->operator = $this->getOperator();
	}
	
	public function getUrl($actionName=ACTION_NAME,  $moduleName=MODULE_NAME, $groupName=GROUP_NAME, $args=array()) {
		$stuInfo = $this->getStudent();
		$operInfo = $this->getOperator();
		$args['stu'] = $stuInfo['scode'];
		$args['user'] = $operInfo['scode'];
		$args['key'] = $this->key;
        return U($groupName . '/' . $moduleName . '/' . $actionName, $args);
	}
	
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		$allConstants = get_defined_constants(true);
		$userConstants = $allConstants['user'];
		$this->assign($userConstants);
		$time = time();
		$this->assign('_time', $time);
		$this->assign('operInfo', $this->operator);
		$this->assign('stuInfo', $this->student);
		parent::display($templateFile,$charset,$contentType,$content,$prefix);
	}
	
	protected function getOperator($operCode='') {
        $setThisOper = false;
		if(false == $operCode) {
            $operCode = trim($_GET['user']);
            $setThisOper = true;
        }
        if(false == isset(self::$operators[$operCode])) {
            $operModel = D('Operator');
            self::$operators[$operCode] = $operModel->find($operCode);
        }
        $operInfo = self::$operators[$operCode];
        if($setThisOper) {
            $this->operator = $operInfo;
        }
        return $operInfo;
	}
	
	protected function getStudent($stuCode='') {
		$setThisStu = false;
        if('' == $stuCode) {
            $stuCode = trim($_GET['stu']);
            $setThisStu = true;
        }
        if(false == self::$students[$stuCode]) {
            $stuModel = D('Student');
            self::$students[$stuCode] = $stuModel->getStuInfo($stuCode);
        }
        $stuInfo = self::$students[$stuCode];
        if($setThisStu) {
            $this->student = $stuInfo;
        }
        return $stuInfo;
	}
}
?>
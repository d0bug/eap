<?php
class UtilCommAction extends Action {
	protected function getUrl($actionName=ACTION_NAME, $moduleName=MODULE_NAME, $groupName=GROUP_NAME, $args = array()) {
		$url = U($groupName . '/' . $moduleName . '/'. $actionName, $args, false);
		return $url;
	}
	
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		$allConstants = get_defined_constants(true);
		$userConstants = $allConstants['user'];
		$this->assign($userConstants);
		$time = time();
		$this->assign('_time', $time);
		parent::display($templateFile,$charset,$contentType,$content,$prefix);
	}
}
?>
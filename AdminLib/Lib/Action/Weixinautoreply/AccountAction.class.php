<?php
class AccountAction extends WeixinautoreplyCommAction {
	protected function notNeedLogin() {
		return array ();
	}
	
	// $this->writeCheck($this->getAclKey(''));
	
	/*
	 * 微信账号
	 */
	public function main() {
		import ( 'ORG.Util.Page' );
		$wxarModel = D ( 'WeixinAutoReply' ); 
		$count = $wxarModel->countWeixinAccount ();
		$page = new Page ( $count, 10 );
		$pageshow = $page->show ();
		
		$varPage = C ( 'VAR_PAGE' ) ? C ( 'VAR_PAGE' ) : 'p';
		$nowPage = ! empty ( $_GET [$varPage] ) ? intval ( $_GET [$varPage] ) : 1;
		$arrWeixinAccount = $wxarModel->getWeixinAccount ( $nowPage, $page->listRows );
		$this->assign ( array (
				"arrWeixinAccount" => $arrWeixinAccount,
				"pageshow" => $pageshow 
		) );
		$this->display ();
	}
	public function ajax_addWeixinAccount() {
		if ($this->isPost ()) {
			$wxarModel = D ( 'WeixinAutoReply' );
			$r = $wxarModel->checkUniqueWeixinAccount ( $_POST ['account_id'], $_POST ['account_name'], $_POST ['script_name'] );
			if ($r) {
				$this->print_json ( '', '不能重复添加微信号、微信名称或脚本名称，此次添加失败。', '-1' );
			}
			$r = $wxarModel->addWeixinAccount ( $_POST ['account_id'], $_POST ['account_name'], $_POST ['script_name'] );
			if ($r) {
				$this->print_json ( '', '添加成功。', '1' );
			} else {
				$this->print_json ( '', '添加失败。', '-2' );
			}
		}
	}
	public function tpl_flashWeixinAccount() {
		if ($this->isAjax ()) {
			$wxarModel = D ( 'WeixinAutoReply' );
			$r = $arrWeixinAccount = $wxarModel->getWeixinAccount ();
		}
	}
	private function print_json($data, $info, $status) {
		die ( json_encode ( array (
				'data' => $data,
				'info' => $info,
				'status' => $status 
		) ) );
	}
}

?>
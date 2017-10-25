<?php
class KeywordsAction extends WeixinautoreplyCommAction{
	protected function notNeedLogin() {
		return array (
				'WEIXINAUTOREPLY-KEYWORDS-AJAX_GETRELREPLY',
				'WEIXINAUTOREPLY-KEYWORDS-AJAX_GETISADDEDREPLY',
				'WEIXINAUTOREPLY-KEYWORDS-AJAX_GETNOANSWERREPLY',
		);
	}
	
	public function main(){
		$wxarModel = D ( 'WeixinAutoReply' );
		$arrWeixinAccount = $wxarModel->getWeixinAccount ();
		$arrWeixinRules = $wxarModel->getWeixinRules ();
		$this->assign ( array (
				"arrWeixinAccount" => $arrWeixinAccount,
				"arrWeixinRules" => $arrWeixinRules
		) );
		$this->display();
	}
	
	public function ajax_addWeixinKeywordRule(){
		if ($this->isPost ()) {
			if (empty ( $_POST ['content_type'] ) || ($_POST ['content_type'] != 1 && $_POST ['content_type'] != 2)) {
				die ();
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			$dao = $wxarModel->dao;
			if (empty ( $_POST ['account_id'] ) || ! is_array ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['rule_name'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}
			
			if ($_POST ['content_type'] == 1) {
				if (empty ( $_POST ['reply_content'] )) {
					$this->print_json ( '', '必须填写回复内容。', '-1' );
				}
			}elseif($_POST ['content_type'] == 2){
				if (empty ( $_POST ['reply_title'] )) {
					$this->print_json ( '', '必须填写回复标题。', '-1' );
				}
				if (empty ( $_POST ['reply_desc'] )) {
					$this->print_json ( '', '必须填写回复简介。', '-1' );
				}
				if (empty ( $_POST ['reply_image'] )) {
					$this->print_json ( '', '必须填写回复图片。', '-1' );
				}
				if (empty ( $_POST ['reply_jumpurl'] )) {
					$this->print_json ( '', '必须填写跳转链接。', '-1' );
				}
			}else{
				$this->print_json ( '', '非法操作。', '-1' );
			}
			
			$arrKeyword = array();
			if (! empty ( $_POST ['keywords'] )) {
				$arrKeyword = explode ( ',', $_POST ['keywords'] );
			}
			//step1,校验account_id是否存在,以及在account_id下是否有已经存在的规则明和keywords
			$arrAccountId = $_POST ['account_id'];
			
			$dao->begin ();
			$status = true;
			foreach ( $arrAccountId as $valAccountId ) {
				$r = $wxarModel->checkUniqueWeixinAccount ( $valAccountId );
				if (empty ( $r )) {
					$this->print_json ( '', '您选择的微信号"' . $valAccountId . '"不存在。', '-1' );
				}
				
// 				$strCheckWeixinKeyword = $wxarModel->checkWeixinKeyword ( $valAccountId, array_merge ( $arrKeyword, array ( $_POST ['rule_name'] ) ) );
				$strCheckWeixinKeyword = $wxarModel->checkWeixinKeyword ( $valAccountId, $arrKeyword );
				if ($strCheckWeixinKeyword) {
					$this->print_json ( '', '微信号为' . $valAccountId . '的关键词' . $strCheckWeixinKeyword . '已经被定义过,请使用别的关键词。', '-1' );
				}
				//step2,将规则名数据表中
				$strRuleid = $wxarModel->addWeixinRule ( $_POST ['rule_name'], '', $_POST ['content_type'] );
				if ($strRuleid && ! empty ( $arrKeyword )) {
					// step3,将关键词插入到数据表中
					foreach ( $arrKeyword as $keyword ) {
						$strKeyid = $wxarModel->addWeixinKeyword ( $keyword, $strRuleid );
						if ($strKeyid) {
							// step4,将数据插入关系表中
							if ($wxarModel->addWeixinKeywordRel ( $valAccountId, $strRuleid, $strKeyid )) {
								if ($_POST ['content_type'] == 2) {
									$newsStr = array (
											'reply_title' => $_POST ['reply_title'],
											'reply_desc' => $_POST ['reply_desc'],
											'reply_image' => $_POST ['reply_image'],
											'reply_jumpurl' => $_POST ['reply_jumpurl'] 
									);
									if (! $wxarModel->addOneNewsContent ( $strRuleid, $newsStr )) {
										$status = false;
									}
								}
							} else {
								$status = false;
							}
						} else {
							$status = false;
						}
					}
				} elseif ($strRuleid && empty ( $arrKeyword )) {
					if (! $wxarModel->addWeixinKeywordRel ( $valAccountId, $strRuleid, 0 )) {
						$status = false;
					}
				} else {
					$status = false;
				}
			}
			if ($status === true) {
				$dao->commit ();
				$this->print_json ( '', '规则添加成功。', '1' );
			} else {
				$dao->rollback ();
				$this->print_json ( '', '规则添加失败。', '-1' );
			}
		}
	}
	
	public function ajax_editWeixinKeywordRule(){
		if ($this->isPost ()) {
			if (empty ( $_POST ['content_type'] ) || ($_POST ['content_type'] != 1 && $_POST ['content_type'] != 2)) {
				die ();
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			$dao = $wxarModel->dao;
			if (empty ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['rule_name'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}
			
			if ($_POST ['content_type'] == 1) {
				if (empty ( $_POST ['reply_content'] )) {
					$this->print_json ( '', '必须填写回复内容。', '-1' );
				}
			}elseif($_POST ['content_type'] == 2){
				if (empty ( $_POST ['reply_title'] )) {
					$this->print_json ( '', '必须填写回复标题。', '-1' );
				}
				if (empty ( $_POST ['reply_desc'] )) {
					$this->print_json ( '', '必须填写回复简介。', '-1' );
				}
				if (empty ( $_POST ['reply_image'] )) {
					$this->print_json ( '', '必须填写回复图片。', '-1' );
				}
				if (empty ( $_POST ['reply_jumpurl'] )) {
					$this->print_json ( '', '必须填写跳转链接。', '-1' );
				}
			}else{
				$this->print_json ( '', '非法操作。', '-1' );
			}
			
			$arrKeyword = array();
			if (! empty ( $_POST ['keywords'] )) {
				$arrKeyword = explode ( ',', $_POST ['keywords'] );
			}
			//step1,校验account_id是否存在,以及在account_id下是否有已经存在的规则明和keywords
			$account_id = $_POST ['account_id'];
			
			$rule_id= $_POST ['rule_id'];
			
			$dao->begin ();
			$status = true;
			$status = $wxarModel->delOneWeixinKeywordRel($account_id, $rule_id, $_POST ['content_type']);
			if ($status) {
				$strCheckWeixinKeyword = $wxarModel->checkWeixinKeyword ( $account_id, $arrKeyword );
				if ($strCheckWeixinKeyword) {
					$this->print_json ( '', '关键词"' . $strCheckWeixinKeyword . '"已经被定义过,请使用别的关键词。', '-1' );
				}
			}
			
			if (! empty ( $arrKeyword )) {
				foreach ( $arrKeyword as $keyword ) {
					$strKeyid = $wxarModel->addWeixinKeyword ( $keyword, $rule_id );
					if ($strKeyid) {
						if ($wxarModel->addWeixinKeywordRel ( $account_id, $rule_id, $strKeyid )) {
							if ($_POST ['content_type'] == 2) {
								$newsStr = array (
										'reply_title' => $_POST ['reply_title'],
										'reply_desc' => $_POST ['reply_desc'],
										'reply_image' => $_POST ['reply_image'],
										'reply_jumpurl' => $_POST ['reply_jumpurl'] 
								);
								if (! $wxarModel->addOneNewsContent ( $rule_id, $newsStr )) {
									$status = false;
								}
							}
						} else {
							$status = false;
						}
					} else {
						$status = false;
					}
				}
			} elseif ($rule_id && empty ( $arrKeyword )) {
				if (! $wxarModel->addWeixinKeywordRel ( $account_id, $rule_id, 0 )) {
					$status = false;
				}
			} else {
				$status = false;
			}
			
			//修改rule主体
			$modify_time = time();
			$exec_user = $this->userKey;
			$sqlArr = array (
				'name' => $_POST['rule_name'],
				'reply_content' => $_POST['reply_content'],
				'modify_time' => $modify_time,
				'exec_user' => $exec_user 
			);
			if ($_POST ['content_type'] == 1) {
				
			}elseif ($_POST ['content_type'] == 2) {
				$sqlArr['reply_content'] = '';
			}else{
				$this->print_json ( '', '非法操作。', '-1' );
			}
			
			if(!$dao->save ( $wxarModel->tbl_wxar_rule, $sqlArr, 'id='.$dao->quote($rule_id).' AND content_type='.$dao->quote($_POST['content_type']) )){
				$status = false;
			}

			if ($status === true) {
				$dao->commit ();
				$this->print_json ( '', '规则修改成功。', '1' );
			} else {
				$dao->rollback ();
				$this->print_json ( '', '规则修改失败。', '-1' );
			}
		}
	}

	public function ajax_delWeixinKeywordRule() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['rule_id'] ) || empty( $_POST['account_id'] )) {
				$this->print_json ( '', '非法操作。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			if ($wxarModel->delOneWeixinRule ( $_POST['account_id'], $_POST ['rule_id'], $_POST ['content_type'] )) {
				return $this->print_json ( array (), '删除成功。', '1' );
			}
			return $this->print_json ( array (), '删除失败。', '-1' );
		}
	}
	
	/*
	 * 
	 * 
	 * 
	 */
	public function ajax_getRelReply() {
		if ($this->isPost ()) {
			$account_id = $_POST ['account_id'];
			$keyword = $_POST ['keyword'];
			$wxarModel = D ( 'WeixinAutoReply' );
			$r = $wxarModel->getRelReply ( $account_id, $keyword );
			if ($r) {
				$data = array ();
				$data = $r;
				$info = '';
				$status = '1';
			} else {
				$data = array ();
				$info = '';
				$status = '-1';
			}
			return $this->print_json ( $data, $info, $status );
		}
	}
	
	public function isAddedReplyMain(){
		$wxarModel = D ( 'WeixinAutoReply' );
		$arrWeixinAccount = $wxarModel->getWeixinAccount ();
		$arrIsAddedReply = $wxarModel->getIsAddedReply ();
		$this->assign ( array (
				"arrWeixinAccount" => $arrWeixinAccount,
				"arrIsAddedReply" => $arrIsAddedReply
		) );
		$this->display();
	}
	
	public function ajax_addIsAddedReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] ) || ! is_array ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['reply_content'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			$arrAccountId = $_POST ['account_id'];			
			
			$dao = $wxarModel->dao;
			$dao->begin ();
			$status = true;
			foreach ( $arrAccountId as $valAccountId ) {
				$r = $wxarModel->checkUniqueWeixinAccount ( $valAccountId );
				if (empty ( $r )) {
					$this->print_json ( '', '您选择的微信号"' . $valAccountId . '"不存在。', '-1' );
				}
				if ($wxarModel->countIsAddedReply($valAccountId)) {
					$this->print_json ( '', '您选择的微信号"' . $valAccountId . '"已经添加过自动回复。', '-1' );
				}
				$r = $wxarModel->addIsAddedReply ( $valAccountId, $_POST ['reply_content'], 1 );
				if (! $r) {
					$status = false;
				}
			}
			if ($status === true) {
				$dao->commit ();
				$this->print_json ( '', '规则添加成功。', '1' );
			} else {
				$dao->rollback ();
				$this->print_json ( '', '规则添加失败。', '-1' );
			}
		}
	}
	
	public function ajax_editIsAddedReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['reply_content'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			if ($wxarModel->updateOneIsAddedReply ( $_POST ['account_id'], $_POST ['reply_content'] )) {
				return $this->print_json ( array (), '更新成功。', '1' );
			}
			return $this->print_json ( array (), '更新失败。', '-1' );
		}
	}
	
	public function ajax_delIsAddedReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			if ($wxarModel->delOneIsAddedReply ( $_POST ['account_id'] )) {
				return $this->print_json ( array (), '删除成功。', '1' );
			}
			return $this->print_json ( array (), '删除失败。', '-1' );
		}
	}
	
	public function ajax_getIsAddedReply() {
		if ($this->isPost ()) {
			$account_id = $_POST ['account_id'];
			$wxarModel = D ( 'WeixinAutoReply' );
			$r = $wxarModel->getOneIsAddedReply ( $account_id );
			if ($r) {
				$data = array ();
				$data ['reply_content'] = $r ['reply_content'];
				$data ['content_type'] = $r ['content_type'];
				$info = '';
				$status = '1';
			} else {
				$data = array ();
				$info = '';
				$status = '-1';
			}
			return $this->print_json ( $data, $info, $status );
		}
	}
	
	public function noAnswerReplyMain(){
		$wxarModel = D ( 'WeixinAutoReply' );
		$arrWeixinAccount = $wxarModel->getWeixinAccount ();
		$arrNoAnswerReply = $wxarModel->getNoAnswerReply ();
		$this->assign ( array (
				"arrWeixinAccount" => $arrWeixinAccount,
				"arrNoAnswerReply" => $arrNoAnswerReply
		) );
		$this->display();
	}
	
	public function ajax_addNoAnswerReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] ) || ! is_array ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['reply_content'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			$arrAccountId = $_POST ['account_id'];
				
			$dao = $wxarModel->dao;
			$dao->begin ();
			$status = true;
			foreach ( $arrAccountId as $valAccountId ) {
				$r = $wxarModel->checkUniqueWeixinAccount ( $valAccountId );
				if (empty ( $r )) {
					$this->print_json ( '', '您选择的微信号"' . $valAccountId . '"不存在。', '-1' );
				}
				if ($wxarModel->countNoAnswerReply($valAccountId)) {
					$this->print_json ( '', '您选择的微信号"' . $valAccountId . '"已经添加过自动回复。', '-1' );
				}
				$r = $wxarModel->addNoAnswerReply ( $valAccountId, $_POST ['reply_content'], 1 );
				if (! $r) {
					$status = false;
				}
			}
			if ($status === true) {
				$dao->commit ();
				$this->print_json ( '', '规则添加成功。', '1' );
			} else {
				$dao->rollback ();
				$this->print_json ( '', '规则添加失败。', '-1' );
			}
		}
	}
	
	public function ajax_editNoAnswerReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			if (empty ( $_POST ['reply_content'] )) {
				$this->print_json ( '', '必须填写规则名。', '-1' );
			}			
			$wxarModel = D ( 'WeixinAutoReply' );
			if ($wxarModel->updateOneNoAnswerReply ( $_POST ['account_id'], $_POST ['reply_content'] )) {
				return $this->print_json ( array (), '更新成功。', '1' );
			}
			return $this->print_json ( array (), '更新失败。', '-1' );
		}
	}
	
	public function ajax_delNoAnswerReply() {
		if ($this->isPost ()) {
			if (empty ( $_POST ['account_id'] )) {
				$this->print_json ( '', '必须选择微信号。', '-1' );
			}
			$wxarModel = D ( 'WeixinAutoReply' );
			if ($wxarModel->delOneNoAnswerReply ( $_POST ['account_id'] )) {
				return $this->print_json ( array (), '删除成功。', '1' );
			}
			return $this->print_json ( array (), '删除失败。', '-1' );
		}
	}
	
	public function ajax_getNoAnswerReply() {
		if ($this->isPost ()) {
			$account_id = $_POST ['account_id'];
			$wxarModel = D ( 'WeixinAutoReply' );
			$r = $wxarModel->getOneNoAnswerReply ( $account_id );
			if ($r) {
				$data = array ();
				$data ['reply_content'] = $r ['reply_content'];
				$data ['content_type'] = $r ['content_type'];
				$info = '';
				$status = '1';
			} else {
				$data = array ();
				$info = '';
				$status = '-1';
			}
			return $this->print_json ( $data, $info, $status );
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
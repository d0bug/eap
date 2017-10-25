<?php
class WeixinAutoReplyModel extends Model {
	public $dao = null;
	private $tbl_wxar_account = 'wxar_account';
	public $tbl_wxar_rule = 'wxar_rule';
	private $tbl_wxar_extra_keyword = 'wxar_extra_keyword';
	private $tbl_wxar_account_keywords_rel = 'wxar_account_keywords_rel';
	private $tbl_wxar_autoreply = 'wxar_autoreply';
	private $tbl_wxar_news_content = 'wxar_news_content';
	private $operator = '';
	public function __construct() {
		$this->dao = Dao::getDao ();
		if (class_exists ( 'User', false )) {
			$operator = User::getLoginUser ();
			if ($operator)
				$this->userKey = $operator->getUserKey ();
		}
	}
	public function getWeixinAccount($currentPage = null, $pageSize = null) {
		$strQuery = "SELECT * FROM $this->tbl_wxar_account ORDER BY add_time DESC";
		if ($currentPage && $pageSize) {
			return $this->dao->getLimit ( $strQuery, $currentPage, $pageSize, 'ORDER BY add_time DESC' );
		}
		return $this->dao->getAll ( $strQuery );
	}
	public function addWeixinAccount($account_id, $account_name, $script_name) {
		$add_time = time ();
		$modify_time = 0;
		$exec_user = $this->userKey;
		$sqlArr = array (
				'account_id' => $account_id,
				'account_name' => $account_name,
				'script_name' => $script_name,
				'add_time' => $add_time,
				'modify_time' => $modify_time,
				'exec_user' => $exec_user 
		);
		return $this->dao->save ( $this->tbl_wxar_account, $sqlArr );
	}
	public function checkUniqueWeixinAccount($account_id = null, $account_name = null, $script_name = null) {
		$strQuery = "SELECT count(1) FROM $this->tbl_wxar_account WHERE 0=1";
		if ($account_id) {
			$strQuery .= " OR account_id = " . $this->dao->quote ( $account_id );
		}
		if ($account_name) {
			$strQuery .= " OR account_name = " . $this->dao->quote ( $account_name );
		}
		if ($script_name) {
			$strQuery .= " OR script_name = " . $this->dao->quote ( $script_name );
		}
		return $this->dao->getOne ( $strQuery );
	}
	public function countWeixinAccount() {
		$strQuery = "SELECT count(1) FROM $this->tbl_wxar_account";
		return $this->dao->getOne ( $strQuery );
	}
	public function getWeixinRules($currentPage = null, $pageSize = null) {
		$strQuery = "SELECT a.account_id, a.account_name, r.* FROM $this->tbl_wxar_rule AS r INNER JOIN $this->tbl_wxar_account_keywords_rel AS rel ON r.id = rel.rule_id INNER JOIN $this->tbl_wxar_account AS a ON rel.account_id = a.account_id ORDER BY r.add_time DESC";
		if ($currentPage && $pageSize) {
			return $this->dao->getLimit ( $strQuery, $currentPage, $pageSize, 'ORDER BY add_time DESC' );
		}
		$arrRules = $this->dao->getAll ( $strQuery );
		if ($arrRules) {
			foreach ( $arrRules as $keyRule => $valRule ) {
				if ($valRule ['content_type'] == 2) {
					$strQuery = "SELECT * FROM $this->tbl_wxar_news_content WHERE rule_id =" . $this->dao->quote ( $valRule ['id'] ) . " ORDER BY order_index ASC";
					$arrNewsContent = $this->dao->getAll ( $strQuery );
					$arrRules [$keyRule] ['news_content'] = $arrNewsContent;
				}
				$strQuery = "SELECT * FROM $this->tbl_wxar_extra_keyword WHERE rule_id = " . $this->dao->quote ( $valRule ['id'] );
				$arrKeywords = $this->dao->getAll ( $strQuery );
				$arrKeywordItems = array ();
				foreach ( $arrKeywords as $keyKeyword => $valKeyword ) {
					$arrKeywordItems [] = $valKeyword ['keyword'];
				}
				$strKeywordItems = implode ( ',', $arrKeywordItems );
				if ($arrKeywordItems) {
					$arrRules [$keyRule] ['keywords'] = $strKeywordItems;
				}
			}
		}
		return $arrRules;
	}
	public function checkWeixinKeyword($arrAccountId, $arrKeyword) {
// 		$strQuery = "SELECT name AS keyword FROM $this->tbl_wxar_rule WHERE id IN (SELECT rule_id FROM $this->tbl_wxar_account_keywords_rel WHERE account_id = " . $this->dao->quote ( $arrAccountId ) . " GROUP BY rule_id)";
// 		$arrRuleName = $this->dao->getAll ( $strQuery );
		
		$strQuery = "SELECT keyword FROM $this->tbl_wxar_extra_keyword WHERE id IN (SELECT keyword_id FROM $this->tbl_wxar_account_keywords_rel WHERE account_id = " . $this->dao->quote ( $arrAccountId ) . " GROUP BY keyword_id)";
		$arrKeywords = $this->dao->getAll ( $strQuery );
		
// 		$arrKeywords = array_merge ( $arrKeywords, $arrRuleName );
		$arrExistKeyword = array ();
		foreach ( $arrKeywords as $itemKeyword ) {
			$arrExistKeyword [] = $itemKeyword ['keyword'];
		}
		
		$arrExistKeyword = array_unique ( $arrExistKeyword );
		
		$arrReturn = array ();
		foreach ( $arrKeyword as $oneKeyword ) {
			if (in_array ( $oneKeyword, $arrExistKeyword )) {
				$arrReturn [] = $oneKeyword;
			}
		}
		$strReturn = implode ( ',', $arrReturn );
		return $strReturn;
	}
	
	public function addWeixinRule($name, $reply_content, $content_type) {
		$add_time = time ();
		$modify_time = 0;
		$exec_user = $this->userKey;
		$sqlArr = array (
				'name' => $name,
				'reply_content' => $reply_content,
				'content_type' => $content_type,
				'add_time' => $add_time,
				'modify_time' => $modify_time,
				'exec_user' => $exec_user 
		);
		if ($this->dao->save ( $this->tbl_wxar_rule, $sqlArr )) {
			return $this->dao->lastInsertId ();
		}
		return false;
	}
	
	public function addWeixinKeyword($keyword, $rule_id) {
		$add_time = time ();
		$exec_user = $this->userKey;
		$sqlArr = array (
				'keyword' => $keyword,
				'rule_id' => $rule_id,
				'add_time' => $add_time,
				'exec_user' => $exec_user 
		);
		if ($this->dao->save ( $this->tbl_wxar_extra_keyword, $sqlArr )) {
			return $this->dao->lastInsertId ();
		}
		return false;
	}
	
	public function addWeixinKeywordRel($account_id, $rule_id, $keyword_id) {
		$sqlArr = array (
				'account_id' => $account_id,
				'rule_id' => $rule_id,
				'keyword_id' => $keyword_id 
		);
		return $this->dao->save ( $this->tbl_wxar_account_keywords_rel, $sqlArr );
	}
	
	public function addOneNewsContent($rule_id, $content) {
		$add_time = time ();
		$modify_time = 0;
		$exec_user = $this->userKey;
		$sqlArr = array (
				'rule_id' => $rule_id,
				'news_title' => $content ['reply_title'],
				'news_description' => $content ['reply_desc'],
				'news_picurl' => $content ['reply_image'],
				'news_url' => $content ['reply_jumpurl'],
				'order_index' => '1',
				'add_time' => $add_time,
				'modify_time' => $modify_time,
				'exec_user' => $exec_user 
		);
		return $this->dao->save ( $this->tbl_wxar_news_content, $sqlArr );
	}
	
	public function delOneWeixinRule($account_id, $rule_id, $content_type) {
		// $content_type为1是文本消息，2是图文消息
		$strQuery = "DELETE FROM $this->tbl_wxar_rule WHERE id =" . $this->dao->quote ( $rule_id );
		$this->dao->begin ();
		$status = true;
		if ($this->dao->execute ( $strQuery )) {
			$strQuery = "DELETE FROM $this->tbl_wxar_extra_keyword WHERE rule_id =" . $this->dao->quote ( $rule_id );
			if ($this->dao->execute ( $strQuery )) {
				$strQuery = "DELETE FROM $this->tbl_wxar_account_keywords_rel WHERE account_id =" . $this->dao->quote($account_id) . " AND rule_id =" . $this->dao->quote($rule_id);
				if ($this->dao->execute ( $strQuery )) {
					if ($content_type == 2) {
						$strQuery = "DELETE FROM $this->tbl_wxar_news_content WHERE rule_id =" . $this->dao->quote ( $rule_id );
						if (! $this->dao->execute ( $strQuery )) {
							$status = false;
						}
					}
				}else{
					$status = false;
				}
			} else {
				$status = false;
			}
		} else {
			$status = false;
		}
		
		if ($status === true) {
			$this->dao->commit ();
		} else {
			$this->dao->rollback ();
		}
		return $status;
	}

	public function delOneWeixinKeywordRel($account_id, $rule_id, $content_type) {
		// $content_type为1是文本消息，2是图文消息
		// $this->dao->begin ();
		$status = true;

		//删除关键词和对应关系等信息
		$strQuery = "DELETE FROM $this->tbl_wxar_extra_keyword WHERE rule_id =" . $this->dao->quote ( $rule_id );
		if ($this->dao->execute ( $strQuery )) {
			$strQuery = "DELETE FROM $this->tbl_wxar_account_keywords_rel WHERE account_id =" . $this->dao->quote($account_id) . " AND rule_id =" . $this->dao->quote($rule_id);
			if ($this->dao->execute ( $strQuery )) {
				if ($content_type == 2) {
					$strQuery = "DELETE FROM $this->tbl_wxar_news_content WHERE rule_id =" . $this->dao->quote ( $rule_id );
					if (! $this->dao->execute ( $strQuery )) {
						$status = false;
					}
				}
			}else{
				$status = false;
			}
		} else {
			$status = false;
		}
		
		// if ($status === true) {
		// 	$this->dao->commit ();
		// } else {
		// 	$this->dao->rollback ();
		// }
		return $status;
	}
	
	public function getRelReply($account_id, $keyword){
		$strQuery = "SELECT r.* FROM $this->tbl_wxar_rule AS r INNER JOIN $this->tbl_wxar_account_keywords_rel AS re ON r.id = re.rule_id INNER JOIN $this->tbl_wxar_extra_keyword AS k ON r.id = k.rule_id WHERE re.account_id = " . $this->dao->quote ( $account_id ) . " AND k.keyword = " . $this->dao->quote ( $keyword );
		$arrRule = $this->dao->getRow ( $strQuery );
		if ($arrRule ['content_type'] == 2) {
			$strQuery = "SELECT * FROM $this->tbl_wxar_news_content WHERE rule_id =" . $this->dao->quote ( $arrRule ['id'] ) . " ORDER BY order_index ASC";
			$arrNewsContent = $this->dao->getAll ( $strQuery );
			$arrRule ['news_content'] = $arrNewsContent;
		}
		return $arrRule;
	}
	
	
	/******************
	 * 
	 * 被添加后的数据相关操作们
	 *  
	 ******************
	 */
	
	public function addIsAddedReply($account_id, $reply_content, $content_type) {
		$add_time = time ();
		$modify_time = 0;
		$exec_user = $this->userKey;
		
		$sqlArr = array (
				'account_id' => $account_id,
				'reply_content' => $reply_content,
				'content_type' => $content_type,
				'reply_type' => '1',
				'add_time' => $add_time,
				'modify_time' => $modify_time,
				'exec_user' => $exec_user
		);
		return $this->dao->save ( $this->tbl_wxar_autoreply, $sqlArr );
	}
	
	public function getIsAddedReply($currentPage = null, $pageSize = null) {
		$strQuery = "SELECT y.*, a.account_name FROM $this->tbl_wxar_autoreply as y INNER JOIN $this->tbl_wxar_account AS a ON y.account_id = a.account_id WHERE y.reply_type = 1 ORDER BY y.add_time DESC";
		if ($currentPage && $pageSize) {
			return $this->dao->getLimit ( $strQuery, $currentPage, $pageSize, 'ORDER BY y.add_time DESC' );
		}
		return $this->dao->getAll ( $strQuery );
	}
	
	public function getOneIsAddedReply($account_id) {
		$strQuery = "SELECT * FROM $this->tbl_wxar_autoreply WHERE account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 1";
		return $this->dao->getRow ( $strQuery );
	}
	
	public function delOneIsAddedReply($account_id) {
		$strQuery = "DELETE FROM $this->tbl_wxar_autoreply WHERE account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 1";
		return $this->dao->execute ( $strQuery );
	}
	
	public function updateOneIsAddedReply($account_id, $reply_content) {
		$data = array ();
		$data ['reply_content'] = $reply_content;
		$data ['modify_time'] = time();
		$condition = "account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 1";
		return $this->dao->save ( $this->tbl_wxar_autoreply, $data, $condition );
	}
	
	public function countIsAddedReply($account_id = null) {
		$strQuery = "SELECT count(1) FROM $this->tbl_wxar_autoreply WHERE reply_type = 1";
		if ($account_id) {
			$strQuery .= " AND account_id = " . $this->dao->quote ( $account_id );
		}
		return $this->dao->getOne ( $strQuery );
	}
	
	
   /*************************
	*
	* 关键词未被匹配到时数据的相关操作们
	*
	*************************
	*/
	
	public function addNoAnswerReply($account_id, $reply_content, $content_type) {
		$add_time = time ();
		$modify_time = 0;
		$exec_user = $this->userKey;
	
		$sqlArr = array (
				'account_id' => $account_id,
				'reply_content' => $reply_content,
				'content_type' => $content_type,
				'reply_type' => '2',
				'add_time' => $add_time,
				'modify_time' => $modify_time,
				'exec_user' => $exec_user
		);
		return $this->dao->save ( $this->tbl_wxar_autoreply, $sqlArr );
	}
	
	public function getNoAnswerReply($currentPage = null, $pageSize = null) {
		$strQuery = "SELECT y.*, a.account_name FROM $this->tbl_wxar_autoreply as y INNER JOIN $this->tbl_wxar_account AS a ON y.account_id = a.account_id WHERE y.reply_type = 2 ORDER BY y.add_time DESC";
		if ($currentPage && $pageSize) {
			return $this->dao->getLimit ( $strQuery, $currentPage, $pageSize, 'ORDER BY y.add_time DESC' );
		}
		return $this->dao->getAll ( $strQuery );
	}
	
	public function getOneNoAnswerReply($account_id) {
		$strQuery = "SELECT * FROM $this->tbl_wxar_autoreply WHERE account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 2";
		return $this->dao->getRow ( $strQuery );
	}
	
	public function delOneNoAnswerReply($account_id) {
		$strQuery = "DELETE FROM $this->tbl_wxar_autoreply WHERE account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 2";
		return $this->dao->execute ( $strQuery );
	}
	
	public function updateOneNoAnswerReply($account_id, $reply_content) {
		$data = array ();
		$data ['reply_content'] = $reply_content;
		$data ['modify_time'] = time();
		$condition = "account_id =" . $this->dao->quote ( $account_id ) . " AND reply_type = 2";
		return $this->dao->save ( $this->tbl_wxar_autoreply, $data, $condition );
	}
	
	public function countNoAnswerReply($account_id = null) {
		$strQuery = "SELECT count(1) FROM $this->tbl_wxar_autoreply WHERE reply_type = 2";
		if ($account_id) {
			$strQuery .= " AND account_id = " . $this->dao->quote ( $account_id );
		}
		return $this->dao->getOne ( $strQuery );
	}
	
	
}
?>
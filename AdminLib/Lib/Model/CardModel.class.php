<?php
/**
 * 激活卡组基础模型类
 */
class CardModel {
	private $dao = null;
	private $tableName = '';
	private $codeTable = '';
	private $cardGroupLogTable = '';
	private $operator = null;
	public function __construct(){
		import('COM.SysUtil');
		$this->dao = Dao::getDao();
		$this->tableName = 'ex_card_groups';
		$this->codeTable = 'ex_cards';
		$this->cardGroupLogTable = 'ex_card_group_logs';
		$this->operator = User::getLoginUser(C('USER_COOKIE_NAME'));
	}
	/**
	 * 获取密码组成元素(不包含可能会产生干扰的元素)
	 * $type 为d时，密码将全部由数字组成；为c 密码全部由字母组成； 默认将由数字+字母组成
	 * @param int $type 元素类型
	 * @return string 密码组成元素
	 */
	private function _getPassElement($type){
		switch($type){
			case 'd':
				return '23456789';
				break;
			case 'c':
				return 'abcdefghjkmnpqrtvwuxy';
				break;
			default :
				return '3456789abcdefghjkmnpqrtvwuxy';
		}
	}
	
	/**
	 * 生成激活号的sql语句
	 *
	 * @param int $groupId	卡组id
	 * @param int $total	生成卡号总量
	 * @param int $start	卡号流水的开始号码
	 * @param string $card_pre		卡号前缀
	 * @param int $card_length	卡号长度（含卡前缀长度）
	 * @param string $pass_element	卡密组成元素
	 * @param int $pass_length	卡密长度
	 * @param int $logId	生成记录日志id
	 * @return string	激活卡号sql语句
	 */
	private  function _genCodeSql($groupId = 0, $total = 1000, $start = 1, $card_pre = 'GS', $card_length = 8, $pass_element = 'd', $pass_length = 6, $logId = 0){
		$numLength = ($card_length - strlen($card_pre));
		$passElement = ($this->_getPassElement($pass_element));
		$passElement = str_repeat($passElement, ceil($pass_length/strlen($passElement)));
		$logId = abs($logId);
		
		$data = array();
		$codeData = array();
		$sql = '';
		$cTime = date('Y-m-d H:i:s');

		for($i = 1, $j = $start ; $i <= $total; $i++, $j++ ){
			$code = strtoupper( $card_pre . sprintf('%0' . $numLength . 'd', $j) ) ;
			$pass = strtoupper( substr(str_shuffle($passElement), 0, $pass_length) );

			if($i != 0 && ($i%100) == 0){
				$codeData[] = " SELECT '$groupId', '$code', '$pass', '1', '$logId',  '$cTime' ";
				$sql .= "INSERT INTO $this->codeTable(card_group, card_num, card_pwd, card_status, log_id, create_at) "  .  implode(' UNION ', $codeData) . '; ';

				$codeData = array();
			}else{
				$codeData[] = " SELECT '$groupId', '$code', '$pass', '1', '$logId', '$cTime' ";
				//$codeData[] = " SELECT '$groupId', '$code', '$pass' ";
			}
		}

		return   '' == $sql ?  "INSERT INTO $this->codeTable(card_group, card_num, card_pwd, card_status, log_id, create_at) "  .  implode(' UNION ', $codeData) . '; ' : $sql ;
	}
	/**
	 * 判断卡组是否存在(尚未启用)
	 * 特别注意：当前激活卡号的时候 匹配的是 卡组id+激活号+卡密 所以只要属于不同考试的，即使是相同卡号也不会有冲突
	 * @param String $prefix 卡前缀
	 * @return boolen 卡组是否已经存在
	 */
	private function _isExistCardGroup($prefix){
		$strNum = preg_match('/^[0-9]/', $prefix, $match);
		if($strNum > 0){
			$testPrefix = rtrim($prefix, '0123456789');
		}else{
			$testPrefix = $prefix;
		}
		$sql = "
				SELECT COUNT(1) FROM $this->tableName WHERE card_pre = '$testPrefix'
			";
		$cnt = $this->dao->getOne($sql);
		return $cnt >=  1;
	}

	/**
	 * 保存卡组信息
	 *
	 * @param Array $para 表单提交来的卡组form信息
	 * @return Array	操作结果
	 */
	public function saveCardGroup($para){
		$id = abs($para['id']);
		if(0 === $id){
			return $this->_addCardGroup($para);
		}else{
			return $this->_editCardGroup($para);
		}
	}// the end of save function

	/**
	 * 添加卡组信息
	 *
	 * @param Array $para	form数据
	 * @return Array	操作结果
	 */
	private function _addCardGroup($para){

		$group_name = ckInput($para['group_name']);
		if('' == $group_name){
			return array('error' => true, 'message' => '组别名称 不能为空', 'data' => 'group_name');
		}

		$group_total = abs($para['group_total']);
		if(0 == $group_total){
			return array('error' => true, 'message' => '卡数量 不能为空', 'data' => 'group_total');
		}

		$card_max_num = $group_total;

		$card_length = abs($para['card_length']);
		if(0 == $card_length){
			return array('error' => true, 'message' => '卡号长度 不能为空', 'data' => 'card_length');
		}

		$card_pre = ckInput($para['card_pre']);
		if('' == $card_pre){
			return array('error' => true, 'message' => '卡号前缀 不能为空', 'data' => 'card_pre');
		}
		/*$isExistPrefix = $this->_isExistCardGroup($card_pre);
		if ($isExistPrefix) {
		return array('error' => true, 'message' => '卡号前缀 与现有卡冲突', 'data' => 'card_pre');
		}*/

		$numLength =  $card_length - strlen($card_pre);
		if ($numLength < 0) {
			return array('error' => true, 'message' => '卡号长度不足以容纳卡前缀，请加长卡号长度或者减小前缀长度', 'data' => array());
		}
		$max = pow(10, $numLength);
		if( $max < $group_total){
			return array('error' => true, 'message' => '抱歉！当前卡号长度的最大容量是' . $max . ' 不能满足生成 ' . $group_total . ' 张卡！', 'data' => array());
		}


		$pass_length = abs($para['pass_length']);
		$pass_length = 0 == $pass_length ? 8 : $pass_length;
		$pass_element = ckInput($para['pass_element']);

		$created = date('Y-m-d H:i:s');
		$create_user_id = $this->operator->getUserKey();

		$sql = '
			INSERT INTO ' . $this->tableName . '
			(
			 group_name,  group_total, card_max_num, card_length, card_pre, pass_length, pass_element,    create_at, create_user_id
			) 
			VALUES
			(
	 		' . $this->dao->quote($group_name) . ', 
	 		' . $this->dao->quote($group_total) . ', 
	 		' . $this->dao->quote($card_max_num) . ', 
	 		' . $this->dao->quote($card_length) . ', 
	 		' . $this->dao->quote($card_pre) . ', 
	 		' . $this->dao->quote($pass_length) . ', 
	 		' . $this->dao->quote($pass_element) . ', 
			\'' . $created .'\',
			\'' . $create_user_id . '\');
		';	

		$this->dao->begin();
		//添加卡组记录
		$rs = $this->dao->execute($sql);

		if (false === $rs) {
			$this->dao->rollback();
			return array('error' => true, 'message' => '操作失败'  , 'data' => array());
		}
		
		$lastGroupId = $this->dao->getOne("SELECT @@IDENTITY AS 'Identity'");
		
		//添加生成记录
		$rs = $this->saveGenCodeLog(array('gid' => $lastGroupId, 'card_count' => $group_total, 'start_num' => 1));
		if ($rs['error']) {
			$this->dao->rollback();
			return array('error' => true, 'message' => $rs['message'], 'data' => array());
		}
		$log_id = $rs['data']['last_insert_id'];
		
		//生成卡号
		$codeSql = $this->_genCodeSql($lastGroupId, $group_total, 1, $card_pre, $card_length, $pass_element, $pass_length, $log_id);
		$codeRs = $this->dao->execute($codeSql);

		if (false === $codeRs) {
			$this->dao->rollback();
			return array('error' => true, 'message' => '生成卡号失败'  , 'data' => array());
		}

		$this->dao->commit();
		return array('error' => false, 'message' => '操作成功', 'data' => array());

	}// the end of add function
	
	/**
	 * 编辑卡组信息
	 *
	 * @param Array	$para	form提交数据
	 * @return Array	操作结果
	 */
	private function _editCardGroup($para){
		$id = abs($para['id']);
		$groupName = ckInput($para['group_name']);
		if('' == $groupName){
			return array('error' => true, 'message' => '组别名称 不能为空', 'data' => 'group_name');
		}

		$updated = date('Y-m-d H:i:s');
		$update_user_id = $this->operator->getUserKey();


		$sql = "
			UPDATE $this->tableName 
			SET group_name = ? ,
				update_at = ? ,
				update_user_id = ?
			WHERE gid = ?
			;
		";	
		
		//$this->startTrans();
		$st = $this->dao->pdo->prepare($sql);
		$rs = $st->execute(array($groupName, $updated, $update_user_id, $id));
//		dumps($st->errorInfo());
		if (false === $rs) {
			return array('error' => true, 'message' => '操作失败'  , 'data' => array());
		}
		return array('error' => false, 'message' => '操作成功', 'data' => array());
	}// the end of edit function
	
	/**
	 * 删除卡组信息
	 *
	 * @param Array $para	
	 * @return Array
	 */
	public function delCardGroup($para){

		$id = abs($para['id']);
		$delete_at= date('Y-m-d H:i:s');
		$delete_user_id = $this->operator->getUserKey();

		$sql = "
			UPDATE $this->tableName 
			SET is_deleted = '1',
			delete_at = '$delete_at',
			delete_user_id = '$delete_user_id'
			WHERE gid = '$id'
			;
		";	

		$rs = $this->dao->execute($sql);
		if (false === $rs) {
			return array('error' => true, 'message' => '操作失败'  , 'data' => array());
		}
		return array('error' => false, 'message' => '操作成功', 'data' => array());

	}// the end of del function

	/**
	 * 通过uid获取卡组信息
	 *
	 * @param Int $id 卡组id
	 * @return Array	卡组信息数组
	 */
	public function getCardGroupByGid($id){
		$id = abs($id);
		$sql = "
			SELECT * 
			FROM $this->tableName
			WHERE gid = '$id'
		";

		return $this->dao->getRow($sql);
	}
	
	/**
	 * 卡组分页信息
	 *
	 * @param Array	$para	分页信息
	 * @return Array	分页数据
	 */
	public function pageCardGroup($para){

		$page = isset($para['page']) ? abs($para['page']) : 1;
		$pageSize = isset($para['rows']) ? abs($para['rows']) : 20;
		$start = ($page - 1) * $pageSize;

		//$sortName = isset($_GET['sortname']) ? trim($_GET['sortname']) : 'id';
		//$sortOrder = isset($_GET['sortorder']) ? trim($_GET['sortorder']) : 'DESC';

		$condtion = "  is_deleted = '0'  ";

		//根据关键字 组织 查询条件
		if (isset($_GET['keyword']) && '' != $_GET['keyword']) {
			$keyword = ckInput($_GET['keyword']);
			$condtion .= " AND t.realname LIKE '%$keyword%'  ";
		}

		$sql = "
			SELECT t.gid, t.group_name, t.card_max_num, t.card_pre, t.card_length, t.pass_length, t.create_at, t.create_user_id, CONVERT(varchar(100), create_at, 23) cdate
			FROM 
				$this->tableName t
			WHERE $condtion
		";

		$order = 'ORDER BY  create_at desc';;
		$data = array('rows' => array(), 'total' => '0');
		$list =  $this->dao->getLimit($sql, $page, $pageSize, $order);

		if($list){
			/*for ($i = 0, $n =  count($list); $i < $n; $i++) {
			$list[$i]['id'] = $list[$i]['id'];
			}*/
			$data['rows'] = $list;

			$countSQL = "SELECT COUNT(1) AS cnt FROM $this->tableName t WHERE  $condtion ";
			$count =  $this->dao->getOne($countSQL);
			$data['total'] = abs($count);
		}

		return $data;

	}// the end of page function
	
	/**
	 * 激活卡号分页数据
	 *
	 * @param Array $para	分页信息
	 * @return Array	分页数据
	 */
	public function pageCode($para){

		$page = isset($para['page']) ? abs($para['page']) : 1;
		$pageSize = isset($para['rows']) ? abs($para['rows']) : 20;
		$start = ($page - 1) * $pageSize;
		$groupId = abs($para['gid']);

		$condtion = "  card_group = '$groupId'  ";
		$sql = "
			SELECT t.cid, t.card_group, t.card_num, t.card_pwd, t.card_status,  CONVERT(varchar(100), create_at, 23) cdate, CONVERT(varchar(100), update_at, 23) udate
			FROM 
				$this->codeTable t
			WHERE $condtion
		";

		$order = 'ORDER BY  cid ASC';;
		$data = array('rows' => array(), 'total' => '0');
		$list =  $this->dao->getLimit($sql, $page, $pageSize, $order);

		if($list){
			/*for ($i = 0, $n =  count($list); $i < $n; $i++) {
			$list[$i]['id'] = $list[$i]['id'];
			}*/
			$data['rows'] = $list;

			$countSQL = "SELECT COUNT(1) AS cnt FROM $this->codeTable t WHERE  $condtion ";
			$count =  $this->dao->getOne($countSQL);
			$data['total'] = abs($count);
		}

		return $data;

	}
	
	/**
	 * 获取卡组id与caption的键值对数组
	 *
	 * @param Int $limit	提取卡组信息个数
	 * @return Array	
	 */
	public function arrayCardGroup($limit){
		$sql = "
			SELECT gid, group_name
			FROM $this->tableName
			WHERE is_deleted = '0'
		";
		$order = ' ORDER BY gid DESC';
		$list =  $this->dao->getLimit($sql, 1, $limit, $order);
		$data =array();

		foreach ($list as $group){
			$data[$group['gid'] . '_'] = $group['group_name'];
		}

		return $data;
	}
	
	/**
	 * 记录每次生成卡号的相关信息
	 *
	 * @param Array $para 卡组信息
	 * @return Array	操作结果
	 */
	public function logCardGroup($para){
		$gid = abs($para);

		$start_num = abs($para['start_num']);
		$card_count = abs($para['card_count']);

		$carete_at = date('Y-m-d H:i:s');
		$create_user_id = $this->operator->getUserKey();

		$sql = '
			INSERT INTO ' . $this->cardGroupLogTable . ' (gid, card_count, start_num, create_at, create_user_id) VALUES(
			' . $this->dao->quote($gid) . ', 
			' . $this->dao->quote($card_count) . ', 
			' . $this->dao->quote($start_num) . ', 
			' . $this->dao->quote($carete_at) . ', 
			' . $this->dao->quote($create_user_id) . '
			);
		';

		$rs = $this->dao->execute($sql);

		if (false === $rs) {
			return array('error' => true, 'message' => '操作失败'  , 'data' => array());
		}
		return array('error' => false, 'message' => '操作成功', 'data' => array());
	}
	
	/**
	 * 追加卡组信息
	 *
	 * @param Array $para	追加卡号卡组信息
	 * @return Array	操作结果
	 */
	public function appendCode($para){
		$gid = abs($para['gid']);
		$card_count = abs($para['append_count']);

		$cardGroup = $this->getCardGroupByGid($gid);

		$start_num = abs($cardGroup['card_max_num']) + 1;

		$cardPre = $cardGroup['card_pre'];
		$cardLength = $cardGroup['card_length'];
		
		//校验剩余卡量是否可以满足当前追加的卡数
		$numLength =  $cardGroup['card_length'] - strlen($cardGroup['card_pre']);
		$max = pow(10, $numLength);
		$sql = "SELECT COUNT(1) FROM $this->codeTable WHERE card_group = '$gid'";
		$currNum = $this->dao->getOne($sql);
		if( ($max-$currNum) < $card_count){
			return array('error' => true, 'message' => '抱歉！当前卡号长度的最多还可以生成 ' . ($max-$currNum) . ' 张卡！', 'data' => array());
		}
		
		
		$passEle = $cardGroup['pass_element'];
		$passLen = $cardGroup['pass_length'];

		$create_at = date('Y-m-d H:i:s');
		$create_user_id =  $this->operator->getUserKey();

		$this->dao->begin();

		//插入追加记录
		$rs = $this->saveGenCodeLog(array('gid' => $gid, 'card_count' => $card_count, 'start_num' => $start_num));
		if ($rs['error']) {
			$this->dao->rollback();
			return array('error' => true, 'message' => $rs['message'], 'data' => array());
		}
		$logId = $rs['data']['last_insert_id'];
		

		//插入卡号
		$codeSql = $this->_genCodeSql($gid, $card_count, $start_num, $cardPre, $cardLength, $passEle, $passLen, $logId);
		$rs = $this->dao->pdo->exec($codeSql);
		if(false === $rs){
			$this->dao->rollback();
			return array('error' => true, 'message' => '追加卡号失败', 'data' => array());
		}


		//更改最大值
		$total = $cardGroup['card_max_num'] + $card_count;
		$sql = "
			UPDATE $this->tableName SET card_max_num = '$total', group_total = '$total' 	WHERE gid = '$gid'
		";
		$rs = $this->dao->pdo->exec($sql);
		if(false === $rs){
			$this->dao->rollback();;
			return array('error' => true, 'message' => '更新卡组信息失败', 'data' => array());
		}

		$this->dao->commit();
		return array('error' => false, 'message' => '操作成功', 'data' => array());
	}
	
	/**
	 * 保存卡号生成记录
	 *
	 * @param unknown_type $para
	 * @return unknown
	 */
	public function saveGenCodeLog($para){
		$gid = abs($para['gid']);
		$card_count = abs($para['card_count']);
		$start_num = abs($para['start_num']);
 		
		$create_at = date('Y-m-d H:i:s');
		$create_user_id = $this->operator->getUserKey();
		
		$sql = "
				INSERT INTO $this->cardGroupLogTable(gid, card_count, start_num, create_at, create_user_id) VALUES(?,?,?,?,?);
			";
		$st = $this->dao->pdo->prepare($sql);
		$rs = $st->execute(array($gid, $card_count, $start_num, $create_at, $create_user_id));
		if(false === $rs){
			return array('error' => true, 'message' => '追加卡号失败', 'data' => array());
		}
		$lastAppendId = $this->dao->getOne("SELECT @@IDENTITY AS 'Identity'");
		return array('error' => false, 'message' => '操作成功', 'data' => array('last_insert_id' => $lastAppendId));
	}
	
	/**
	 * 列表卡号生成记录信息
	 *
	 * @param Int $gid	卡组id
	 * @return Array 卡组列表信息
	 */
	public function listCodeLogByGid($gid){
		$gid = abs($gid);
		$sql = "
			SELECT id, card_count, start_num, create_at, create_user_id FROM $this->cardGroupLogTable WHERE gid = '$gid' ORDER BY create_at ASC
		";
		return $this->dao->getAll($sql);
	}

	/**
	 * 导出卡号
	 *
	 * @param Array $para	卡组id
	 * @return Array	卡组卡号信息
	 */
	public function exportFile($para){
		$fileName = trim(urldecode($para['c'])) . '_' . date('Y-m-d');
		$gid = abs($para['gid']);
		$logid = abs($para['log_id']);
		
		if (0 !== $gid) {
			$sql = "
				SELECT card_num, card_pwd FROM $this->codeTable WHERE card_group = '$gid'
			";
		}else{
			$sql = "
				SELECT card_num, card_pwd FROM $this->codeTable WHERE log_id = '$logid' 
			";
		}
		
		$list = $this->dao->getAll($sql);
		$content = '
			<table border="1">
				<tr>
					<th>卡号</th>
					<th>密码</th>
				</tr>
		';
		
		for($i = 0, $n = count($list); $i < $n; $i++ ){
			$content .= '<tr><td>' . $list[$i]['card_num'] . '</td><td>' . $list[$i]['card_pwd'] . '</td></tr>';
		}
		$content .= '</table>';
		
		$data = array();
		$data['content'] = $content;
		$data['fileName'] = $fileName;
		return $data;
	}
	
}
?>
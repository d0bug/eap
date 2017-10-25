<?php
/* VIP知识库系统 */
class QuestionCommAction extends AppCommAction {
	protected $dictTypes = array (
			array (
					'cate' => 'QUESTION_TYPE',
					'name' => '题型' 
			),
			array (
					'cate' => 'PROVINCE',
					'name' => '省份' 
			),
			array (
					'cate' => 'GRADE',
					'name' => '年级' 
			),
			array (
					'cate' => 'DEPARTMENT',
					'name' => '部门' 
			) 
	);
	protected $optionFlags = array (
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z',
			'A2',
			'B2',
			'C2',
			'D2',
			'E2',
			'F2',
			'G2',
			'H2',
			'I2',
			'J2',
			'K2',
			'L2',
			'M2',
			'N2',
			'O2',
			'P2',
			'Q2',
			'R2',
			'S2',
			'T2',
			'U2',
			'V2',
			'W2',
			'X2',
			'Y2',
			'Z2' 
	);
	public function __construct() {
		parent::__construct ();
	}
	protected function notNeedLogin() {
		return array ();
	}
	function outPut($data) {
		if (is_array ( $data )) {
			echo json_encode ( $data );
		} else {
			echo $data;
		}
		die ();
	}
	function success() {
		$this->outPut ( array (
				'status' => true 
		) );
	}
	function error() {
		$this->outPut ( array (
				'status' => false 
		) );
	}
	function getDropdownDefault($cate) {
		$default = array (
				'id' => '',
				'name' => '请选择...',
				'selected' => true 
		);
		if (empty ( $cate )) {
			return $default;
		}
		$tip = '';
		foreach ( $this->dictTypes as $dictType ) {
			if ($dictType ['cate'] == strtoupper ( $cate )) {
				$default ['name'] = '请选择' . $dictType ['name'] . '...';
				break;
			}
		}
		
		return $default;
	}
	public function get_currentUserInfo() {
		$userInfo = $this->loginUser->getInformation ();
		if (! $userInfo ['user_type']) {
			$userInfo ['user_type'] = $this->loginUser->getUserType ();
		}
		$userInfo ['user_key'] = $this->loginUser->getUserKey ();
		$userInfoAll = $this->getUserOtherInfo ( $userInfo );
		$userInfoAll ['is_admin'] = $this->checkIsAdmin ( $userInfo ['user_key'], GROUP_NAME );
		$userModel = D ( 'Users' );
		if (! empty ( $userInfoAll ['sCode'] )) {
			$userInfoAll ['teachsubject'] = $userModel->getTeacherXueKeAndSubject ( $userInfoAll ['sCode'] );
			if (! empty ( $userInfoAll ['teachsubject'] )) {
				$teachSubjectStr = "'" . implode ( "','", explode ( ',', trim ( $userInfoAll ['teachsubject'], ',' ) ) ) . "'";
				$tempResult = $userModel->getTeacherXueBuBySubject ( $teachSubjectStr );
				$userInfoAll ['xuebu'] = $tempResult ['xuebu'];
				$userInfoAll ['xueke'] = $tempResult ['xueke'];
			}
		}
		$userInfoAll ['role'] = $userModel->get_userRoles ( $userInfoAll ['user_key'], GROUP_NAME, APP_NAME );
		return $userInfoAll;
	}
	public function get_roleCondition($condition = array(), $limitType = 0) {
		$userInfo = $this->get_currentUserInfo ();
		if ($limitType == 1) {
			if ($userInfo ['is_admin'] != 1 && strpos ( '000,' . $userInfo ['role'] . ',', ',教研审核员,' ) && ! strpos ( '000,' . $userInfo ['role'] . ',', '教研主管' )) {
				$condition ['xueke'] = "'" . implode ( "','", explode ( ',', $userInfo ['xueke'] ) ) . "'";
				$condition ['xuebu'] = "'" . implode ( "','", explode ( ',', $userInfo ['xuebu'] ) ) . "'";
			}
		} else {
			if ($userInfo ['is_admin'] != 1 && ! strpos ( '000,' . $userInfo ['role'] . ',', ',教研审核员,' ) && ! strpos ( '000,' . $userInfo ['role'] . ',', '教研主管' )) {
				$condition ['xueke'] = "'" . implode ( "','", explode ( ',', $userInfo ['xueke'] ) ) . "'";
				$condition ['xuebu'] = "'" . implode ( "','", explode ( ',', $userInfo ['xuebu'] ) ) . "'";
			}
		}
		return $condition;
	}
}

?>


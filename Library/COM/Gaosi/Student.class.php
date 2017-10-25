<?php
import ( "COM.Auth.UcAuth" );
import ( 'COM.User.User' );
import ( 'COM.Dao.Dao' );
class Student extends User {
	private $studentsInfo = array ();
	/**
	 * 返回用户类型
	 */
	protected function getType() {
		return USER_TYPE_STUDENT;
	}
	
	/**
	 * 学生账号登录
	 */
	protected function _login() {
		if (false == $this->userInfo ['user_pass']) {
			die ( '登录用户信息数组中必须包含“user_name”以及“user_pass”键值' );
		}
		$fStudent = new fStudentModel ();
		$arrResult = $fStudent->getOneStudent ( $this->userInfo ['user_name'] );
		if (isset ( $arrResult [0] ['spassword'] ) && ! empty ( $arrResult [0] ['spassword'] ) && $arrResult [0] ['spassword'] == md5 ( $this->userInfo ['user_pass'] )) {
			$this->userInfo ['student_code'] = $arrResult [0] ['sCode'];
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 更新用户登录信息，登录日志等
	 */
	protected function _updateInfo() {
		$User = new UserSetModel ();
		// 添加或更新用户登录信息
		$User->addUserS ( $this->userInfo ['student_code'] );
	}
	
	/**
	 * 返回学员姓名（学员姓名不是用户名，不能用作登录）
	 */
	protected function getName() {
		$studentsInfo = $this->getInformation ();
		return $studentsInfo ['uname'];
	}
	
	/**
	 * 获取用户详细信息
	 */
	public function getInformation() {
		if (false == $this->studentsInfo) {
			$fStudent = new fStudentModel ();
			$arrResult = $fStudent->getOneStudent ( $this->userInfo ['user_name'] );
			if (! isset ( $arrResult [0] ['spassword'] ) || empty ( $arrResult [0] ['spassword'] ) || $arrResult [0] ['spassword'] != md5 ( $this->userInfo ['user_pass'] )) {
				$this->logout ( C ( 'USER_COOKIE_NAME' ) );
			}
			$this->studentsInfo = array (
					'uid' => $arrResult [0] ['sCode'],
					'uname' => $arrResult [0] ['sName'],
					'is_guardian' => '0',
					'is_student' => '1',
					'guardianInfo' => array (),
					'studentInfo' => $arrResult
			);
		}
		return $this->studentsInfo;
	}
	protected function _findUser($keyword) {
	}
	
	public static function chkStuMobile($stuInfo, $mobile) {
		if(false == $mobile) return true;
		$dao = Dao::getDao();
		$strQuery = 'SELECT count(1) FROM bs_student
					 WHERE bisvalid=1 
					  AND sname =' . $dao->quote($stuInfo['sName']) . '
					  AND scode !=' . $dao->quote($stuInfo['sCode']) . '
					  AND (smobile=' . $dao->quote($mobile) . '
					    OR sphone=' . $dao->quote($mobile) . '
					    OR sparents1phone=' . $dao->quote($mobile) . '
					    OR sparents2phone=' . $dao->quote($mobile) . ')';
		return $dao->getOne($strQuery) == 0;
	}
	
	public static function chkStuEmail($stuInfo, $email) {
		if(false == $email) return true;
		$dao = Dao::getDao();
		$strQuery = 'SELECT count(1) FROM bs_student
					 WHERE bisvalid=1
					   AND sname =' . $dao->quote($stuInfo['sName']) . '
					   AND scode != ' . $dao->quote($stuInfo['sCode']) . '
					   AND semail=' . $dao->quote($email);
		return $dao->getOne($strQuery) == 0;
	}
}
?>
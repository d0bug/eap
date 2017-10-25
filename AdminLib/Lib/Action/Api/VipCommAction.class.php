<?php
abstract class VipCommAction extends AppCommAction{
	protected $autoCheckPerm = false;
	public function __construct() {
		parent::__construct();
	}

	protected  function notNeedLogin() {
		return array();
	}

	/*判断教师身份，是否为教研*/
	public  function checkUserRole(){
		$userKey = $this->loginUser->getUserKey();
		$userInfo = D('Users')->get_userInfo($userKey);
		if($userInfo['is_teaching_and_research'] == 1){
			$is_jiaoyan = 1;
		}else{
			if($this->checkIsAdmin()){
				$is_jiaoyan = 1;
			}else{
				$is_jiaoyan = 0;
			}
		}
		return $is_jiaoyan;
	}

	/*判断教师类型，是否为兼职*/
	public  function checkUserType(){
		$userKey = $this->loginUser->getUserKey();
		$userType = reset(explode('-',$userKey));
		if($userType == 'VTeacher'){
			$is_jianzhi = 1;
		}else{
			$is_jianzhi = 0;
		}
		return $is_jianzhi;
	}

	/*获取客户端IP*/
	public function getClientIp(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){//当客户端使用的是自己的服务器时
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){//当客户端使用的是代理的服务器时
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else if(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];
		}else{
			$cip = '';
		}
		$cips=array();
		preg_match('/[\d\.]{7,15}/', $cip, $cips);
		$cip = isset($cips[0]) ? $cips[0] : 'unknown';
		unset($cips);
		return $cip;
	}

	/*处理文件url*/
	public function deal_file_url($handoutsList){
		foreach ($handoutsList as $key => $handouts){
			$handoutsList[$key]['picture'] = end(explode('eap',str_replace('Upload/','upload/',$handouts['picture'])));
			$handoutsList[$key]['teacher_version'] = end(explode('eap',str_replace('Upload/','upload/',$handouts['teacher_version'])));
			$handoutsList[$key]['student_version'] = end(explode('eap',str_replace('Upload/','upload/',$handouts['student_version'])));
		}
		return $handoutsList;
	}

	/*获取当前用户的科目权限*/
	public function get_thisuser_subjectIdStr($userKey){
		$subjectModel = D('VpSubject');
		$sidStr = $subjectModel->get_thisuser_sidsStr($userKey);
		return $sidStr;
	}

	/*二维数组，按指定键值去重*/
	public function unique_arr($arr,$keyName){
		if(!empty($arr)){
			$keyName_str = '';
			foreach ($arr as $key=>$row){
				if(strpos(','.$keyName_str,','.$row[$keyName].',')!==false){
					unset($arr[$key]);
				}
				$keyName_str .= $row[$keyName].',';
			}
		}
		return $arr;
	}


	/*判断当前登录用户是否为超级管理员*/
	public function checkIsAdmin($userKey='',$groupName=''){
		if(empty($groupName)) $groupName = GROUP_NAME;
		if(!$userKey){
			$userKey = $this->loginUser->getUserKey();
		}
		$is_admin = D('Users')->checkIsAdminer($userKey,$groupName);
		if($is_admin == 1){
			return true;
		}
		if(in_array(end(explode('-',$userKey)),C('SUPER_USERS'))){
			return true;
		}
		return false;
	}

	/*获取权限信息*/
	public function getPermInfo($aclKey){
		$this->getPermInfo($aclKey);
	}

	/*获取讲义/试题表单提交信息*/
	public function getCacheHandoutsInfo(){
		$arr = array();
		if($_GET){
			$arr['title'] = urldecode($_GET['title']);
			$arr['picture'] = str_replace('__','/',$_GET['picture']);
			$arr['picture_show'] = end(explode('eap',str_replace('Upload/','upload/',$arr['picture'])));
			$arr['introduce'] = urldecode($_GET['introduce']);
			if(!empty($_GET['teacher_version'])){
				$arr['teacher_version'] = str_replace('__','/',$_GET['teacher_version'].'.'.$_GET['teacher_version_type']);
				$arr['teacher_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$arr['teacher_version'])));
			}
			if(!empty($_GET['student_version'])){
				$arr['student_version'] = str_replace('__','/',$_GET['student_version'].'.'.$_GET['student_version_type']);
				$arr['student_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$arr['student_version'])));
			}
		}
		return $arr;
	}


	public function download_file($source_url,$toDownloadFile){
		$file = fopen($source_url,"r");
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($source_url));
		Header("Content-Disposition: attachment; filename=".mb_convert_encoding($toDownloadFile,'gb2312','utf8'));
		//header("Content-Type: text/html; charset=gb2312");
		ob_clean();
		flush();
		echo fread($file, filesize($source_url));
		$buffer=10240;//
		while (!feof($file)) {
			$file_data=fread($file,$buffer);
			echo $file_data;
		}
		fclose($file);
	}


	public function get_currentUserInfo(){
		$userInfo = $this->loginUser->getInformation();
		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		$userInfo['user_key'] = $this->loginUser->getUserKey();
		$userInfo['real_name'] = D('Users')->get_userRealName_by_userKey($userInfo['user_key']);
		return  $this->getUserOtherInfo($userInfo);
	}


	/*判断当前登录用户是否模块的超级管理员*/
	public function checkIsRealAdmin($userKey='',$groupName=''){
		if(empty($groupName)) $groupName = GROUP_NAME;
		if(!$userKey){
			$userKey = $this->loginUser->getUserKey();
		}
		$is_admin = D('Users')->checkIsAdminer($userKey,$groupName);
		if($is_admin == 1){
			return true;
		}
		return false;
	}



	public function getUserInfoFull(){
		$userInfo = $this->loginUser->getInformation();
		$userInfo['user_key'] = $this->loginUser->getUserKey();
		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		$eapUserInfo = D('Users')->get_userInfo($userInfo['user_key']);
		if($eapUserInfo){
			$userInfo['real_name'] = $eapUserInfo['user_realname'];
		}
		$userInfo = $this->getUserOtherInfo($userInfo);
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		return $userInfo;
	}



	/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string $str
 *        	要分割的字符串
 * @param string $glue
 *        	分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
	function str2arr($str, $glue = ',') {
		return explode ( $glue, $str );
	}

	/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array $arr
 *        	要连接的数组
 * @param string $glue
 *        	分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
	function arr2str($arr, $glue = ',') {
		return implode ( $glue, $arr );
	}


	/*空格换行符替换*/
	public function textarea_content_to($content){
		return str_replace(" ","&nbsp;",str_replace("\r\n","<br>",$content));
	}

	/*空格换行符替换*/
	public function to_textarea_content($content){
		return str_replace("&nbsp;"," ",str_replace("<br>","\r\n",$content));
	}


	public function object2array(&$object) {
		$object =  json_decode( json_encode( $object),true);
		return  $object;
	}
}

?>

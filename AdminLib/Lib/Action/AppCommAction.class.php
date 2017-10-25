<?php
include(CONF_PATH . '/const.php');
import('COM.User.User');
import('COM.SysUtil');
import('COM.Auth.Permission');
import('COM.Logger.AppLogger');
abstract class AppCommAction extends Action {
	protected $loginUser = null;
	protected $cookieName = '';
	protected $permName = '';
	protected $permValue = 0;
	protected static $init = false;
	protected $autoCheckPerm = true;
	protected $aclKey = '';

	public function __construct() {
		parent::__construct();
		$this->cookieName = C('USER_COOKIE_NAME');
		$this->aclKey = GROUP_NAME . '-' . MODULE_NAME . '-' . ACTION_NAME;
		$actionName = strtoupper($this->aclKey);
		if (false == in_array($actionName, $this->notNeedLogin()) && false == $this->getLoginUser()) {
			$this->redirect(U('/System/User/login', array(), false));
		}
		if(false == self::$init && $this->autoCheckPerm && $this->loginUser) {
			self::$init = true;
			$this->readCheck();
		}
	}

	/**
     * 获取当前登录用户实例
     * @return User
     */
	protected function getLoginUser() {
		if (null  === $this->loginUser) {
			$this->loginUser = User::getLoginUser($this->cookieName);
		}
		return $this->loginUser;
	}

	/**
     * 重载框架的DISPLAY方法， 所有自定义常量赋值为模板变量
     * @param String $templateFile
     * @param String $charset
     * @param String $contentType
     * @param String $content
     * @param String $prefix
     */
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		$allConstants = get_defined_constants(true);
		$userConstants = $allConstants['user'];
		$this->assign($userConstants);
		$time = time();
		$this->assign('_time', $time);
		parent::display($templateFile,$charset,$contentType,$content,$prefix);
	}

	/**
     * 获取当前操作方法的授权信息
     * @return void
     */
	private function getPermInfo($aclKey =''){
		if (false == $this->permValue &&  $this->getLoginUser()) {
			$permInfo = Permission::getPermInfo($this->loginUser, $aclKey);
			$this->permName = $permInfo['permName'];
			$this->permValue = $permInfo['permValue'];
		}
	}

	/**
     * 检查是否有读的权限，当有写权限时默认为有读的权限
     * @param boolean $isAjax
     * @return boolean
     */
	protected function readCheck($aclKey = '') {
		$isAjax = $this->isAjax();
		$this->getPermInfo($aclKey);
		if ($this->permValue >0) return true;
		//todo:dislay
	}

	/**
     * 检查是否具有写的权限
     * @param boolean $isAjax
     * @return boolean
     */
	protected function writeCheck($aclKey = '') {
		$isAjax = $this->isAjax();
		$this->getPermInfo($aclKey);
		if($this->permValue & PERM_WRITE) {
			return true;
		}
		//todo:dislay
	}

	protected function getAclKey($actionName=ACTION_NAME, $moduleName=MODULE_NAME, $groupName=GROUP_NAME) {
		return $groupName . '-' . $moduleName . '-' . $actionName;
	}

	protected function getUrl($actionName=ACTION_NAME, $moduleName=MODULE_NAME, $groupName=GROUP_NAME, $args = array()) {
		$url = U($groupName . '/' . $moduleName . '/'. $actionName, $args, false);
		return $url;
	}

	public function _empty() {
		if(method_exists($this, ACTION_NAME)) {
			$actionName = ACTION_NAME;
			$this->$actionName();
			return;
		}
		die('access denied');
	}

	public function callMethod($methodName, $args) {
		return $this->$methodName($args);
	}

	/**
	 * 获取用户其他信息，如部门、教师类型、授课风格等
	 *
	 * @param array $userInfo
	 * @return array
	 */
	public function getUserOtherInfo($userInfo){
		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		if($userInfo['user_type'] == 'Employee'){
			$TypeArray = $this->loginUser->getTypeArray();//内部员工获取员工类型数组
			$userInfo['department'] = '';
			preg_match_all('/ou=([^,]+),/i', $userInfo['dn'], $department_arr);
			$department_arr = array_reverse(array_unique($department_arr[1]));
			foreach ($department_arr as $key=>$department){
				if($department != 'GS' && $department != 'Person'){
					$userInfo['department'] .= $department."->";
				}
			}
			$userInfo['department'] = trim($userInfo['department'],'->');
		}else{
			$TypeArray = $this->loginUser->getAdminUserTypes();
		}
		$userInfo['user_type'] = $TypeArray[$userInfo['user_type']];
		$userInfo['user_key'] = $this->loginUser->getUserKey();
		$userInfo['sCode'] = D('Users')->get_teacherCode($userInfo);
		return $userInfo;
	}


	/*生成缩略图*/
	public function thumb_img($targetFile,$maxwidth,$maxheight,$autocut=0,$imgtype){
		list($imgWidth,$imgHeight) = getimagesize($targetFile);
		if(($maxwidth && $imgWidth > $maxwidth) || ($maxheight && $imgHeight > $maxheight) || $autocut){
			//			//以下创建缩略图-----------------------
			//			if($autocut==1){
			//				$newwidth = $maxwidth;
			//				$newheight = $maxheight;
			//			}else{
			//				if($maxwidth && $imgWidth > $maxwidth){
			//					$widthratio = $maxwidth/$imgWidth;
			//					$RESIZEWIDTH=true;
			//				}
			//				if($maxheight && $imgHeight > $maxheight){
			//					$heightratio = $maxheight/$imgHeight;
			//					$RESIZEHEIGHT=true;
			//				}
			//				if($RESIZEWIDTH && $RESIZEHEIGHT){
			//					if($widthratio < $heightratio){
			//						$ratio = $widthratio;
			//					}else{
			//						$ratio = $heightratio;
			//					}
			//				}elseif($RESIZEWIDTH){
			//					$ratio = $widthratio;
			//				}elseif($RESIZEHEIGHT){
			//					$ratio = $heightratio;
			//				}
			//				$newwidth = $imgWidth * $ratio;
			//				$newheight = $imgHeight * $ratio;
			//			}
			$creat_arr = $this->getpercent($imgWidth,$imgHeight,$maxwidth,$maxheight);
			$createwidth = $newwidth = $creat_arr['w'];
			$createheight = $newheight = $creat_arr['h'];
			$psrc_x = $psrc_y = 0;
			if($autocut && $maxwidth > 0 && $maxheight > 0) {
				if($maxwidth/$maxheight<$imgWidth/$imgHeight && $maxheight>=$newheight) {
					$newwidth = $maxheight/$newheight*$newwidth;
					$newheight = $maxheight;
					if ($autocut == 1) {
						$psrc_x = $newwidth/2-$maxwidth/2;
						$psrc_y = 0;
					}
				}elseif($maxwidth/$maxheight>$imgWidth/$imgHeight && $maxwidth>=$newwidth) {
					$newheight = $maxwidth/$newwidth*$newheight;
					$newwidth = $maxwidth;
					if ($autocut == 1) {
						$psrc_x = 0;
						$psrc_y = $newheight/2-$maxheight/2;
					}
				}
				$createwidth = $maxwidth;
				$createheight = $maxheight;
			}
			$thumb_file = $targetFile;//缩略图名字和路径
			//$thumb = imagecreatetruecolor($newwidth, $newheight);
			if($ext != '.gif' && function_exists('imagecreatetruecolor'))
			$thumb = imagecreatetruecolor($createwidth, $createheight);
			else
			$thumb = imagecreate($newwidth, $newheight);
			$ext=strtolower(strrchr($thumb_file,"."));
			if($ext == '.jpg' || $ext == '.jpeg') {
				$img=imagecreatefromjpeg($targetFile);
			}else if ($ext == '.png') {
				$img=imagecreatefrompng($targetFile);
			}else if($ext == '.gif') {
				$img=imagecreatefromgif($targetFile);
			}
			if(function_exists('imagecopyresampled'))
			ImageCopyResampled($thumb,$img,0,0,$psrc_x,$psrc_y,$newwidth,$newheight,$imgWidth,$imgHeight);
			else
			ImageCopyResized($thumb,$img,0,0,$psrc_x,$psrc_y,$newwidth,$newheight,$imgWidth,$imgHeight);

			if($ext=='.gif' || $ext=='.png') {
				$background_color  =  imagecolorallocate($thumb,  0, 255, 0);  //  指派一个绿色
				imagecolortransparent($thumb, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
			}
			if($ext=='.jpg' || $ext=='.jpeg') imageinterlace($thumb, 0);
			if($ext == '.jpg' || $ext == '.jpeg') {
				imagejpeg($thumb,$thumb_file);
			}else if ($ext == '.png') {
				imagepng($thumb,$thumb_file);
			}else if($ext == '.gif') {
				imagegif($thumb,$thumb_file,100);
			}
			return $thumb_file;
		}else{
			return $targetFile;
		}
	}

	//img_resize
	public function img_resize( $file, $maxwidth,$maxheight ){
		$tempFile = $file;
		$gis        = getimagesize($tmpname);
		$imgWidth = $gis[1];
		$imgHeight = $gis[0];
		$type        = $gis[2];
		switch($type)
		{
			case "1": $imorig = imagecreatefromgif($file); break;
			case "2": $imorig = imagecreatefromjpeg($file);break;
			case "3": $imorig = imagecreatefrompng($file); break;
			default:  $imorig = imagecreatefromjpeg($file);
		}


		$aw = $maxwidth;
		$ah = $maxheight;

		$im = imagecreatetruecolor($aw,$ah);
		if (imagecopyresampled($im,$imorig , 0,0,0,0,$aw,$ah,$imgWidth,$imgHeight))
		if (imagejpeg($im, $tempFile))
		return $tempFile;
		else
		return $file;
	}

	public function getpercent($srcwidth,$srcheight,$dstw,$dsth) {
		if (empty($srcwidth) || empty($srcheight) || ($srcwidth <= $dstW && $srcheight <= $dstH)) $w = $srcwidth ;$h = $srcheight;
		if ((empty($dstw) || $dstw == 0)  && $dsth > 0 && $srcheight > $dsth) {
			$h = $dsth;
			$w = round($dsth / $srcheight * $srcwidth);
		} elseif ((empty($dsth) || $dsth == 0) && $dstw > 0 && $srcwidth > $dstw) {
			$w = $dstw;
			$h = round($dstw / $srcwidth * $srcheight);
		} elseif ($dstw > 0 && $dsth > 0) {
			if (($srcwidth / $dstw) < ($srcheight / $dsth)) {
				$w = round($dsth / $srcheight * $srcwidth);
				$h = $dsth;
			} elseif (($srcwidth / $dstw) > ($srcheight / $dsth)) {
				$w = $dstw;
				$h = round($dstw / $srcwidth * $srcheight );
			} else {
				$w = $dstw;
				$h = $dsth;
			}
		}
		$array['w']  = $w;
		$array['h']  = $h;
		return $array;
	}


	/*二维数组去重*/
	public function array_unique_2D($arr){
		foreach ($arr as $v){
			$v = join(",",$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[] = $v;
		}
		$temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
		foreach ($temp as $k => $v){
			$temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
		}
		return $temp;
	}

	/*二维数组排序*/
	public function array_sort($arr,$keys,$type='asc'){
		$keysvalue = $new_array = array();
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = mb_convert_encoding($v[$keys],'gbk','utf-8');
		}
		if($type == 'asc'){
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}

		reset($keysvalue);
		foreach ($keysvalue as $k=>$v){
			$new_array[] = $arr[$k];
		}
		return $new_array;
	}


	/*判断当前登录用户是否为超级管理员*/
	public function checkIsAdmin($userKey,$groupName){
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


	public function getRandChar($length){
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;

		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	}



	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	abstract protected function notNeedLogin();
};
?>


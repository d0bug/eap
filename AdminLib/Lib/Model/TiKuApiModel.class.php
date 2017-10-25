<?php
class TiKuApiModel extends Model{
	public $dao = null;
	public function __construct() {
		$this->dao = Dao::getDao ();
		if (class_exists ( 'User', false )) {
			$operator = User::getLoginUser ();
			if ($operator)
				$this->userKey = $operator->getUserKey ();
		}
	}
	public function linkTiApi($fun,$param = '',$userAnen = true){
		$tiUrl = C('TiKu');
		$apiUrl = $tiUrl.$fun;
		if (is_array($param)){
			$Parameters = '';
			foreach ($param as $k=>$v){
				$Parameters .= '/'.$k.'/'.$v;
			}
			$url = $apiUrl.$Parameters;
		}else{
			$url = $apiUrl.'/'.$param;
		}
        if($fun == 'ques') {
            $url .= '/1';
        }
		return $this->cUrlGet($url,$userAnen);
	}
	private function cUrlGet($url,$userAnent=false){
		$ch = curl_init();
		if ($userAnent){
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
<?php
abstract class VipschoolCommAction extends AppCommAction{
	protected $autoCheckPerm = false;
	public function __construct() {
		parent::__construct();
	}

	protected  function notNeedLogin() {
		return array();
	}
	
	public function textarea_content_to($content){
		return str_replace(" ","&nbsp;",str_replace("\r\n","<br>",$content));
	}


	public function to_textarea_content($content){
		return str_replace("&nbsp;"," ",str_replace("<br>","\r\n",$content));
	}

}

?>

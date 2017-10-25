<?php
/*教师列表*/
class ModularIndexAction extends ModularCommAction{



	public function main(){

		$this->assign(get_defined_vars());
		$this->display();
	}


}

?>

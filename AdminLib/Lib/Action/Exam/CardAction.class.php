<?php
/**
 * 激活卡组模块
 *
 */
class CardAction extends ExamCommAction {
	
	/**
	 * 卡组主操作控制台
	 *
	 */
	public function main(){
		$permValue = $this->permValue;

		$jsonPageCardGroup = $this->getUrl('jsonPageCardGroup');
		$addUrl = $this->getUrl('add');
		$showUrl = $this->getUrl('showCardGroup');
		$listCodeUrl = $this->getUrl('listCode');
		$delCardUrl = $this->getUrl('delCardGroup');
		
		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 添加卡组
	 *
	 */
	protected function add(){
		$this->writeCheck($this->getAclKey('main'));
		$url = $_SERVER['REQUEST_URI'];

		$addUrl = $this->getUrl('add');

		if($this->isPost()) {

			$resultScript = true;
			$cardModel = D('Card');
			$cardGroupInfo = $_POST;
			$rs = $cardModel->saveCardGroup($cardGroupInfo);

		}
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	/**
	 * 编辑卡组信息
	 *
	 */
	protected function editCardGroup(){
		$this->writeCheck($this->getAclKey('main'));
		
		$rs = D('Card')->saveCardGroup($this->_post());
		outPut($rs);
	}
	
	/**
	 * 删除卡组信息
	 *
	 */
	protected function delCardGroup(){
		$this->writeCheck($this->getAclKey('main'));
		
		$rs = D('Card')->delCardGroup($this->_post());
		outPut($rs);
	}
	
	/**
	 * 卡组信息分页json数据源
	 *
	 */
	protected function jsonPageCardGroup(){
		$this->readCheck($this->getAclKey('main'));

		$cardModel = D('Card');
		$para = $this->_post();
		$page = $cardModel->pageCardGroup($para);

		outPut($page);
	}
	
	/**
	 * 显示卡组信息
	 *
	 */
	protected function showCardGroup(){
		$this->readCheck($this->getAclKey('main'));
		$gid = abs($this->_get('gid'));
		$model = D('Card');

		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('main'));
		//卡组信息
		$cardGroup = $model->getCardGroupByGid($gid);
		//生成卡号日志记录
		$logList = $model->listCodeLogByGid($gid);


		$this->assign($permInfo);
		$this->assign($cardGroup);
		$this->assign('logList', $logList);
		$this->assign('appendCodeUrl', $this->getUrl('appendCode'));

		if($permInfo['permValue'] & PERM_WRITE){
			$this->assign('exportUrl', $this->getUrl('export'));
			$this->assign('editCardUrl', $this->getUrl('editCardGroup'));

			$this->display('editCardGroup');
		}else{
			$this->display();
		}

	}
	
	/**
	 * 激活卡号列表
	 *
	 */
	protected function listCode(){
		$this->readCheck($this->getAclKey('main'));
		
		$gid = abs($this->_get('gid'));
		$this->assign('jsonPageCodeUrl', $this->getUrl('jsonPageCode') . '?gid=' . $gid);
		$this->display();
	}

	/**
	 * 激活卡号列表
	 */
	protected function jsonPageCode(){
		$this->readCheck($this->getAclKey('main'));

		$cardModel = D('Card');
		$para = $this->_post();
		$para['gid'] = abs($this->_get('gid'));
		$page = $cardModel->pageCode($para);

		outPut($page);
	}
	
	/**
	 * 追加卡号
	 */
	protected function appendCode(){
		$this->writeCheck($this->getAclKey('main'));
		$rs = D('Card')->appendCode($this->_post());
		outPut($rs);
	}
	
	/**
	 * 导出卡号
	 */
	protected function export() {
		$this->readCheck($this->getAclKey('main'));
		
		$data = D('Card')->exportFile($this->_get());
		$filename = $data['fileName'];
		$filename = mb_convert_encoding($filename, 'cp936', 'utf-8');
				
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=$filename.xls ");
		header("Content-Transfer-Encoding: binary ");
		
		echo $data['content'];
	}
}
?>
<?php
/*新闻资讯*/
class VipNewsAction extends VipCommAction{
	/*资讯列表*/
	public function index(){
		$articleType = C('ARTICLE_TYPE');
		$vipnewsModel = D('VpNews');
		$detail_news = array();
		$ntype = isset($_GET['ntype'])?SysUtil::safeString($_GET['ntype']):'';
		$nid = isset($_GET['nid'])?intval($_GET['nid']):0;
		if(!empty($nid)){
			$detail_news = $vipnewsModel->get_newsInfo_by_nid($nid);
			$detail_news['instime'] = date('Y-m-d H:i:s',$detail_news['instime']);
		}
		import("ORG.Util.String");
		$string = new String();
		if(!empty($articleType)){
			foreach ($articleType as $key=>$type){
				$newslist[$key] = $vipnewsModel->get_newsList_menu($type);
				if(!empty($newslist[$key])){
					foreach ($newslist[$key] as $kk=>$new){
						$newslist[$key][$kk]['instime'] = date('Y-m-d',$new['instime']);
						$newslist[$key][$kk]['title'] = !empty($ntype)?$string->msubstr($new['title'],0,20,'utf-8'):$new['title'];
					}
				}
				if(!empty($newslist[$key]) && empty($detail_news)){
					$detail_news = $newslist[$key][0];
				}
			}
		}
		if(!empty($ntype)){
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$detail_newsList = $vipnewsModel->get_newsList("[ntype] = '".$articleType[$ntype]."'",$curPage,$pagesize);
			if(!empty($detail_newsList)){
				foreach ($detail_newsList as $key => $new){
					$detail_newsList[$key]['instime'] = date('Y-m-d',$new['instime']);
					$detail_newsList[$key]['title'] = $string->msubstr($new['title'],0,80,'utf-8');
				}
			}
			$count = $vipnewsModel->get_newsCount("[ntype] = '".$articleType[$ntype]."'");
			$page = new page($count,$pagesize);
			$showPage = $page->show();
		}
		if(!empty($detail_news)){
			$detail_news['ncontent'] = stripslashes($detail_news['ncontent']);
		}
		$userInfo = $this->loginUser->getInformation();
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*资讯上传*/
	public function add_news(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$userInfo = $this->loginUser->getInformation();
		$is_jiaoyan = $this->checkUserRole();
		//if($is_jiaoyan!=1){
		//	echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		//}
		$articleType = C('ARTICLE_TYPE');
		if($_POST ){
			if(!empty($_POST['ntype']) && !empty($_POST['title']) && !empty($_POST['ncontent'])){
				$vipnewsModel = D('VpNews');
				$dao = $vipnewsModel->dao;
				if($vipnewsModel->get_newsCount(' [title] = '.$dao->quote($_POST[title]))){
					$this->error('该资讯标题已存在，资讯添加失败',$return_url);
				}
				$result = $vipnewsModel->add_news(array('user_name'=>$userInfo['real_name'],'ntype'=>SysUtil::safeString($_POST['ntype']),'title'=>SysUtil::safeString($_POST['title']),'ncontent'=>addslashes(stripslashes($_POST['ncontent']))),$this->loginUser->getUserKey());
				if($_POST['save_close']){
					$return_url = U('/Vip/VipNews/index');
				}else{
					$return_url = U('/Vip/VipNews/add_news');
				}
				if($result == true){
					$this->success('资讯添加成功',$return_url);
				}else{
					$this->error('资讯添加失败',$return_url);
				}
			}else{
				$this->error('请填写完整的资讯信息',$return_url);
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}

	
	/*资讯删除*/
	public function delete_news(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$vipnewsModel = D('VpNews');
		$condition = '';
		if(!empty($_GET['keyword'])){
			$dao = $vipnewsModel->dao;
			$condition .= ' title LIKE '.$dao->quote('%' . SysUtil::safeString(urldecode($_GET['keyword'])) . '%');
		}
		$keyword = $_GET['keyword'];
		$newsList = $vipnewsModel->get_newsList($condition,$curPage,$pagesize);
		if(!empty($newsList)){
			import("ORG.Util.String");
			$string = new String();
			foreach ($newsList as $key => $new){
				$newsList[$key]['instime'] = date('Y-m-d H:i:s',$new['instime']);
			}
		}
		$count = $vipnewsModel->get_newsCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		if($_POST['delete']){
			$is_delete = isset($_POST['is_delete'])?$_POST['is_delete']:'';
			$delete_newsId = "'".implode("','",$is_delete)."'";
			$vipnewsModel = D('VpNews');
			$returnUrl = U('/Vip/VipNews/delete_news',array('p'=>$curPage,'keyword'=>urldecode($_GET['keyword'])));
			if($vipnewsModel->delete_news($delete_newsId)){
				$this->success('资讯删除成功',$returnUrl);
			}else{
				$this->error('资讯删除失败',$returnUrl);
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}

}

?>
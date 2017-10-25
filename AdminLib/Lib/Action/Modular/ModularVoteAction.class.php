<?php
class ModularVoteAction extends ModularCommAction {
	protected function notNeedLogin() {
		return array('MODULAR-MODULARVOTE-SHOW','MODULAR-MODULARVOTE-VOTEADD');
	}
	public function main(){
		$model = D('Vote');
		$rs = $model->getList();
		$list = $rs['list'];
		$this->assign('list',$list);
		$this->display('main');
	}
	public function add(){
		if (empty($_POST)){
			$this->display();
		}else{
			$model = D('Vote');
			$addid = $model->add($_POST);
			$rs = $model->getList($addid);
			$list = $rs['list'];
			$this->assign('list',$list);
			$this->display('rel');
		}
	}
	public function edit(){
		$model = D('Vote');
		if (!empty($_GET['id'])){
			$rs = $model->getList($_GET['id'],true);
			$list = $rs['list'];
			$info = $rs['info'];
			foreach ($list as $key=>$one){
				$option = $model->getOption($one['listid']);
				
				for ($i=0;$i<count($option);$i++){
					if ($i==0){
						$o = $option[$i]['title'];
						$k = $option[$i]['oid'];
					}else{
						$o .= ','.$option[$i]['title'];
						$k .= ','.$option[$i]['oid'];
					}
				}
				$list[$key]['option'] = $o;
				$list[$key]['optionid'] = $k;
			}
			$this->assign('info',$info);
			$this->assign('list',$list);
			$this->display('edit');
		}elseif(!empty($_POST)){
			$rel = $model->edit($_POST);
			echo "<pre>";
			print_r($rel);
			$this->display('main');
		}else{
			$this->display('main');
		}
	}
	
	public function show($display = 0){
		$voteid = empty($_GET['voteid']) ? 0 : intval($_GET['voteid']);
		if ($voteid > 0){
			$model = D('Vote');
			$rs = $model->getList($voteid);
			$list = $rs['list'];
			$html = '<script src="http://img.gaosiedu.com/www/js/jquery.js"></script><script src="http://eap.local/static/js/vote.js"></script>';
			$html .= '<div class="modSurvey"><div class="modSurvey_form"><div class="modSurvey_hd">共8374人参与</div>';
			$i = 0;
			$ii = 1;
			foreach ($list as $one){
				$html .='<input type="hidden" id="voteid'.$i.'" value="'.$one['listid'].'"><div class="modSurvey_item mi'.$i.'"><div class="modSurvey_item_hd">'.$ii.'、'.$one['title'].'</div><ul class="modSurvey_box">';
				$type = $one['type']==1 ? 'radio' : 'checkbox';
				$op = $model->getoption($one['listid']);
				$m = 0;
				foreach ($op as $v){
					$html .='<li class="modSurvey_row mr'.$m.'"><div class="modSurvey_op"><label><input type="'.$type.'" name="modular_vote'.$i.'" value="'.$v['oid'].'" />'.$v['title'].'</label></div><div class="modSurvey_result" id=""><span class="bar"><em class="sbar" style="width:auto;"></em></span><span class="ct">25%</span><span class="per">245票</span></div></li>';
					$m++;
				}
				$html .= '</ul></div>';
				$i++;
				$ii++;
			}
			$sub = $display==1 ?'':'<input type="button" class="modSurvey_btn" value="提交" id="sub" />';
			$html .= '<div class="modSurvey_submit"><input type="hidden" value="'.$i.'" id="len"/><input type="hidden" value="'.$list[0]['infoid'].'" id="infoid"/>'.$sub.'<div class="modSurvey_view"><a href="#">查看投票结果</a></div></div></div></div>';
			if ($display == 1){
				$html .= '<textarea cols="80" rows="2" id="biao1">http://eap.local/modular/modular_vote/show/voteid/17</textarea><input type="button" onClick="copyUrl2()" value="点击复制代码" />';
				echo $html;
			}else{
				echo "document.write('".$html."')";
			}
		}
	}
	public function voteadd(){
		$arr = $_GET['_post'];
		$infoid = $_GET['_infoid'];
		if (!empty($arr) && !empty($infoid)){
			$model = D('Vote');
			$message = $model->voteadd($arr,$infoid);
		}else{
			$message = '提交失败'; 
		}
		$result=json_encode($message);
		$callback=$_GET['callback'];
		echo $callback."($result)";
	}
}
?>
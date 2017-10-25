<?php
/**在线预约，报名*/
class ModularApplyAction extends ModularCommAction{
	protected function notNeedLogin() {
		return array('MODULAR-MODULARAPPLY-CREATE_JSFORM','MODULAR-MODULARAPPLY-AJAXSAVEFORMDATA','MODULAR-MODULARAPPLY-AJAX_ATTR_RELATION');
	}

	public function main(){
		$moduleForm = D('ModelForm');
		$moduleList = $moduleForm->get_moduleList();
		$moduleCount = count($moduleList);
		foreach ($moduleList as $key =>$module){
			$moduleList[$key]['used_num'] = $moduleForm->getUsedCount($module['id']);
		}
		$channelArr = C('CHANNEL');
		$this->assign(get_defined_vars());
		$this->display();
	}


	protected function preview(){
		$mid = isset($_GET['mid'])?$_GET['mid']:'';
		$keyword = isset($_GET['keyword'])?SysUtil::safeString($_GET['keyword']):'';
		if(empty($mid)){
			$this->error('非法操作');
		}
		$sexArr = C('SEX');
		$gradeArr = C('GRADES');
		$subjectArr = C('SUBJECTS');
		$moduleForm = D('ModelForm');
		$dao = $moduleForm->dao;
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = " mid = '$mid' ";
		if(!empty($keyword)){
			$condition .= ' AND CONCAT(`id`,`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`) LIKE '.$dao->quote('%' . SysUtil::safeSearch($keyword) . '%');
		}
		/****获取场次属性名称**********/
		$Attrname = $moduleForm->get_attrname($mid);
		
		$dataList = $moduleForm->get_dataList($condition,$curPage,$pagesize);

		$count = $moduleForm->get_dataCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$mname = $moduleForm->get_mname_by_mid($mid);
		
		$this->assign(get_defined_vars());
		$this->display();

	}


	protected  function export_excel(){
		$mid = isset($_GET['mid'])?$_GET['mid']:'';
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
		if(!empty($mid)){
			$moduleForm = D('ModelForm');
			$condition = " mid = '$mid' ";
			if(!empty($keyword)){
				$condition .= " AND CONCAT(`id`,`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`) LIKE '%$keyword%'";
			}
			$dataList = $moduleForm->get_dataListAll($condition);
			/***获取场次属性名称***/
			$Attrname = $moduleForm->get_attrname($mid);
			import("ORG.Util.Excel");
			$exceler = new Excel_Export();
			$exceler->setFileName(time().'.csv');
			$excel_title = array('姓名', '性别', '学校','年级','学科','Email','手机号','留言','报名/预约时间');
			if(!empty($Attrname)){
				foreach ($Attrname as $k=>$v){
					array_push($excel_title,$v['title2']);
				}
			}
			foreach ($excel_title as $key=>$title){
				$excel_title[$key] = mb_convert_encoding($title,'gb2312','utf8');
			}

			$exceler->setTitle($excel_title);
			$sexArr = C('SEX');
			$gradeArr = C('GRADES');
			$subjectArr = C('SUBJECTS');

			// 设置excel内容
			foreach($dataList as $key=>$val){
				$tmp_data = array(mb_convert_encoding($val['name'],'gb2312','utf8'),
				mb_convert_encoding($sexArr[$val['sex']],'gb2312','utf8'),
				mb_convert_encoding($val['school'],'gb2312','utf8'),
				mb_convert_encoding($gradeArr[$val['grade']],'gb2312','utf8'),
				mb_convert_encoding($subjectArr[$val['dept']],'gb2312','utf8'),
				mb_convert_encoding($val['email'],'gb2312','utf8'),
				mb_convert_encoding($val['phone'],'gb2312','utf8'),
				mb_convert_encoding(str_replace("<br>","\r\n",$val['message']),'gb2312','utf8'),
				!empty($val['instime'])?date('Y-m-d H:i:s',$val['instime']):'');
				if(!empty($val['attrname'])){
					$atrr = unserialize($val['attrname']);
					$attrname = explode('_',$atrr);
					foreach($attrname as $k =>$v){
						array_push($tmp_data,mb_convert_encoding($v,'gb2312','utf8'));
					}
				}
				$exceler->addRow($tmp_data);
			}
			

			// 生成excel
			$exceler->export();
		}else{
			$this->error('非法操作');
		}
	}


	public function step_one(){
		$mid = isset($_GET['mid'])?intval($_GET['mid']):'';
		$moduleForm = D('ModelForm');
		$moduleInfo = $moduleForm->get_moduleInfo_by_mid($mid);
		$moduleFormInfo = $moduleForm->get_moduleFormInfo_by_mid($mid);
		foreach ($moduleFormInfo as $key=>$form){
			if(!empty($form['remark'])){
				$moduleFormInfo[$key]['remarkcount'] = count(json_decode($form['remark']));
			}
		}

		$channelArr = C('CHANNEL');
		$gradeArr =  C('GRADES');
		$subjectArr = C('SUBJECTS');
		$this->assign(get_defined_vars());
		$this->display();
	}

	protected  function savadata_step_one(){
		$mid = isset($_REQUEST['mid'])?intval($_REQUEST['mid']):'';
		if(!empty($mid)) $_SESSION['MID'] = $mid;
		$modelname = isset($_POST['modulename'])?SysUtil::safeString($_POST['modulename']):'';
		$channel = isset($_POST['channel'])?intval($_POST['channel']):0;
		$titleType = explode('-',trim($_POST['title1_str'],'-'));
		$title = explode('-',trim($_POST['title2_str'],'-'));
		$display = explode('-',trim($_POST['display_str'],'-'));
		$required = explode('-',trim($_POST['required_str'],'-'));
		$type = explode('-',trim($_POST['type_str'],'-'));
		$remark = json_encode(explode('-',trim($_POST['remark_str'],'-')));
		$remark1 = json_encode(explode('-',trim($_POST['remark1_str'],'-')));
		$moduleForm = D('ModelForm');
		$thisModelFormCount = 0;
		if($_SESSION['MID']){
			$thisModelFormCount = $moduleForm->getModuleCount($_SESSION['MID']);
		}
		$result = $moduleForm->update_moduleInfo($modelname,$channel,$_SESSION['MID']);

		if($thisModelFormCount>0){
			$operate = 'UPDATE';
		}else{
			$operate = 'INSERT';
		}
		$count = count($titleType);
		if(!empty($titleType) && count($title) ==$count && count($display)==$count && count($required)==$count){
			$arrModuleForm = array();
			$result3 = 0;
			foreach ($titleType as $key =>$val){
				$arrModuleForm = array('mid'=>$_SESSION['MID'],'titleid'=>$key+1,'title2'=>$title[$key],'display'=>$display[$key],'required'=>$required[$key],'cate'=>$type[$key]);
				switch($key){
					case 3:
						$arrModuleForm = array_merge($arrModuleForm,array('remark'=>$remark));
						break;
					case 4:
						$arrModuleForm = array_merge($arrModuleForm,array('remark'=>$remark1));
						break;
				}

				$result2 = $moduleForm->save_moduleForm($operate, $arrModuleForm);
				if($result2){
					$result3 += 1;
				}
			}
		}

		if($_SESSION['MID'] && !empty($result3)){
			$data['status'] = $_SESSION['MID'];
			$data['url'] = U('/Modular/ModularApply/step_two',array('mid'=>$_SESSION['MID']));
		}else{
			$data['status'] = 0;
		}
		echo json_encode($data);
	}

	public function step_two(){
		$mid = isset($_GET['mid'])?intval($_GET['mid']):0;
		$moduleForm = D('ModelForm');
		$moduleInfo = $moduleForm->get_moduleInfo_by_mid($mid);
		if(!empty($moduleInfo['limited'])){
			$moduleInfo['start'] = reset(explode(',',$moduleInfo['limited']));
			$moduleInfo['end'] = end(explode(',',$moduleInfo['limited']));
		}

		//获取当前模块需求中要显示的项
		$itemArr = C('ITEM');
		$displayItem = $moduleForm->get_displayItem($mid);
		foreach ($displayItem as $key=>$item){
			$displayItem[$key]['title'] = $itemArr[$item['titleid']];
		}
		$markedWords = C('MARKED_WORDS');
		$replaceArr = C('REPLACES');
		$this->assign(get_defined_vars());
		$this->display();
	}

	protected  function savedata_step_two(){
		$mid = isset($_POST['mid'])?intval($_POST['mid']):'';
		if(!empty($mid)){
			$arr = array();
			$arr['display'] = isset($_POST['display'])?intval($_POST['display']):'';
			$arr['words'] = isset($_POST['persontext'])?SysUtil::safeString($_POST['persontext']):'';
			$arr['limitshow'] = isset($_POST['limited'])?intval($_POST['limited']):'';
			$arr['limited'] = isset($_POST['start']) || isset($_POST['end'])?intval($_POST['start']).','.intval($_POST['end']):'';
			$arr['isgoldlimit'] = isset($_POST['isgoldlimit'])?intval($_POST['isgoldlimit']):0;
			$arr['message'] = isset($_POST['message'])?intval($_POST['message']):'';
			$arr['messagetext'] = isset($_POST['mess'])?SysUtil::safeString($_POST['mess']):'';
			if($display !== '' && $limited !== '' && $message !== ''){
				$moduleForm = D('ModelForm');
				if($moduleForm->save_moduleInfo_step2($arr, $mid)){
					redirect(U('Modular/ModularApply/step_three',array('mid'=>$mid)));
				}else{
					echo json_encode(array('result'=>0));
				}
			}else{
				echo json_encode(array('result'=>0));
			}
		}else{
			echo json_encode(array('result'=>0));
		}
	}
	/****第三步 ***设置场次属性******/
	public function step_three(){
		$this_step = 3;
		$mid = isset($_GET['mid'])?intval($_GET['mid']):'';
		if(empty($mid)){
			$this->error('非法操作');
		}
		$moduleForm = D('ModelForm');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	/**保存场次属性添加的数据***/
	protected function savadata_step_three(){
		$data = array();
		$attrname = isset($_POST['attrname']) ?$_POST['attrname']:$_GET['attrname'];//属性名称
		$data_name = isset($_POST['data_name']) ? $_POST['data_name']:$_GET['data_name'];//数据
		$limitnum = isset($_POST['limitnum']) ? $_POST['limitnum']:$_GET['limitnum'];//限制人数
		$mid = isset($_POST['mid'])?$_POST['mid']:'';
		$moduleForm = D('ModelForm');
		if(empty($_POST['attrname'][0])){$this->error('属性 名称不能为空！请重新输入');}
		if(!empty($_POST['attrname']) && !empty($mid)){
		$upload_files = array();
		$upload_files =	array_filter($_FILES['namelist']['name']);	
			/****y读取上传的数据********/
			if(!empty($upload_files)){
	 			foreach($_FILES as $file){
		         	import("ORG.Net.UploadFile");
		          	$upload = new UploadFile(); // 实例化上传类 
		         	$upload->maxSize  = 3145728 ; // 讴置附件上传大小 
		            $upload->allowExts = array('xls','xlsx'); // 讴置附件上传类型 
		            $folder = date('Y-m-d');
		            if(!file_exists(UPLOAD_PATH));
		            	mkdir(UPLOAD_PATH,0777);
					$upload->savePath = UPLOAD_PATH.$folder.'/';
		           if(!file_exists($upload->savePath)){
		            	@mkdir($upload->savePath,0777);
		            }     
		            if(!$upload->upload()) {// 上传错诣 提示错诣信息 
		               $this->error($upload->getErrorMsg()); 
		            }else{ // 上传成功 获叏上传文件信息 
		                $infos =  $upload->getUploadFileInfo(); 
		            }
	 			}//循环结束
	 			
	 		/**从EXCEL中读取出来的数据******/	
			foreach ($infos as $k=>$info){
				$resurl = $info['savepath'].$info['savename'];
				$arr[]    =    $moduleForm->read($resurl);
			}
			/***把EXCEL读取出来的数据重新排列组合*******/
			foreach($arr as $k=>$v) {
				if (empty($v[0][0])) continue;
			    $data['attrname'][$k] = $v[0][0];
			    foreach($v as $i=>$x) {
			        if ($i == 0) continue;
			        $data['data_name'][$k][] = trim($x[0]);
			        $data['limitnum'][$k][] = trim($x[1]);
			    }
			}
			$data['data_name'] = array_filter($data['data_name']);	
			$data['limitnum'] = array_filter($data['limitnum']);				
	 	}//判断上传文件结束
		
	$add_data= array();
	$newdata_name = array();
	$newdata_limit = array();
	/**判如果文件上传则首先读取上传文件，否则读取添加文件*****/
	if(!empty($data['data_name'])) 
		$newdata_name = $data['data_name'];
	else 
		$newdata_name = $data_name;
		
	if(!empty($data['limitnum']))	
		$newdata_limitnum=$data['limitnum'];
	else 
		$newdata_limitnum=$limitnum;
			
	/***过滤掉空数组************/
	$newdata_name = array_filter($newdata_name);
	$newdata_limitnum = array_filter($newdata_limitnum);
	//print_r($newdata_name);echo 'aa';print_r($newdata_limitnum);exit;
	/***经过排列组合成插入到对应的数据库**********/
	foreach($attrname as $k=>$attr) {
	 	$fid = $moduleForm->add_model_forms($titleid,$attr,$mid);
	     foreach($newdata_name[$k] as $i=>$d) {
	     	$result = $moduleForm->add_model_attributes($fid,$d,$newdata_limitnum[$k][$i],$mid);
	     	if(!$result){
                     $rerror  =  $rerror +1;    
                 }
	     }
	}
		if ($rerror > 0)//设置失败
			echo json_encode(array('result'=>0));
	     else
	      	redirect(U('Modular/ModularApply/relations',array('mid'=>$mid)));
			//echo "<script type='text/javascript'> alert('保存成功！转向属性关系表');window.location.href=\"".U('Modular/ModularCour/relations')."\";</script>";                  
	}else 
	$this->error('没有上传标题名称！');//判断是否添加标题名称
}
	
	
	/**设置对应关系表**/
	protected function relations(){
		$this_step = 3;
		$mid = isset($_REQUEST['mid'])?$_REQUEST['mid']:0;
		if($mid==0) $mid = $_SESSION['MID'];

		if(empty($mid)){
			$this->error('非法操作');
		}
		$moduleForm = D('ModelForm');
		$attrlist = $moduleForm->get_AttriList($mid);
		$this->assign(get_defined_vars());
		$this->display();	
	}	
	
	/***保存场次属性对应关系表**********/
	protected function saverelations(){
		$title1 = isset($_POST['title1'])? $_POST['title1'] : $_GET['title1'];
		$display = isset($_POST['display'])? $_POST['display'] : $_GET['display'];
		$moduleForm = D('ModelForm');
		$mid = isset($_POST['mid'])?$_POST['mid']:0;
		//print_r($_POST);exit;
		foreach($title1 as $k=>$main_id) {
		    foreach($display as $fid=>$arr) {
		        $datanames = $arr[$k];
		        foreach($datanames as $data_id) {
		        	$result = $moduleForm->add_model_attr_relations($main_id,$fid,$data_id);
		           	if(!$result){
		                    $rerror    =    $rerror +1;    
		                }
			     }
			 }	
		}
		
		if ($rerror > 0){//设置失败
					echo json_encode(array('result'=>0));
			     }else{
			      	redirect(U('Modular/ModularApply/step_four',array('mid'=>$mid)));
					//echo "<script type='text/javascript'> alert('保存成功！转向属性关系表');window.location.href=\"".U('Modular/ModularCour/relations')."\";</script>";                 
			     }	
	}
	/*****第四步***生成代码页*******/
	public function step_four(){
		$this_step = 4;
		$mid = isset($_GET['mid'])?intval($_GET['mid']):0;
		//echo $mid;echo 'dd';exit;
		if($mid==0) $mid = $_SESSION['MID'];
		if(!empty($mid)){
			$dataArr = $this->create_form($mid);
			$js_src = APP_URL.U('Modular/ModularApply/create_jsForm',array('mid'=>$mid));
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	public function ajaxSaveFormData(){
		$data['status'] = 0;
		$arr = array();
		$mid = isset($_REQUEST['mid'])?intval($_REQUEST['mid']):'';
		$callback = $_GET['callback'];
		if($mid){
			$arr['name'] =  isset($_REQUEST['m_name'])?preg_replace("/<([a-zA-Z]+)[^>]*>/","",trim($_REQUEST['m_name'])):'';
			$arr['sex'] =  isset($_REQUEST['m_sex'])?intval($_REQUEST['m_sex']):'';
			$arr['school'] =  isset($_REQUEST['m_school'])?preg_replace("/<([a-zA-Z]+)[^>]*>/","",trim($_REQUEST['m_school'])):'';
			$arr['grade'] =  isset($_REQUEST['m_grade'])?intval($_REQUEST['m_grade']):'';
			$arr['dept'] =  isset($_REQUEST['m_subject'])?intval($_REQUEST['m_subject']):'';
			$arr['email'] =  isset($_REQUEST['m_email'])?trim($_REQUEST['m_email']):'';
			$arr['tel'] =  isset($_REQUEST['m_mobile'])?trim($_REQUEST['m_mobile']):'';
			$arr['message'] =  isset($_REQUEST['m_words'])?preg_replace("/<([a-zA-Z]+)[^>]*>/","",trim($_REQUEST['m_words'])):'';
			$arr['attr'] = isset($_REQUEST['attr'])? $_REQUEST['attr']:'';//场次属性名称
			/***对添加的场次属性进行转换为序列化字符串***/
			if(!empty($arr['attr'])){
				$arr_str ='';
				foreach($arr['attr'] as $k=>$v){
				   $v = explode('|',$v);
				   $v_str =$v[1];
				   $arr_str .=	$v_str.'_';
				}
				$arr_str = trim($arr_str,'_');
				$arr_str = serialize($arr_str);
				$arr['attr'] =$arr_str;
			}
			
			$moduleForm = D('ModelForm');
			$moduleInfo =  $moduleForm->get_moduleInfo_by_mid($mid);
			if($moduleInfo['isgoldlimit'] ==1){
				$GoldCard = $moduleForm->checkUserIsbGoldCard($arr['name']);
				$isGoldCard=0;//不是金卡会员
				if(!empty($GoldCard)){
					foreach($GoldCard as $v){
						if($v['bgoldcard']==1){
							$isGoldCard=1;//是金卡会员
							break;
						}	
					}
				}
				if($isGoldCard == 0){//不是金卡会员，给出不能重复报名提示
					$data['status'] = 4;
					echo $callback.'('.json_encode($data).')';
					exit;
				}
			}
			if($moduleInfo['limitshow'] == 1){//限制报名人数
				$totalCount = $moduleForm->get_dataCount(" mid = '$mid' ");
				if(!empty($moduleInfo['limited'])){
					$limit['start'] = reset(explode(',',$moduleInfo['limited']));
					$limit['end'] = end(explode(',',$moduleInfo['limited']));
					if($totalCount>=$limit['end']){
						$data['status'] = 3;
						echo $callback.'('.json_encode($data).')';
						exit;
					}
				}
			}
			if($moduleForm->get_formData_by_info($arr,$mid)>0){
				$data['status'] = 2;
			}else{
				if($moduleForm->saveFormData($arr,$mid)){//生成表单后添加数据表
					$data['status'] = 1;
					//判断是否触发短信，如果触发，则发送短信
					if($moduleInfo['message']==1 && !empty($moduleInfo['messagetext'])){
						/*发送报名/预约短信*/
						import("COM.MsgSender.SmsSender");
						$smsSender = new SmsSender();
						$replaceArr = C('REPLACES');
						if(!empty($replaceArr)){
							$gradeArr = C('GRADES');
							$subjectArr = C('SUBJECTS');
							foreach ($replaceArr as $key =>$replace){
								switch ($replace['title']){
									case '姓名':
										$replace_to = $arr['name'];
										break;
									case '性别':
										$replace_to = ($arr['sex']==1)?'女':'男';
										break;
									case '学校':
										$replace_to = $arr['school'];
										break;
									case '年级':
										$replace_to = $gradeArr[$arr['grade']];
										break;
									case '学科':
										$replace_to = $subjectArr[$arr['dept']];
										break;
									case 'Email':
										$replace_to = $arr['email'];
										break;
									case '手机号':
										$replace_to = $arr['tel'];
										break;
									case '留言':
										$replace_to = $arr['message'];
										break;
								}
								$moduleInfo['messagetext'] = str_replace($replace['replace'],$replace_to,$moduleInfo['messagetext']);
							}
						}
						if($smsSender->sendSms($arr['tel'], $moduleInfo['messagetext']) == true){
							$data['is_sms'] = 1;
						}else{
							$data['is_sms'] = 0;
						}
					}
					$data['status'] = 1;
				}
			}
		}
		echo $callback.'('.json_encode($data).')';
	}

	
	/**通过AJAX获取对应的课程，校区，时间 等**************/
	public function ajax_attr_relation(){
		#header('Content-type:text/javascript');
		$callback = isset($_REQUEST['callback']) ? trim($_REQUEST['callback']):0;
		$main_id= isset($_REQUEST['main_id']) ? trim($_REQUEST['main_id']):0;
		$mid= isset($_REQUEST['mid']) ? trim($_REQUEST['mid']):0;
		if(!empty($main_id) && !empty($mid)){
			$moduleForm = D('ModelForm');
			$data_name = $moduleForm->get_ajax_attr($mid,$main_id);
 			if(!empty($data_name)){
				foreach($data_name as $k=>$att){
					$k =$k+9;
					$data .='<div class="modSignup_item mi'.$k.'">';
					$data .='<span class="alt">'.$att['title2'].':</span>&nbsp;';
					$data .='<select name="attr[]" id="attr_'.$att['fid'].'">';
					$data .='<option value="">请选择'.$att['title2'].'</option>';
					foreach($att['aname'] as $key=>$atrname){
						$data .='<option value="'.$atrname['data_id'].'|'.$atrname['aname'].'">'.$atrname['aname'].'</option>';
					}
					$data .='</select>';
					$data .= '</div>';
				}
		}
		echo $callback.'('.json_encode($data).');';
		//echo $callback.'('.json_encode(htmlspecialchars($data)).')';
		exit(); 	
	}
}	
	
	/*生成预约/报名表单*/
	protected function create_form($mid){
		
		$moduleForm = D('ModelForm');
		//生成预览效果表单
		$moduleInfo = $moduleForm->get_moduleInfo_by_mid($mid);
		$moduleFormInfo = $moduleForm->get_moduleFormInfo_by_mid($mid);
		/**start *步骤4添加场次属性***/
		$moduleAttributeInfo = $moduleForm->get_moduleFormAttribute_by_mid($mid);
		//print_R($moduleAttributeInfo);exit;
		/**end*步骤4添加场次属性***/
		
		$js_str = '';

		$js_str .= $js_str.'
		
	function checkInfo(){';
		
		$form_str .= '
<div class="modSignup">
	<form id="form_'.$mid.'" name="" method="post" action="">
	<input type="hidden" name="mid" value='.$mid.'>
	<div class="modSignup_hd">'.$moduleInfo['name'].'</div>';
		if(!empty($moduleInfo['display'])){
			$dataCount = $moduleForm->get_dataCount(" mid= '$mid'");
			$form_str .= '
	<div class="modSignup_num">'.str_replace('#','<em>'.$dataCount.'</em>',$moduleInfo['words']).'</div>';
		}
		$form_str .= '
	<div class="modSignup_form">';
		$ajaxData = '';
		foreach ($moduleFormInfo as $key =>$item){
			if($item['display'] == 1){
				$require_str = ($item['required'] == 1)?'<em>*</em> ':'';
				$form_str .= '
	    <div class="modSignup_item mi'.$key.'">';
				switch ($item['titleid']){
					case 1://姓名
					if($item['cate'] == 'text'){
						$form_str .= '
		  <span class="alt">'.$require_str.$item['title2'].'：</span>
		  <input type="text" name="m_name" id="m_name" value="" style="clear:both;">';
					}
					if($item['required'] == 1){
						$js_str = $js_str . '
		if($("#m_name").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
					}
					$ajaxData .= 'name:$("#m_name").val(),';
					break;

					case 2://性别
					if($item['cate'] == 'radio'){
						$form_str .= '
		  <span class="alt">'.$require_str.$item['title2'].'：</span>
		  <input type="radio" name="m_sex" id="m_sex" value="0" checked>男  <input type="radio" name="m_sex" id="m_sex" value="1">女';
					}
					if($item['required'] == 1){
						$js_str = $js_str.'
		if($("input[name=m_sex]:checked").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
					}
					$ajaxData .= 'sex:$("input[name=m_sex]:checked").val(),';
					break;

					case 3://学校
					if($item['cate'] == 'text'||$item['cate'] == 'popup'){
						$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>
		<input type="text" name="m_school" id="m_school" value="" size="30">';
					}
					if($item['required'] == 1){
						$js_str = $js_str.'
		if($("#m_school").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
					}
					$ajaxData .= 'school:$("#m_school").val(),';
					break;

					case 4://年级
					$GRADES = C('GRADES');
					$gradeArr = json_decode($item['remark']);
					if(!empty($gradeArr)){
						$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>';
						if($item['cate'] == 'select'){
							$form_str .= '
		<select id="m_grade" name="m_grade">
		  <option value="">请选择'.$item['title2'].'</option>';
							foreach ($gradeArr as $key=>$grade){
								$form_str .= '
			<option value="'.$grade.'">'.$GRADES[$grade].'</option>';
							}
							$form_str .= '
		</select>';
						}
						if($item['cate'] == 'radio'){
							foreach ($gradeArr as $key=>$grade){
								$form_str .= '
		<label><input type=radio name=m_grade id="m_grade_'.$key.'" value="'.$grade.'">'.$GRADES[$grade].'</label>';
							}
						}
					}

					if($item['required'] == 1){
						if($item['cate'] == 'select'){
							$js_str = $js_str . '
		if($("#m_grade").val() == "")';
						}
						if($item['cate'] == 'radio'){
							$js_str = $js_str . '
		if($("input[name=m_grade]:checked").val() == "")';
						}
						$js_str = $js_str .'{alert("'.$item['title2'].'不能为空");return false;}';
					}
					//$ajaxData_grade = ($item['cate'] == 'select')?'$("#m_grade").val()':'$("input[name=m_grade]:checked").val()';
					$ajaxData .= ($item['cate'] == 'select')?'grade:$("#m_grade").val(),':'grade:$("input[name=m_grade]:checked").val(),';
					break;

					case 5://学科
					$SUBJECTS = C('SUBJECTS');
					$subjectArr = json_decode($item['remark']);
					if(!empty($subjectArr)){
						$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>';
						if($item['cate'] == 'select'){
							$form_str .= '
		<select id="m_subject" name="m_subject">
			<option value="">请选择'.$item['title2'].'</option>';
							foreach ($subjectArr as $key=>$subject){
								$form_str .= '
			<option value="'.$subject.'">'.$SUBJECTS[$subject].'</option>';
							}
							$form_str .= '
		</select>';
						}
						if($item['cate'] == 'radio'){
							foreach ($subjectArr as $key=>$subject){
								$form_str .= '
		<input type=radio name=m_subject id="m_subject_'.$key.'" value="'.$subject.'">'.$SUBJECTS[$subject];
							}
						}
					}

					if($item['required'] == 1){
						if($item['cate'] == 'select'){
							$js_str = $js_str .'
		if($("#m_subject").val() == "")';
						}
						if($item['cate'] == 'radio'){
							$js_str = $js_str . '
		if($("input[name=m_subject]:checked").val() == "")';
						}
						$js_str = $js_str .'{alert("'.$item['title2'].'不能为空");return false;}';
					}
					//$ajaxData_subject = ($item['cate'] == 'select')?'$("#m_subject").val()':'$("input[name=m_subject]:checked").val()';
					$ajaxData .= ($item['cate'] == 'select')?'dept:$("#m_subject").val(),':'dept:$("input[name=m_subject]:checked").val(),';
					break;

					case 6://Email
					if($item['cate'] == 'text'){
						$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>
		<input type="text" name="m_email" id="m_email" value="" size="30">';
					}
					if($item['required'] == 1){
						$js_str = $js_str.'
							if($("#m_email").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
					}
					$js_str = $js_str.'
		if($("#m_email").val()!="" && !$("#m_email").val().match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)){ alert("'.$item['title2'].'格式错误");return false;}';
					$ajaxData .= 'email:$("#m_email").val(),';
					break;

					case 7://手机号
					if($item['cate'] == 'text'){
						$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>
		<input type="text" name="m_mobile" id="m_mobile" value="" size="30">';
					}
					if($item['required'] == 1){
						$js_str = $js_str.'
		if($("#m_mobile").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
					}
					$js_str = $js_str.'
		if($("#m_mobile").val()!="" && !$("#m_mobile").val().match(/^1[3|5|8][0-9]\d{8}$/)){alert("'.$item['title2'].'格式错误");return false;}';//验证手机格式
					$ajaxData .= 'tel:$("#m_mobile").val(),';
					break;

					case 8://留言
					$form_str .= '
		<span class="alt">'.$require_str.$item['title2'].'：</span>';
					if($item['cate'] == 'text'){
						$form_str .= '
		<input type="text" name="m_words" id="m_words" value="">';
					}
					if($item['cate'] == 'textarea'){
						$form_str .= '
		<textarea name="m_words" id="m_words" cols=20 rows=2></textarea>';
					}

					if($item['required'] == 1){
						$js_str = $js_str.'
		if($("#m_words").val() == ""){alert("'.$item['title2'].'不能为空");return false;}';
						$ajaxData .= 'message:$("#m_words").val().replace(/\r\n|\n/g, "<br>"),';
					}else{
						$ajaxData .= 'message:$("#m_words").val(),';
					}

					break;
				}
				$form_str .= '
	    </div>';
			}
		}
		/**start*添加场次属性***/
		 $form_str .= '
		<div class="modSignup_item mi8">';	
		if(!empty($moduleAttributeInfo)){
			$j=0;
			$attrname_str = '';
			foreach($moduleAttributeInfo as $AttrInfo){
				$form_str .= '
				<span class="alt">'.$AttrInfo['title'].':</span>';
				//if($item['cate'] == 'select'){
				$form_str .= '
				<select id="attr_'.$AttrInfo['fid'].'" name="attr[]"  onchange="javascript:ajaxattr(this.value,'.$mid.');">
				<option value="">请选择'.$AttrInfo['title'].'</option>';
				foreach ($AttrInfo['name'] as $key=>$atrname){
					$form_str .= '
					<option value="'.$atrname['id'].'|'.$atrname['name'].'">'.$atrname['name'].'</option>';
				}
				$form_str .= '
				</select>';
				$j++;
				$js_str = $js_str.'
				if($("attr_'.$AttrInfo['fid'].'").val() == ""){alert("'.$AttrInfo['title'].'不能为空");return false;}';				
				//$ffid = $AttrInfo['fid'];	//}
				$attrname_str .= '$("#attr_'.$AttrInfo['fid'].'").val()'.'+"_"+';		
				}
			}
			$attrname_str .= '$("#attr_'.$AttrInfo['fid'].'").val()'.'+"_"+';
			
			$form_str .= '
	    </div>';
		$form_str .= '<span id="attr"></span>';	
	/**end*添加场次属性***/
		
		$ajaxData .= ' attrname_str:'.trim($attrname_str,'+"_"+').', ';
		$ajaxData .= ' mid:'.$mid.' ';
		$form_str .= '
	</div>
	<div class="submit"><input type="button" value="确认提交" class="btn" onclick="return checkInfo()"></div>
   </form>
</div>';
		$form_str .= '<script type="text/javascript" src="http://static.gaosiedu.com/public/js/jquery.js"></script>';
		$js_str = $js_str.';'.trim($attrname_str,'+"_"+').';
		var formData = $("#form_'.$mid.'").serialize();
		$.ajax({
			url:"'.APP_URL.U('Modular/ModularApply/ajaxSaveFormData').'",
				type:"post",
				dataType: "jsonp",
				data:formData,
				success:function(data){
					if(data.status==1){
						alert("提交成功");
						location.reload();
					}else if(data.status==2){
						alert("提交失败，不能重复预约/报名");
					}else if(data.status==3){
						alert("提交失败，预约/报名名额已满");
					}else if(data.status==4){
						alert("抱歉家长，报名失败。在高思持续学习满一年及以上即可成为金卡学员并获得相应优惠");
					}else{
						alert("提交失败");
					}
				}
		});
	}
';

	$js_str = $js_str.'function ajaxattr('.main_id.','.mid.'){
		getSelectVal('.main_id.','.mid.');
	}';
	$js_str =$js_str .'
	function getSelectVal('.main_id.','.mid.'){
	 var str1=main_id.split(\'|\');main_id=str1[0];
	 $.ajax({
	 		url:"'.APP_URL.U('Modular/ModularApply/ajax_attr_relation').'?t="+ new Date().getTime(),
	       	type:"GET",
			dataType: "jsonp",
			async:false,
			data:{main_id:'.main_id.',mid:'.mid.'},    
	 		success: function (data) {
	 		 	 	$("#attr").empty();           
	        	 	$("#attr").html(data);
            }
	        })
}';	
	return array('html'=>htmlspecialchars($form_str),'js'=>$js_str);

}
	
	
	/*js调用产生表单*/
	public function create_jsForm(){
		$mid = isset($_REQUEST['mid'])?intval($_REQUEST['mid']):0;
		if($mid==0) $mid = $_SESSION['MID'];
		$dataArr = array();
		if(!empty($mid)){
			$dataArr = $this->create_form($mid);
		}
		$callback = $_GET['callback'];
		echo $callback.'('.json_encode(array('html'=>htmlspecialchars_decode($dataArr['html']),'js'=>$dataArr['js'])).')';
	}

	/*删除指定模块报名、预约记录*/
	public function deleteData(){
		$mid = isset($_GET['mid'])?intval($_GET['mid']):'';
		if(!empty($mid)){
			$deleteIdStr = isset($_POST['deleteId'])?"'".implode("','",$_POST['deleteId'])."'":'';
			$moduleForm = D('ModelForm');
			if($moduleForm->deleteDataById($deleteIdStr,$mid)){
				$this->error('记录删除成功',U('Modular/ModularApply/preview',array('mid'=>$mid)));
			}
			echo $deleteIdStr;die;
		}else{
			$this->error('非法操作');
		}
	}
}

?>
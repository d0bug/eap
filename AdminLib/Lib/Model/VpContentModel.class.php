<?php
class VpContentModel extends Model {
	public  $dao = null;
	public function __construct(){

		$this->dao = Dao::getDao();
		$this->BS_Teacher = 'BS_Teacher';
		$this->V_S_Dept = 'V_S_Dept';
		$this->V_D_XueKe = 'V_D_XueKe';
		$this->V_D_KeCheng = 'V_D_KeCheng';
		$this->V_D_Subject = 'V_D_Subject';
		$this->V_BS_StudentLessonHeLu = 'V_BS_StudentLessonHeLu';
		$this->view_VB_StudentLessonHeLu = 'view_VB_StudentLessonHeLu';
		$this->vp_kechenghelu = 'vp_kechenghelu';
		$this->vp_kechenghelu_files = 'vp_kechenghelu_files';
		$this->vp_kechenghelu_log = 'vp_kechenghelu_log';
		$this->vp_training_program = 'vp_training_program';
	}


	public function get_menuTree(){
		session_start();
		if(!empty($_SESSION['metuTree'])){
			$menuTree = $_SESSION['metuTree'];
		}else{
			$deptArr = C('DEPT');
			$deptStr = "'".implode("','",$deptArr)."'";
			$menuTree = $this->dao->getAll('SELECT [sCode] as id,[sName] as text  FROM '.$this->V_S_Dept." WHERE [bValid] = 1 AND [sName] IN ($deptStr)");//一级
			if(!empty($menuTree)){
				foreach ($menuTree as $key=>$menu){
					$isHaveTeacher = $this->dao->getOne('SELECT count(1) FROM '.$this->BS_Teacher.' WHERE [bValid] =1 AND [nKind] = 3 AND [sDeptCode] = '.$this->dao->quote($menu['id']));
					if($isHaveTeacher >0){
						$menuTree[$key]['attributes'] = '/deptCode/'.$menu['id'];
						$menuTree[$key]['state'] = 'open';
						$tempArr = $this->dao->getAll('SELECT [id],[sName] as text FROM '.$this->V_D_XueKe);
						if(!empty($tempArr)){
							$menuTree[$key]['state'] = 'closed';
							$menuTree[$key]['children'] = $tempArr;//二级
							foreach ($tempArr as $key2=>$child){
								$subjectArr = $this->get_subjectArr($child['id']);
								if(!empty($subjectArr)){
									$subjectStr = '';
									$condition = ' (';
									foreach ($subjectArr as $kk=>$subject){
										$condition .= '  [sTeachSubject] like '.$this->dao->quote('%'.$subject['scode'].'%').' or';
										$subjectStr .= $subject['scode'].',';
									}
									$condition = trim($condition,'or');
									$condition .= ' )';
									$subjectStr = "'".implode("','",explode(',',trim($subjectStr,',')))."'";
								}
								$menuTree[$key]['children'][$key2]['attributes'] = '/deptCode/'.$menu['id'].'/xuekeId/'.$child['id'];
								$menuTree[$key]['children'][$key2]['state'] = 'open';
								$tempArr2 = $this->dao->getAll('SELECT [sCode] as id,[sName] as text FROM '.$this->BS_Teacher.' WHERE [bValid] =1 AND [nKind] = 3 AND [sDeptCode] = '.$this->dao->quote($menu['id']).' AND [nVIPXueKe] = '.$this->dao->quote($child['id']));
								if(!empty($tempArr2)){
									$menuTree[$key]['children'][$key2]['state'] = 'closed';
									$menuTree[$key]['children'][$key2]['children'] = $tempArr2;//三级
									foreach ($tempArr2 as $key3=>$grandson){
										$menuTree[$key]['children'][$key2]['children'][$key3]['attributes'] = '/deptCode/'.$menu['id'].'/xuekeId/'.$child['id'].'/teacherCode/'.$grandson['id'];
										$menuTree[$key]['children'][$key2]['children'][$key3]['state'] = 'open';
										$tempArr3 = $this->dao->getAll('SELECT MAX([sStudentCode]) AS id,MAX([sStudentName]) AS text FROM '.$this->view_VB_StudentLessonHeLu.' WHERE [sSubjectCode] IN ('.$subjectStr.') AND [steacherCode]= '.$this->dao->quote($grandson['id']).' GROUP BY [sContractCode]');
										if(!empty($tempArr3)){
											$menuTree[$key]['children'][$key2]['children'][$key3]['state'] = 'closed';
											$menuTree[$key]['children'][$key2]['children'][$key3]['children'] = $tempArr3;//四级
											foreach ($tempArr3 as $key4=>$great_grandson){
												$menuTree[$key]['children'][$key2]['children'][$key3]['children'][$key4]['attributes'] = '/deptCode/'.$menu['id'].'/xuekeId/'.$child['id'].'/teacherCode/'.$grandson['id'].'/studentCode/'.$great_grandson['id'];
												$menuTree[$key]['children'][$key2]['children'][$key3]['children'][$key4]['state'] = 'open';
											}

										}
									}
								}else{
									unset($menuTree[$key]['children'][$key2]);
								}
							}
						}
						$menuTree[$key]['children'] = array_values($menuTree[$key]['children']);
					}else{
						unset($menuTree[$key]);
					}
				}
			}
			$menuTree = array_values($menuTree);
			$_SESSION['metuTree'] = $menuTree;
		}
		return $menuTree;
	}


	public function get_contents($arr,$currentPage, $pageSize){
		$placeHolder = '%_placeholder_';
		$strQuery = 'SELECT '.$placeHolder.'
							FROM '.$this->vp_kechenghelu.' AS helu 
							LEFT JOIN '.$this->view_VB_StudentLessonHeLu.' as view_helu ON view_helu.[id] = helu.[helu_id] 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS f ON view_helu.[id] = f.[helu_id] 
							LEFT JOIN '.$this->V_D_KeCheng.' AS kc ON view_helu.[sKeChengCode] = kc.[sCode]  
							WHERE f.[url] !=NULL ';
		if(!empty($arr['deptCode'])){
			$teacherStr = '';
			$teacherArr = $this->get_vipTeacherList($arr);
			if(!empty($teacherArr)){
				foreach ($teacherArr as $key=>$teacher){
					$teacherStr .= $teacher['scode'].',';
				}
				$teacherStr = "'".implode("','",explode(',',trim($teacherStr,',')))."'";
				$strQuery .= ' AND view_helu.[steacherCode] IN ('.$teacherStr.') ';
			}else{
				$strQuery .= ' AND view_helu.[id] = NULL ';
			}

			if(!empty($arr['xuekeId'])){
				$subjectStr = $this->get_subjectStr($arr['xuekeId']);
				if(!empty($subjectStr)){
					$strQuery .= ' AND kc.[sSubjectCode] IN ('.$subjectStr.') ';
				}
			}
			if(!empty($arr['studentCode'])){
				$strQuery .= ' AND view_helu.[sStudentCode] = '.$this->dao->quote($arr['studentCode']);
			}
			if(!empty($arr['teacherCode'])){
				$strQuery .= ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
			}
			if(!empty($arr['lessonNo'])){
				$strQuery .= ' AND view_helu.[nLessonNo] = '.$this->dao->quote($arr['lessonNo']);
			}
		}

		if(!empty($arr['keyword'])){
			$strQuery .= ' AND ( f.[title] LIKE '.$this->dao->quote('%'.$arr['keyword'].'%').' )';
		}
		if(!empty($arr['start'])){
			$strQuery .= ' AND  helu.[lasttime] >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])){
			$strQuery .= ' AND  helu.[lasttime] <= '.$this->dao->quote(date('Y-m-d',strtotime($arr['end'])+3600*24));
		}

		$fileds =  'view_helu.[id] as helu_id,
				    view_helu.[nLessonNo],
				    view_helu.[sTeacherName],
					view_helu.[sKeChengName],
					view_helu.[sStudentName],
					helu.[id] as id,
					convert(varchar(20),helu.[lasttime],120) as lasttime,
					f.[url],
					f.[type],
					f.[title] ';

		$count = $this->dao->getOne(str_replace($placeHolder, 'count(1)', $strQuery));
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = str_replace($placeHolder, $fileds, $strQuery);
		$order = ' ORDER BY [helu_id] DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$urlArr = explode('|',trim($row['url'],'|'));
				if(is_array($urlArr) && count($urlArr)>1){
					$list[$key]['title'] = '';
					foreach ($urlArr as $k=>$url){
						$list[$key]['title'] .= $row['title'].'('.$k.').'.end(explode('.',$url)).'&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('Vip/VipStudents/download',array('id'=>$row['id'],'type'=>($row['type']+1),'order'=>$k)).'" class="blue" target="_blank">下载</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="previewFile(event,\''.$row['helu_id'].'\',\''.$row['type'].'\',\''.base64_encode($url).'\')" class="blue">预览</a><br>';
					}
				}else{
					$list[$key]['title'] = $row['title'].'.'.end(explode('.',$row['url'])).'&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('Vip/VipStudents/download',array('id'=>$row['id'],'type'=>($row['type']+1),'order'=>0)).'" class="blue"  target="_blank">下载</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="previewFile(event,\''.$row['helu_id'].'\',\''.$row['type'].'\',\''.base64_encode($row['url']).'\')" class="blue">预览</a>';
				}
			}
		}
		return array($count, $list);
	}



	public function get_subjectArr($xuekeId){
		return $this->dao->getAll('SELECT [sCode],[sName] FROM '.$this->V_D_Subject.' WHERE [nXueKe] = '.$this->dao->quote($xuekeId));
	}

	public function get_subjectStr($xuekeId){
		$subjectArr = $this->get_subjectArr($xuekeId);
		if(!empty($subjectArr)){
			$subjectStr = '';
			foreach ($subjectArr as $kk=>$subject){
				$subjectStr .= $subject['scode'].',';
			}
			$subjectStr = "'".implode("','",explode(',',trim($subjectStr,',')))."'";
		}
		return $subjectStr;
	}


	public function delete_contents($arr){
		return (boolean)$this->dao->execute('DELETE FROM '.$this->vp_kechenghelu_files.' WHERE [helu_id] ='.$this->dao->quote($arr['helu_id']).' AND [type] ='.$this->dao->quote($arr['type']));
	}


	public function do_moveFiles($arr){
		$subjectStr = $this->get_subjectStr($arr['xuekeId']);
		$view_heluInfo = $this->dao->getRow('SELECT [id],[sStudentName],[sKeChengName],[sTeacherName],[nLessonNo] FROM '.$this->view_VB_StudentLessonHeLu.' WHERE [sSubjectCode] IN ('.$subjectStr.') AND [steacherCode] = '.$this->dao->quote($arr['teacherCode']).' AND [sStudentCode] = '.$this->dao->quote($arr['studentCode']).' AND [sKeChengCode] = '.$this->dao->quote($arr['kechengCode']).' AND [nLessonNo] = '.$this->dao->quote($arr['lessonNo']));
		$to_heluId = !empty($view_heluInfo)?$view_heluInfo['id']:'';
		$succNum = 0;
		if(!empty($to_heluId)){
			foreach ($arr['heluIdArr'] as $key=>$val){
				$heluId = reset(explode('_',$val));
				$type = end(explode('_',$val));
				$new_title = $view_heluInfo['skechengname'].'_'.$view_heluInfo['steachername'].'_'.$view_heluInfo['sstudentname'].'_'.$view_heluInfo['nlessonno'].'_';
				$new_title .= ($type == 0)?'课程讲义':'测试卷';
				$new_title .= '_'.date('Y_m_d_H_i_s');
				$success = (boolean)$this->dao->execute('UPDATE '.$this->vp_kechenghelu_files.' SET [helu_id]='.$this->dao->quote($to_heluId).',[title]='.$this->dao->quote($new_title).' WHERE [helu_id] ='.$this->dao->quote($heluId).' AND [type] ='.$this->dao->quote($type));
				if($success == true){
					$succNum++;
				}
			}
		}
		return $succNum;
	}


	public function get_lessonNo($arr){
		return $this->dao->getAll('SELECT s.[nLessonNo],helu.[lesson_topic]
										  FROM '.$this->V_BS_StudentLessonHeLu.' AS s  
										  LEFT JOIN '.$this->vp_kechenghelu.' AS helu ON s.id = helu.helu_id 
										  WHERE [steacherCode]= '.$this->dao->quote($arr['teacherCode']).' AND [sStudentCode] = '.$this->dao->quote($arr['studentCode']).' AND [sKeChengCode] = '.$this->dao->quote($arr['kechengCode']));
	}


	public function get_lessonHeLu($arr, $currentPage=1, $pageSize=20){
		$count = $this->get_lessonHeLuCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT view_helu.[id] as id,
							view_helu.[sStudentName],
							view_helu.[sTeacherName],
							dpt.[sName] as sAreaName,
							view_helu.[nHoursReal],
							convert(varchar(20),view_helu.[dtDateReal],111) as dtDateReal,
							convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal,
							helu.[id] as helu_info_id,
							helu.[lesson_topic],
							helu.[comment],
							helu.[lesson_record_img],
							helu.[lesson_report_url],
							helu.[lesson_report_url_wx],
							a.[url] as handouts_url,
							a.[from_type] as handouts_from_type,
							b.[url] as itembank_url,
							b.[from_type] as itembank_from_type,
							helu.[itembank_score] 
							FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							LEFT JOIN '.$this->V_S_Dept.' as dpt ON dpt.sCode = view_helu.sDeptCode 
							LEFT JOIN '.$this->vp_kechenghelu.' as helu ON helu.helu_id = view_helu.id 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON view_helu.id = a.helu_id AND a.type = 0 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON view_helu.id = b.helu_id AND b.type = 1  
							WHERE view_helu.[nStatus] = 2 AND view_helu.[nAudit]=1 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		$condition = $this->get_lessonHeluCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		$order = ' ORDER BY [dtDateReal] DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['dtlessonbeginreal'] = date('H:i',strtotime($val['dtlessonbeginreal']));
				$list[$key]['dtlessonendreal'] = date('H:i',strtotime($val['dtlessonendreal']));
				$list[$key]['lesson_report_url_show'] = str_replace("/Upload/","/upload/",$val['lesson_report_url']);
				if(!empty($val['handouts_url'])){
					$temp_handouts_arr = explode('|',trim($val['handouts_url'],'|'));
					foreach ($temp_handouts_arr as $k=>$v){
						$list[$key]['handouts_arr'][$k]['url'] = $v;
						$list[$key]['handouts_arr'][$k]['file_url'] = APP_DIR.$v;
						$temp_file_type = end(explode('.',$v));
						if(in_array(strtolower($temp_file_type),array('jpg','jpeg','gif','png'))){
							$list[$key]['handouts_arr'][$k]['preview_url'] = APP_DIR.$v;
						}else{
							$list[$key]['handouts_arr'][$k]['preview_url'] = APP_DIR.str_replace('.'.$temp_file_type,'.swf',$v);
						}
						$list[$key]['handouts_arr'][$k]['file_type'] = end(explode('.',$v));


						$list[$key]['handouts_arr'][$k]['is_download'] = 0;
						if(file_exists($list[$key]['handouts_arr'][$k]['file_url'])){
							$list[$key]['handouts_arr'][$k]['is_download'] = 1;
						}

						$list[$key]['handouts_arr'][$k]['is_preview'] = 0;
						if(file_exists($list[$key]['handouts_arr'][$k]['preview_url'])){
							$list[$key]['handouts_arr'][$k]['is_preview'] = 1;
						}
					}
				}
				if(!empty($val['itembank_url'])){
					$temp_itembank_arr = explode('|',trim($val['itembank_url'],'|'));
					foreach ($temp_itembank_arr as $k=>$v){
						$list[$key]['itembank_arr'][$k]['url'] = $v;
						$list[$key]['itembank_arr'][$k]['file_url'] = APP_DIR.$v;
						$temp_file_type = end(explode('.',$v));
						if(in_array(strtolower($temp_file_type),array('jpg','jpeg','gif','png'))){
							$list[$key]['itembank_arr'][$k]['preview_url'] = APP_DIR.$v;
						}else{
							$list[$key]['itembank_arr'][$k]['preview_url'] = APP_DIR.str_replace('.'.$temp_file_type,'.swf',$v);
						}
						$list[$key]['itembank_arr'][$k]['file_type'] = end(explode('.',$v));


						$list[$key]['itembank_arr'][$k]['is_download'] = 0;
						if(file_exists($list[$key]['itembank_arr'][$k]['file_url'])){
							$list[$key]['itembank_arr'][$k]['is_download'] = 1;
						}

						$list[$key]['itembank_arr'][$k]['is_preview'] = 0;
						if(file_exists($list[$key]['itembank_arr'][$k]['preview_url'])){
							$list[$key]['itembank_arr'][$k]['is_preview'] = 1;
						}
					}
				}
				
				if(!empty($val['lesson_record_img'])){
					$temp_recordimg_arr = explode('|',trim($val['lesson_record_img'],'|'));
					foreach ($temp_recordimg_arr as $k=>$v){
						$list[$key]['recordimg_arr'][$k]['url'] = $v;
						$list[$key]['recordimg_arr'][$k]['file_url'] = APP_DIR.$v;
						$list[$key]['recordimg_arr'][$k]['preview_url'] = APP_DIR.$v;
						$list[$key]['recordimg_arr'][$k]['file_type'] = end(explode('.',$v));


						$list[$key]['recordimg_arr'][$k]['is_download'] = 0;
						if(file_exists($list[$key]['recordimg_arr'][$k]['file_url'])){
							$list[$key]['recordimg_arr'][$k]['is_download'] = 1;
						}

						$list[$key]['recordimg_arr'][$k]['is_preview'] = 0;
						if(file_exists($list[$key]['recordimg_arr'][$k]['file_url'])){
							$list[$key]['recordimg_arr'][$k]['is_preview'] = 1;
						}
					}
				}
			}
		}
		return $list;
	}


	public function get_lessonHeLuCount($arr){
		$strQuery = 'SELECT count(1) FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu
									LEFT JOIN '.$this->vp_kechenghelu.' as helu ON helu.helu_id = view_helu.id 
									LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON view_helu.id = a.helu_id AND a.type = 0 
									LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON view_helu.id = b.helu_id AND b.type = 1 
									WHERE view_helu.[nStatus] = 2 AND view_helu.[nAudit]=1  AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		$condition = $this->get_lessonHeluCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_lessonHeluCondition($arr){
		$condition = '';
		if(!empty($arr['deptCode'])){
			$condition.= ' AND view_helu.[sDeptCode] = '.$this->dao->quote($arr['deptCode']);
		}
		if(!empty($arr['teacherCode'])){
			$condition.= ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['studentCode'])){
			$condition.= ' AND view_helu.[sStudentCode] = '.$this->dao->quote($arr['studentCode']);
		}
		if(!empty($arr['startTime'])){
			$condition.= ' AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote($arr['startTime']);
		}
		if(!empty($arr['endTime'])){
			$condition.= ' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote(date('Y-m-d',strtotime($arr['endTime'])+3600*24));
		}
		if(!empty($arr['is_upload'])){
			$condition.= ($arr['is_upload']==1)?" AND a.[url] IS NOT NULL":" AND a.[url] IS NULL";
		}
		if(!empty($arr['teacherName'])){
			$condition.= ' AND view_helu.[sTeacherName] = '.$this->dao->quote($arr['teacherName']);
		}
		if(!empty($arr['studentName'])){
			$condition.= ' AND view_helu.[sStudentName] = '.$this->dao->quote($arr['studentName']);
		}
		return $condition;
	}



	public function get_lessonHeLuAll($condition=''){
		$strQuery = 'SELECT view_helu.[id] as id,
							view_helu.[sStudentName],
							view_helu.[sTeacherName],
							dpt.[sName] as sAreaName,
							convert(varchar(20),view_helu.[dtDateReal],111) as dtDateReal,
							convert(varchar(20),view_helu.[dtLessonBeginReal],108) as dtLessonBeginReal,
							convert(varchar(20),view_helu.[dtLessonEndReal],108) as dtLessonEndReal,
							helu.[id] as helu_info_id,
							helu.[lesson_topic],
							helu.[comment],
							helu.[lesson_report_url],
							a.[url] as handouts_url,
							a.[from_type] as handouts_from_type,
							b.[url] as itembank_url,
							b.[from_type] as itembank_from_type,
							helu.[itembank_score] 
							FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							LEFT JOIN '.$this->V_S_Dept.' as dpt ON dpt.sCode = view_helu.sDeptCode 
							LEFT JOIN '.$this->vp_kechenghelu.' as helu ON helu.helu_id = view_helu.id 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON view_helu.id = a.helu_id AND a.type = 0 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON view_helu.id = b.helu_id AND b.type = 1  
							WHERE view_helu.[nStatus] = 2 AND view_helu.[nAudit]=1 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		$order = ' ORDER BY [dtDateReal] DESC';
		$list = $this->dao->getAll($strQuery.$order);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['handouts_arr'] = explode('|',trim($row['handouts_url'],'|'));
				$list[$key]['handouts_count'] = count($list[$key]['handouts_arr']);
				$list[$key]['itembank_arr'] = explode('|',trim($row['itembank_url'],'|'));
				$list[$key]['itembank_count'] = count($list[$key]['itembank_arr']);
			}
		}
		return $list;
	}


	public function get_vipTeacherList($arr){
		$strQuery = 'SELECT [sCode],[sName] FROM '.$this->BS_Teacher.' WHERE [bValid] =1 AND [nKind] = 3 ';
		if(!empty($arr['deptCode'])){
			$strQuery .= ' AND [sDeptCode]='.$this->dao->quote($arr['deptCode']);
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_deptList(){
		return $this->dao->getAll('SELECT [sCode],[sName] FROM '.$this->V_S_Dept.' WHERE [bValid] =1 ');
	}


	public function get_heluLogList($arr, $currentPage=1, $pageSize=20){
		$count = $this->get_heluLogCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT [id],[student_name],[lesson_date],[lesson_topic],[teacher_name],[comment],convert(varchar(20),[helu_time],120) as helu_time,[is_select_sendsms],[is_trigger_sendsms],[is_upload_handouts],[helu_type],[to_mobile] FROM '.$this->vp_kechenghelu_log.' WHERE 1=1 ';
		$condition = $this->get_heluLogCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		$order = ' ORDER BY [id] DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_heluLogCount($arr){
		$strQuery = 'SELECT count(1) FROM '.$this->vp_kechenghelu_log.' WHERE 1=1 ';
		$condition = $this->get_heluLogCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_heluLogCondition($arr){
		$condition = '';
		if(!empty($arr['student_name'])){
			$condition .= ' AND student_name = '.$this->dao->quote($arr['student_name']);
		}
		if(!empty($arr['teacher_name'])){
			$condition .= ' AND teacher_name = '.$this->dao->quote($arr['teacher_name']);
		}
		if(!empty($arr['lesson_date_start'])){
			$condition .= ' AND lesson_date >= '.$this->dao->quote($arr['lesson_date_start']);
		}
		if(!empty($arr['lesson_date_end'])){
			$condition .= ' AND lesson_date <= '.$this->dao->quote($arr['lesson_date_end']);
		}
		if(!empty($arr['is_select_sendsms'])){
			$condition .= ' AND is_select_sendsms = '.$this->dao->quote($arr['is_select_sendsms']-1);
		}
		return $condition;
	}

	public function get_heluLogAll($arr){
		$strQuery = 'SELECT [id],[student_name],[lesson_date],[lesson_topic],[teacher_name],[comment],convert(varchar(20),[helu_time],120) as helu_time,[is_select_sendsms],[is_trigger_sendsms],[is_upload_handouts],[helu_type],[to_mobile]  FROM '.$this->vp_kechenghelu_log.' WHERE 1=1 ';
		$condition = $this->get_heluLogCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		$order = ' ORDER BY id DESC ';
		$offset = ($arr['cur_page'] - 1) * $arr['page_size'];
		$order = trim ( $order );
		if ($order) {
			$strQuery = str_ireplace ( $order, ' ', $strQuery );
		}
		$strQuery2 = 'SELECT * FROM (
                SELECT ROW_NUMBER() OVER(' . $order . ') _rownum,* FROM (
                    ' . $strQuery . '
                ) tbl
            ) tbl WHERE _rownum >' . $offset . ' AND _rownum <=' . ($offset + $arr['page_size']);
		
		return $this->dao->getAll($strQuery2);
	}
	
	
	public function get_heluLogAllCount($arr){
		$strQuery = 'SELECT count(1)  FROM '.$this->vp_kechenghelu_log.' WHERE 1=1 ';
		$condition = $this->get_heluLogCondition($arr);
		if(!empty($condition)){
			$strQuery .= $condition;
		}
		return $this->dao->getOne($strQuery);
	}



	public function get_programReviewList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_programReviewCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT *, convert(varchar(20),[instime],120) as instime2 FROM '.$this->vp_training_program.' WHERE 1=1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY [id] DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$temp_program_arr = explode('|',trim($val['program_url'],'|'));
				foreach ($temp_program_arr as $k=>$v){
					$list[$key]['program_arr'][$k]['url'] = $v;
					$list[$key]['program_arr'][$k]['file_url'] = APP_DIR.$v;
					$temp_file_type = end(explode('.',$v));
					if(in_array(strtolower($temp_file_type),array('jpg','jpeg','gif','png'))){
						$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.end(explode('_',$v));
					}else{
						$tempFileUrlArr = explode('/',$v);
						$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.$tempFileUrlArr[0].'/'.$tempFileUrlArr[1].'/'.$tempFileUrlArr[2].'/'.str_replace('.'.$temp_file_type,'.swf',end(explode("_",end($tempFileUrlArr))));
					}


					$list[$key]['program_arr'][$k]['is_download'] = 0;
					if(file_exists($list[$key]['program_arr'][$k]['file_url'])){
						$list[$key]['program_arr'][$k]['is_download'] = 1;
					}

					$list[$key]['program_arr'][$k]['is_preview'] = 0;
					if(file_exists($list[$key]['program_arr'][$k]['preview_url'])){
						$list[$key]['program_arr'][$k]['is_preview'] = 1;
					}
				}
			}
		}
		return $list;
	}


	public function get_programReviewCount($condition){
		$strQuery = 'SELECT COUNT(1) FROM '.$this->vp_training_program.' WHERE 1=1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		return $this->dao->getOne($strQuery);
	}
	
	
	
	public function get_programReviewAll($condition=''){
		
		$strQuery = 'SELECT *, convert(varchar(20),[instime],120) as instime2 FROM '.$this->vp_training_program.' WHERE 1=1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY [id] DESC';
		$list = $this->dao->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$temp_program_arr = explode('|',trim($val['program_url'],'|'));
				foreach ($temp_program_arr as $k=>$v){
					$list[$key]['program_arr'][$k]['url'] = $v;
					$list[$key]['program_arr'][$k]['file_url'] = APP_DIR.$v;
					$temp_file_type = end(explode('.',$v));
					if(in_array(strtolower($temp_file_type),array('jpg','jpeg','gif','png'))){
						$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.end(explode('_',$v));
					}else{
						$tempFileUrlArr = explode('/',$v);
						$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.$tempFileUrlArr[0].'/'.$tempFileUrlArr[1].'/'.$tempFileUrlArr[2].'/'.str_replace('.'.$temp_file_type,'.swf',end(explode("_",end($tempFileUrlArr))));
					}

					$list[$key]['program_arr'][$k]['is_download'] = 0;
					if(file_exists($list[$key]['program_arr'][$k]['file_url'])){
						$list[$key]['program_arr'][$k]['is_download'] = 1;
					}

					$list[$key]['program_arr'][$k]['is_preview'] = 0;
					if(file_exists($list[$key]['program_arr'][$k]['preview_url'])){
						$list[$key]['program_arr'][$k]['is_preview'] = 1;
					}
				}
			}
		}
		return $list;
	}

}
?>
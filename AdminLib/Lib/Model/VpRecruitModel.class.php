<?php

class VpRecruitModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->V_Biz_Recruitment = 'V_Biz_Recruitment';
		$this->v_d_general = 'v_d_general';//学历表
		$this->D_University = 'D_University';//学校表
		$this->vp_job_requirements = 'vp_job_requirements';//岗位需求表
		$this->vp_recruitment_weinxin_bind = 'vp_recruitment_weinxin_bind';//微信用户绑定表
		$this->vp_recruitment_weixin_rs = 'vp_recruitment_weixin_rs';//微信用户招聘简历关系表
		$this->vp_job_application = 'vp_job_application';//职位申请
		$this->V_D_XueKe = 'V_D_XueKe';
	}


	public function bindUser($userInfo){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_recruitment_weinxin_bind.' WHERE openid = '.$this->dao->quote($userInfo['openid']));
		if($count==0){
			$strQuery = 'INSERT INTO '.$this->vp_recruitment_weinxin_bind.' (openid,
																			 nickname, 
																			 sex, 
																			 country, 
																			 province, 
																			 city) 
																	 VALUES ('.$this->dao->quote($userInfo['openid']).',
																	 		 '.$this->dao->quote($userInfo['nickname']).',
																	 		 '.$this->dao->quote($userInfo['sex']).',
																	 		 '.$this->dao->quote($userInfo['country']).',
																	 		 '.$this->dao->quote($userInfo['province']).',
																	 		 '.$this->dao->quote($userInfo['city']).') ';
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return true;
	}


	public function insert_recruit($arr,$userInfo){
		if(!empty($arr['userName']) && !empty($arr['mobile']) && !empty($arr['email']) && !empty($arr['sex']) && !empty($arr['school']) && !empty($arr['year'])&& !empty($arr['month'])&& !empty($arr['major'])&& $arr['education']!=='' && !empty($arr['subject']) && $arr['teacherNature']!==''){
			$arr['sUndergraduate'] = '';
			$arr['sUnderObject'] = '';
			$arr['sMaster'] ='';
			$arr['sMasterObject'] = '';
			$arr['sDoctor'] = '';
			$arr['sDoctorObject'] = '';
			switch ($arr['education']){
				case 1://大专
				case 2://本科
				$arr['sUndergraduate'] = $arr['school'];
				$arr['sUnderObject'] = $arr['major'];
				break;
				case 3://硕士
				$arr['sMaster'] = $arr['school'];
				$arr['sMasterObject'] = $arr['major'];
				break;
				case 4://博士
				$arr['sDoctor'] = $arr['school'];
				$arr['sDoctorObject'] = $arr['major'];
				break;
				default://其他
				$arr['sUndergraduate'] = $arr['school'];
				$arr['sUnderObject'] = $arr['major'];
			}
			$now = date('Y-m-d H:i:s');
			if($arr['id']){
				$strQuery = 'UPDATE '.$this->V_Biz_Recruitment.' SET dCreatetime = '.$this->dao->quote($now).',
																	 sOperatorCode = '.$this->dao->quote('WEIXIN').',
																	 sName = '.$this->dao->quote(trim($arr['userName'])).',
																	 nSex = '.$this->dao->quote(abs($arr['sex'])).',
																	 nEducation = '.$this->dao->quote(abs($arr['education'])).',
																	 nEduYear = '.$this->dao->quote($arr['year']).',
																	 nEduMonth = '.$this->dao->quote($arr['month']).',
																	 sUndergraduate = '.$this->dao->quote($arr['sUndergraduate']).',
																	 sUnderObject = '.$this->dao->quote($arr['sUnderObject']).',
																	 sMaster = '.$this->dao->quote($arr['sMaster']).',
																	 sMasterObject = '.$this->dao->quote($arr['sMasterObject']).',
																	 sDoctor = '.$this->dao->quote($arr['sDoctor']).',
																	 sDoctorObject = '.$this->dao->quote($arr['sDoctorObject']).',
																	 sKeChengCode = '.$this->dao->quote($arr['subject']).',
																	 nPostType = '.$this->dao->quote($arr['teacherNature']).',
																	 sTel = '.$this->dao->quote($arr['mobile']).',
																	 sEmail = '.$this->dao->quote($arr['email']).' ,
																	  nSureCome = 21  
																	 WHERE id = '.$this->dao->quote($arr['id']);
			}else{
				$strQuery = 'INSERT INTO '.$this->V_Biz_Recruitment.' (dCreatetime,
																   sOperatorCode,
																   sName,
																   nSex,
																   nEducation,
																   nEduYear,
																   nEduMonth,
																   sUndergraduate,
																   sUnderObject,
																   sMaster,
																   sMasterObject,
																   sDoctor,
																   sDoctorObject,
																   sKeChengCode,
																   nPostType,
																   sTel,
																   sEmail,
																   nSureCome 
															  ) VALUES ('.$this->dao->quote($now).',
															  			'.$this->dao->quote('WEIXIN').',
															  			'.$this->dao->quote(trim($arr['userName'])).',
															  			'.abs($arr['sex']).',
															  			'.abs($arr['education']).',
															  			'.$this->dao->quote($arr['year']).',
															  			'.$this->dao->quote($arr['month']).',
															  			'.$this->dao->quote($arr['sUndergraduate']).',
															  			'.$this->dao->quote($arr['sUnderObject']).',
															  			'.$this->dao->quote($arr['sMaster']).',
															  			'.$this->dao->quote($arr['sMasterObject']).',
															  			'.$this->dao->quote($arr['sDoctor']).',
															  			'.$this->dao->quote($arr['sDoctorObject']).',
															  			'.$this->dao->quote($arr['subject']).',
															  			'.$this->dao->quote($arr['teacherNature']).',
															  			'.$this->dao->quote($arr['mobile']).',
															  			'.$this->dao->quote($arr['email']).',
															  			21)';
			}
			if($this->dao->execute($strQuery)){
				if($arr['id']){
					return true;
				}else{
					$newId = $this->dao->getOne("SELECT ident_current('".$this->V_Biz_Recruitment."')");
					if(!$arr['id']&&$newId){
						$strQuery2 =  'INSERT INTO '.$this->vp_recruitment_weixin_rs.' (openid,recruitment_id) VALUES ('.$this->dao->quote($userInfo['openid']).', '.$this->dao->quote($newId).') ';
						$this->dao->execute($strQuery2);
					}
					return $newId;
				}
			}
			return false;
		}
		return false;
	}

	//获取学历
	public function get_general(){
		return $this->dao->getAll('SELECT id-nType id ,sName,sDescription FROM '.$this->v_d_general.' WHERE ntype=45000');
	}

	//获取学校
	public function get_university($keyword=''){
		$strQuery = "SELECT id,sName FROM ".$this->D_University." WHERE bvalid=1 ";
		if($keyword){
			$strQuery .= " AND sName like '%".$keyword."%'";
		}
		return $this->dao->getAll($strQuery);
	}

	//获取职位类型
	public function get_postType(){
		return $this->dao->getAll('SELECT id-nType id ,sName,sDescription FROM '.$this->v_d_general.' WHERE ntype=47000');
	}

	//获取岗位需求
	public function get_jobList(){
		return $this->dao->getAll('SELECT * FROM '.$this->vp_job_requirements.' WHERE status=1');
	}


	public function get_jobInfo($id){
		return $this->dao->getRow('SELECT * FROM '.$this->vp_job_requirements.' WHERE id = '.$this->dao->quote($id).' and status=1');
	}


	public function get_recruitmentInfo($userInfo){
		if($this->check_isHaveRecruitment($userInfo['openid'])){
			$recruitmentInfo = $this->dao->getRow('  SELECT 	  rs.openid,
																  r.id,
																   r.dCreatetime,
																   r.sOperatorCode,
																   r.sName,
																   r.nSex,
																   r.nEducation,
																   r.nEduYear,
																   r.nEduMonth,
																   r.sUndergraduate,
																   r.sUnderObject,
																   r.sMaster,
																   r.sMasterObject,
																   r.sDoctor,
																   r.sDoctorObject,
																   r.sKeChengCode,
																   r.nPostType,
																   r.sTel,
																   r.sEmail,
																   r.sbak FROM '.$this->vp_recruitment_weixin_rs.' rs 
																   LEFT JOIN '.$this->V_Biz_Recruitment.' r ON rs.recruitment_id = r.id 
																   WHERE rs.openid = '.$this->dao->quote($userInfo['openid']));
			return $recruitmentInfo;
		}
		return false;
	}


	public function check_isHaveRecruitment($openid){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_recruitment_weixin_rs.' WHERE openid = '.$this->dao->quote($openid));
		if($count==0){
			return false;
		}
		return true;
	}


	public function apply_job($jobId, $recruitmentId){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_job_application.' WHERE job_id = '.$this->dao->quote($jobId).' AND recruitment_id = '.$this->dao->quote($recruitmentId) );
		if($count==0){
			$strQuery = 'INSERT INTO '.$this->vp_job_application.' (recruitment_id, job_id, createtime) VALUES ('.$this->dao->quote($recruitmentId).','.$this->dao->quote($jobId).','.$this->dao->quote(date('Y-m-d H:i:s')).')';
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return true;

	}


	public function get_universityNameById($universityId){
		if($universityId){
			return $this->dao->getOne('SELECT sName FROM '.$this->D_University.' WHERE id = '.$this->dao->quote($universityId));
		}
		return false;
	}


	public function get_educationName($educationId){
		if($educationId){
			return $this->dao->getOne('SELECT sName FROM '.$this->v_d_general.' WHERE ntype=45000 AND id-nType = '.$this->dao->quote($educationId));
		}
		return false;
	}


	public function get_postTypeName($postTypeId){
		return $this->dao->getOne('SELECT sName FROM '.$this->v_d_general.' WHERE ntype=47000 AND id-nType = '.$this->dao->quote($postTypeId));
	}
	
	
	public function get_subjectList(){
		return $this->dao->getAll('SELECT id,sName FROM '.$this->V_D_XueKe.' WHERE id >0 ');
	}
	
	
}
?>
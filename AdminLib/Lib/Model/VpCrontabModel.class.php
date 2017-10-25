<?php

class VpCrontabModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_dict_subject="vip_dict_subject";
		$this->vip_lecture_archive="vip_lecture_archive";
		$this->vip_teacher_lecture="vip_teacher_lecture";
		$this->vip_knowledge="vip_knowledge";
	}


	public function getSubjectAll(){
		return $this->dao2->getAll("SELECT id,title FROM ".$this->vip_dict_subject." WHERE status=1 "); 
	}


	public function getLectureAll($sid,$type){
		if($type==2){
			$tableName = $this->vip_teacher_lecture;
		}else{
			$tableName = $this->vip_lecture_archive;
		}
		$list = $this->dao2->getAll('SELECT id,title,cart,config FROM '.$tableName.' WHERE status != -1 AND is_update = 0 AND sid = '.$this->dao2->quote($sid).' order by id desc LIMIT 1500');
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['cart'] = unserialize($row['cart']);
				$list[$key]['config'] = unserialize($row['config']);
			}
		}
		return $list;
	}
	
	
	public function getKnowledge($idArr){
		$knowledgeIdStr = implode(',',$idArr);
		$list = $this->dao2->getAll('SELECT id,`name`,analysis FROM '.$this->vip_knowledge.' WHERE id IN ('.$knowledgeIdStr.') ');
		$newKnowledgeArr = array();
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$newKnowledgeArr[$row['id']] = $row['analysis'];
			}
		}
		return $newKnowledgeArr;
	}
	
	
	public function updateLectureCartAndConfig($cart,$config,$id,$type){
		if($type==2){
			$tableName = $this->vip_teacher_lecture;
		}else{
			$tableName = $this->vip_lecture_archive;
		}
		$sql = 'UPDATE '.$tableName.' SET cart = '.$this->dao2->quote(serialize($cart)).' ,config = '.$this->dao2->quote(serialize($config)).',is_update = 1 WHERE id = '.$this->dao2->quote($id);
		if($this->dao2->execute($sql)){
			return true;
		}
		return false;
	}

}
?>
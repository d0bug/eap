<?php
class VoteModel extends Model {
	public $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'model_voteInfo';
		$this->listTable = 'model_voteList';
		$this->optionTable = 'model_voteOptions';
		$this->userInfo = 'model_userinfo';
		$this->userList = 'model_userlist';
	}
	//添加投票
	public function add($info){
		$list = $info['vote'];
		unset($info['vote']);
		$status = true;
		$info['begintime'] = empty($info['begintime']) ? time() : strtotime($info['begintime']);
		$info['endtime'] = empty($info['endtime']) ? '0' : strtotime($info['endtime']);
		$info['spacing'] = abs(intval($info['spacing']));
		$sql = "insert into ".$this->tableName."(vote_name,begintime,endtime,description,rel,spacing) values('".$info['vote_name']."',".$info['begintime'].",".$info['endtime'].",'".$info['description']."',".$info['rel'].",".$info['spacing'].")";
		$this->dao->begin();
		$rs = $this->dao->execute($sql);
		if (false === $rs) {
			$status = false;
		}
		$lastid = $this->dao->lastInsertId();
		if ($lastid > 0){
			foreach ($list as $one){
				$sql = "insert into ".$this->listTable."(infoid,type,title) values($lastid,".$one['type'].",'".$one['title']."')";
				$rel = $this->dao->execute($sql);
				if (false === $rel) {
					$status = false;
				}
				$listid = $this->dao->lastInsertId();
				$options = explode(',',$one['voptions']);
				foreach ($options as $o){
					$sql = "insert into ".$this->optionTable."(listid,title) values($listid,'$o')";
					$onrel = $this->dao->execute($sql);
					if (false === $onrel) {
						$status = false;
					}
				}
			}
		}
		if ($status == false){
			$this->dao->rollback();
			return array('error' => true, 'message' => '操作失败'  , 'data' => array());			
		}else{
			$this->dao->commit();
		}
		return $lastid;
	}
	public function edit($info){
		$info['begintime'] = empty($info['begintime']) ? time() : strtotime($info['begintime']);
		$info['endtime'] = empty($info['endtime']) ? '0' : strtotime($info['endtime']);
		$info['spacing'] = abs(intval($info['spacing']));
		$status = true;
		$infosql = "update ".$this->tableName." set vote_name = '".$info['vote_name']."',begintime = ".$info['begintime'].",endtime = ".$info['endtime'].",description = '".$info['description']."',rel=".$info['rel'].",spacing=".$info['spacing']." where id = ".$info['infoid'];
		$this->dao->execute($infosql);
		$this->dao->begin();
		foreach ($info['vote'] as $one){
			if (empty($one['listid'])){
				$savesql = "insert into ".$this->listTable."(infoid,type,title) values(".$info['infoid'].",".$one['type'].",'".$one['title']."')";
				$votelist = $this->dao->execute($savesql);
				$lastid = $this->dao->lastInsertId();
				if ($votelist === false){
					$status = false;
				}else{
					$voptions = explode(',',$one['voptions']);
					foreach ($voptions as $v){
						$iosql = "insert into ".$this->optionTable."(listid,title)values($lastid,'$v')";
						$iors = $this->dao->execute($iosql);
						if (false === $iors){
							$status = false;
							break;
						}
					}					
				}
			}else{
				$savesql = "update ".$this->listTable." set type= ".$one['type'].",title='".$one['title']."' where id = ".$one['listid'];
				$this->dao->execute($savesql);
				$opsql = "select * from ".$this->optionTable." where listid = ".$one['listid'];
				$rel = $this->dao->getAll($opsql);
				$count = count($rel);//原来的选项数目
				$sub = explode(',',$one['voptions']);
				if ($count == count($sub)){
					for ($i=0;$i<$count;$i++){
						$upsql = "update ".$this->optionTable." set title = '".$sub[$i]."' where oid = ".$rel[$i]['oid'];
						$upres = $this->dao->execute($upsql);
						if ($upres === false){
							$status = false;
							break;
						}
					}
				}else {
					$status = false;
				}
			}
		}
		if ($status === true) {
			$this->dao->commit();
			return array('status'=>1, 'message' => '修改成功'  , 'data' => array());		
		}else{
			$this->dao->rollback();
			return array('status'=>0, 'message' => '操作失败'  , 'data' => array());		
		}
	} 
	//获取所有投票info列表,有参数则返回某一个投票信息
	public function getList($id,$info=false){
		$id = intval($id);
		if (empty($id)){
			$sql = "select id,vote_name,begintime,endtime,description,rel,spacing from ".$this->tableName." order by id desc";
		}else{
			$sql = "select a.id as infoid,b.id as listid,a.vote_name,b.type,b.title from ".$this->tableName." a left join ".$this->listTable." b on a.id = b.infoid where a.id = ".$id;
		}
		$rel['list'] = $this->dao->getAll($sql);
		if ($info === true){
			$sql = "select * from ".$this->tableName." where id = ".$id;
			$rel['info'] = $this->dao->getRow($sql);
		}
		return $rel;
	}
	//根据投票投票小id获取选项
	public function getoption($oid){
		$sql = "select oid,listid,title from ".$this->optionTable." where listid = ".$oid;
		return $this->dao->getAll($sql);
	}
	//投票记录
	public function voteadd($arr=array(),$infoid){
		$status = true;
		$this->dao->begin();
		if (!empty($arr)){
			$clientip =  get_client_ip();
			$s = "select * from ".$this->userInfo." where voinfoid = $infoid and createip = '$clientip'";
			$r = $this->dao->execute($s);
			if (empty($r)){
				$sql = "insert into ".$this->userInfo."(voinfoid,createtime,createip) values($infoid,".time().",'$clientip')";
				$rel = $this->dao->execute($sql);
				if (false === $rel){
					$status = false;
				}else{
					$lastid = $this->dao->lastInsertId();
					foreach ($arr as $one){
						$sql = "insert into ".$this->usetList."(uiid,listid,voteid)values($lastid,".$one[0].",'".$one[1]."')";
						$rs = $this->dao->execute($sql);
						if (false === $rs){
							$status = false;break;
						}
					}
				}
			}else{
				$status = false;
			}
			if ($status === true){
				$this->dao->commit();
				return array('status'=>1,'message'=>'提交成功');
			}else{
				$this->dao->rollback();
				return array('status'=>0,'message'=>'提交失败');
			}
		}
	}
}
?>
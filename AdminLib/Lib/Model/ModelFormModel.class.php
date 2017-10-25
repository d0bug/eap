<?php

class ModelFormModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'model_forms';
		$this->modelInfo = 'model_info';
		$this->modelData = 'model_data';
		$this->modelAttr = 'model_attributes';
		$this->attr_relation = 'model_attr_relations';
		$this->dao2 = Dao::getDao('MSSQL_CONN');
		$this->bsStudent = 'BS_Student';
	}

	public function get_moduleList(){
		return $this->dao->getAll("SELECT `id`,`name`,`channel`,`instime` FROM ".$this->modelInfo." ORDER BY `id` DESC");
	}

	public function getUsedCount($mid){
		return $this->dao->getOne("SELECT count(*) FROM ".$this->modelData." WHERE `mid`='$mid'");
	}


	public function get_dataList($condition='',$currentPage=1, $pageSize=20){
		$count = $this->get_dataCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT `id`,`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`,`instime`,`attrname`  FROM ' . $this->modelData . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .=  ' AND '.$condition;
		}
		$order = ' ORDER BY id DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}
	
	public function get_dataListAll($condition=''){
		$strQuery = 'SELECT `id`,`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`,`instime`,`attrname`  FROM ' . $this->modelData . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .=  ' AND '.$condition;
		}
		$strQuery .= ' ORDER BY id DESC';
		return $this->dao->getAll($strQuery);
	}
	
	
	public function get_mname_by_mid($mid){
		return $this->dao->getOne("SELECT `name` FROM ".$this->modelInfo." WHERE id = '$mid'");
	}
	
	
	public function get_dataCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->modelData . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}
	

	public function update_moduleInfo($name,$channel,$mid){
		if($mid){//更新
			$this->dao->execute("UPDATE ".$this->modelInfo." SET `name` = ".$this->dao->quote($name).",`channel` = '$channel',`updtime`='".time()."' WHERE id = '$mid'");
		}else{//插入
			$this->dao->execute("INSERT INTO ".$this->modelInfo." (name,channel,instime) VALUES(".$this->dao->quote($name).",'$channel','".time()."')");
		}
		if($this->dao->affectRows()){
			if(!$mid){
				$_SESSION['MID'] = $this->dao->getOne("SELECT id FROM ".$this->modelInfo." ORDER BY id DESC LIMIT 1");
			}
			return true;
		}
		return false;
	}

	public function save_moduleForm($operate, $arr){
		if($operate == 'UPDATE'){
			$sql = "UPDATE ".$this->tableName." SET `title2`=".$this->dao->quote($arr['title2']).", `display`='$arr[display]' , `required`='$arr[required]', `cate`='$arr[cate]',`updtime`='".time()."' ";
			if(!empty($arr['remark'])){
				$sql .= ",`remark` = '$arr[remark]' ";
			}
			$sql .= " WHERE `mid`='$arr[mid]' and `titleid`='$arr[titleid]'";
		}else if($operate == 'INSERT'){
			if(!$arr['remark']){
				$arr['remark'] = NULL;
			}
			$sql = "INSERT INTO ".$this->tableName." (`titleid`, `title2`, `display`,`required`, `cate`, `remark`, `mid`) VALUES('$arr[titleid]',".$this->dao->quote($arr['title2']).",'$arr[display]','$arr[required]','$arr[cate]','$arr[remark]','$arr[mid]')";

		}
		
		$this->dao->execute($sql);
		if($this->dao->affectRows()){
			if($operate == 'INSERT'){
				$_SESSION['MID'] = $this->dao->getOne("SELECT `id` FROM ".$this->modelInfo." ORDER BY id DESC LIMIT 1");
			}
			return true;
		}
		return false;
	}


	public function getModuleCount($mid){
		return $this->dao->getOne("SELECT COUNT(*) FROM ".$this->modelInfo." WHERE id = '$mid'");
	}

	public function save_moduleInfo_step2($arr,$mid){
		$this->dao->execute("UPDATE ".$this->modelInfo." SET `display`='$arr[display]',`words`=".$this->dao->quote($arr['words']).", `limitshow`='$arr[limitshow]', `limited`=".$this->dao->quote($arr['limited']).", `message`='$arr[message]', `messagetext`=".$this->dao->quote($arr['messagetext']).",`updtime`='".time()."',`isgoldlimit`='$arr[isgoldlimit]' WHERE id = '$mid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function get_moduleInfo_by_mid($mid){
		return $this->dao->getRow("SELECT * FROM ".$this->modelInfo." WHERE `id` = '$mid'");
	}

	public function get_moduleFormInfo_by_mid($mid){
		return $this->dao->getAll("SELECT * FROM ".$this->tableName." WHERE mid = '$mid'");
	}

	/**查找属于本次预约模块最大值****/
	public function get_Titleid($mid){
		return $this->dao->getOne("SELECT max(titleid) FROM ".$this->tableName." WHERE mid = '$mid'");
	}
	
    /***添加forms表且获取自增ID**/
    public function add_model_forms($titleid,$v,$mid){
    	$this->dao->execute("insert into ".$this->tableName." SET
			titleid = '".$titleid."',title2 = '".$v."',mid='".$mid."',is_attribute='1'"); 
		if($this->dao->affectRows()){
			$new_insert_id = $this->dao->getOne("SELECT id FROM ".$this->tableName." ORDER BY id DESC LIMIT 1 ");
			return $new_insert_id;
		}
		return false;
    }
    
    /***添加属性表**/
    public function add_model_attributes($fid,$data,$limitnum,$mid){
    	$time=time();
  		$this->dao->execute("insert into ".$this->modelAttr." SET
		name = '".$data."',limitnum = '".$limitnum."',fid = '".$fid."',mid='".$mid."',ctime='".$time."'"); 
		if($this->dao->affectRows()){
			return true;
		}
		return false;	
    }
	
    /***查找属性对应关系表**/
    public function get_AttriList($mid){
  		$strQuery = "SELECT f.title2 as title2,f.id as fid
				 FROM ".$this->tableName." f
				 WHERE f.mid='$mid' and is_attribute=1 order by f.id asc";
    	$r=	$this->dao->getAll($strQuery);
	   	$data = array();
		foreach($r as $s){
		    $fid= $s['fid']; 
		    $str = "SELECT a.id as aid,a.name,a.limitnum as limitnum
				 FROM ".$this->modelAttr." a
				 WHERE a.mid='$mid' and a.fid= '$fid' order by a.id asc";
			$m =$this->dao->getAll($str);
			$data[$s['fid']]= array(
		    'fid' =>$s['fid'],
		    'title'=>$s['title2'],
		    'attribute'=>$m,
			);
		}
		return $data;
    }
    
 /****添加对应属性关系表*******/
    public function add_model_attr_relations($main_id, $fid, $data_id){
  		$time=time();
  		$this->dao->execute("insert into ".$this->attr_relation." SET
		main_id = '".$main_id."',fid = '".$fid."',data_id = '".$data_id."',ctime='".$time."'"); 
		if($this->dao->affectRows()){
			return true;
		}
		return false;
    }  
    
   
	/*在表单中查找场次属性***/
	public function get_moduleFormAttribute_by_mid($mid){
  		$strQuery = "SELECT f.title2 as title2,f.id as fid
				 FROM ".$this->tableName." f
				 WHERE f.mid='$mid' and is_attribute=1 order by f.id asc limit 1";
    	$r=	$this->dao->getAll($strQuery);
 	   	$data = array();
 	   	$m = array();
		foreach($r as $s){
			$fid= $s['fid']; 
	    	$str = "SELECT a.id,a.name,a.limitnum as limitnum
				 FROM ".$this->modelAttr." a
				 WHERE a.mid='$mid' and a.fid= '$fid' order by a.id asc";
			$m =$this->dao->getAll($str);
			$data[$s['fid']]= array(
    		'fid' =>$s['fid'],
    		'title'=>$s['title2'],
    		'name'=>$m,
			);
		}
		return $data;

	}
	
	
     /***第四步生成表单查找属性对应关系表**/
   	 public function get_ajax_attr($mid=0,$main_id=0){
   	 	$where = '';
   		$strQuery = " SELECT r.fid, f.title2
				FROM ".$this->attr_relation." AS r, ".$this->tableName." AS f
				WHERE r.fid = f.id	AND r.main_id ='$main_id' and f.mid='$mid'
				GROUP BY r.fid";
    	$arr1=$this->dao->getAll($strQuery);
 	   	$data = array();
 	   	$query = "SELECT r.id as rid,r.main_id,r.fid as fid,r.data_id,
 	   				a.name as aname,a.limitnum as limitnum
				FROM ".$this->attr_relation." AS r
				LEFT JOIN ".$this->modelAttr." AS a ON r.data_id = a.id
				WHERE r.main_id ='$main_id' and a.mid='$mid'";
		$arr2=$this->dao->getAll($query);
	   	 foreach($arr1 as $k1=>$v1) {
	  		  foreach($arr2 as $v2) {
		       if ($v2['fid'] == $v1['fid']) {
		          $v1['aname'][$v2['data_id']] = $v2;
		      	 }
	    	}
	   		$data[$k1] = $v1;
		}
		return $data;
   }	
	
	/****获取场次属性名称****/
	public function get_attrname($mid){
		return $this->dao->getAll("SELECT id,title2 FROM ".$this->tableName." WHERE  is_attribute=1 AND `mid`='$mid' order by id asc");	
	}
	
	public function read($filename,$encode='utf-8')
    {
    	$arrTeacherData = array();
		import("ORG.Util.PhpExcel");
		$objReader = new PHPExcel_Reader_Excel5(); //use excel2007
		$objPHPExcel = $objReader->load($filename); //指定的文件
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$columnArr = array('A','B','C','D','E','F','G','H','I','J','K');
		$defaultColumnCount = count($columnArr);
		for($j=1;$j<=$highestRow;$j++){
			$code = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//第一列学号
			//echo $code.'<br>';
			for ($i= 1;$i<=$defaultColumnCount-1;$i++){
				$arrTeacherData[$j-1][] = $objPHPExcel->getActiveSheet()->getCell($columnArr[$i-1].$j)->getValue();
				if($columnArr[$i-1] == $highestColumn){
					break;
				}
			}
		}

		return $arrTeacherData;
     
    }	
	
	public function saveFormData($arr,$mid){
		//echo "INSERT INTO ".$this->modelData." (`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`,`mid`,`attrname`,`instime`) VALUES(".$this->dao->quote($arr['name']).",'$arr[sex]',".$this->dao->quote($arr['school']).",'$arr[grade]','$arr[dept]',".$this->dao->quote($arr['email']).",".$this->dao->quote($arr['tel']).",".$this->dao->quote($arr['message']).",'$mid','$arr[attr]','".time()."')";exit;
		$this->dao->execute("INSERT INTO ".$this->modelData." (`name`,`sex`,`school`,`grade`,`dept`,`email`,`phone`,`message`,`mid`,`attrname`,`instime`) VALUES(".$this->dao->quote($arr['name']).",'$arr[sex]',".$this->dao->quote($arr['school']).",'$arr[grade]','$arr[dept]',".$this->dao->quote($arr['email']).",".$this->dao->quote($arr['tel']).",".$this->dao->quote($arr['message']).",'$mid','$arr[attr]','".time()."')");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_formData_by_info($arr,$mid){
		return $this->dao->getOne("SELECT id FROM ".$this->modelData." WHERE `name`=".$this->dao->quote($arr['name'])." AND `sex`='$arr[sex]' AND `grade`='$arr[grade]' AND `dept`='$arr[dept]' AND `email`=".$this->dao->quote($arr['email'])." AND `phone`=".$this->dao->quote($arr['tel'])." AND `mid`='$mid'");
	}
	
	
	public function get_displayItem($mid){
		return $this->dao->getAll("SELECT titleid FROM ".$this->tableName." WHERE mid = '$mid' AND `display` = '1'");
	}
	
	public function deleteDataById($id_str,$mid){
		$this->dao->execute("DELETE FROM ".$this->modelData."  WHERE id IN ($id_str) AND mid = '$mid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	
	
	//验证当前报名/预约用户是否为金卡会员
	public function checkUserIsbGoldCard($userName){
		//echo "SELECT [bGoldCard] FROM ".$this->bsStudent." WHERE [sName] = '$userName'";exit;
		return $this->dao2->getAll("SELECT [bGoldCard] FROM ".$this->bsStudent." WHERE [sName] = '$userName'");
	}
}
?>
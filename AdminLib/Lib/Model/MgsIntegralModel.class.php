<?php
class MgsIntegralModel extends Model {	
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->operator = User::getLoginUser();  //获取操作者
		$this->tableName = 'MGS_Integral';
		$this->tableBsStu = 'BS_Student'; 
		$this->tableNetClassHistory = 'MGS_netClassIntegralHistory';   //网课积分记录表
		$this->tableJifenHistory = 'GS_Jifen_History';   //积分卡充值记录
	}
	//获取学员的信息  bIsValid = 1 表示有效的学员 
	public function get_studentInfo($saliascode){
		return $this->dao->getAll("SELECT sCode, sName, sAliasCode, sMobile FROM " . $this->tableBsStu . " WHERE sAliasCode ='".$saliascode."' AND bIsValid = 1");
	}
	
	
	
	//添加 网课积分
	public function add_Integral($stuArr,$wangkejifen){
		if($this->get_oneCount("stuCode = '$stuArr[scode]'")){  //存在就更新
			$this->dao->execute("UPDATE ".$this->tableName." SET [netClassIntegral] = [netClassIntegral] + ".$wangkejifen.", [totalIntegral] = [totalIntegral] + ".$wangkejifen."  WHERE [stuCode] = '".$stuArr['scode']."'");
		}else{  												//不存在就插入
			$this->dao->execute("INSERT INTO ".$this->tableName." ([stuCode], [cardIntegral], [lessonIntegral], [netClassIntegral], [totalIntegral]) VALUES('".$stuArr['scode']."','0', '0', '".$wangkejifen."', '".$wangkejifen."')");	
		}
			
		//插入到操作记录表中
		$userKey = $this->operator->getUserKey();
		$operator = trim(str_replace('', '',$this->dao->quote($userKey)));
		$remark = $wangkejifen >= 0 ? '执行了追加操作' : '执行了减少操作'; 
		$this->dao->execute("INSERT INTO ".$this->tableNetClassHistory." ([stuCode], [integralValue], [operator], [time], [remark]) VALUES('".$stuArr['scode']."', '".$wangkejifen."', ".$this->dao->quote($userKey).", '".time()."', '".$remark."')");
			
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
		
	public function get_oneCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	
	
	//获取此会员的各种积分操作记录情况
	public function get_integralList($condition='', $currentPage=1, $pageSize=10){
		$count = $this->get_integralCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strSql ="select * from ((select  'b' tableName, stucode, integralValue,  time, operator, '' cardvalue, ''usetime  from mgs_netclassintegralhistory where stucode='".$condition."')
union all
(select  'a' tableName, stucode, cardvalue, useTime, '' integralValue, '' time, '' operator from gs_jifen_history where stucode='".$condition."')) tbl where 1=1 ";
		if($condition){
			$strSql .= "AND stucode='".$condition."' ";	
		}
		$order ='ORDER BY [time] DESC';
		//echo $strSql;
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}
	//获取满足条件的记录总条数  在分页查询时用
	public function get_integralCount($condition='') {
		$strQuery ="select COUNT(1) from ((select  'b' tableName, stucode, integralValue,  time, operator, '' cardvalue, ''usetime  from mgs_netclassintegralhistory where stucode='".$condition."')
union all
(select  'a' tableName, stucode, cardvalue, useTime, '' integralValue, '' time, '' operator from gs_jifen_history where stucode='".$condition."')) tbl where 1=1 ";
		if($condition){
			$strQuery .= "AND stucode='".$condition."' ";	
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	//获取学员的网课积分情况 
	public function get_netClass($stuCode){
		return $this->dao->getAll("SELECT top 1 cardIntegral, lessonIntegral, netClassIntegral, totalIntegral FROM " . $this->tableName . " WHERE stuCode ='".$stuCode."'");
	}
	
	
	#读取Excel文件的内容
	public function readExcel($filename,$encode='utf-8'){
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
	
	
	//获取学员的信息  bIsValid = 1 表示有效的学员  2012 12 05 为批量添加网课赠分使用
	public function get_studentInfo_s($sCode){
		return $this->dao->getAll("SELECT sCode, sName, sAliasCode, sMobile FROM " . $this->tableBsStu . " WHERE sCode ='".$sCode."' AND bIsValid = 1");
	}

}
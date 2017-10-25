<?php
import('COM.Dao.Dao');
class GClass {
	public static function getSemesterCondition($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$condition = ' AND (0=1';
    	$year = $args['year'];
    	if($args['semester']) {
    		foreach ($args['semester'] as $semester) {
    			$condition .= ' OR sclasscode LIKE ' . $dao->quote($year . $semester . '%');
    		}
    	} else {
    		$condition .= ' OR sclasscode LIKE ' . $dao->quote($year . '%');
    	}
    	$condition .= ')';
    	return $condition;
    }
    
    public function getSignupUrl($classCode) {
    	if($classCode) {
    		return 'http://student.gaosiedu.com/Order/ShoppingCart.aspx?cls=' . $classCode;
    	} else {
    		return 'javascript:void(0)';
    	}
    }
    
    public static function getSubjectCondition($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	if($args['sbjCodes']) {
    		$sbjCodes = explode(',', str_replace(' ', '', $args['sbjCodes']));
    		return " AND sdeptcode IN ('" . implode("','", $sbjCodes) . "')";
    	}
    	return '';
    }
    
    public static function getPrjCondition($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	if ($args['prjCodes']) {
    		return " AND sprojectcode IN ('" . implode("','", $args['prjCodes']) . "')";
    	}
    	return '';
    }
    
    
    public static function getCtCondition($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	if ($args['ctCodes']) {
    		return " AND sprojectcode IN ('" . implode("','", $args['ctCodes']) . "')";
    	}
    	return '';
    }
    
    public static function getClassCount($args) {
    	
    }
    
    public static function getClassList($args) {
    	
    }
    
    public static function getUniqClassByLevel($clsLevel, $codePre, $xuebuke) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	import('COM.Gaosi.Subject');
    	list($xuebu, $xueke) = Subject::getXuebukeInfo($xuebuke);
    	$strQuery = 'SELECT distinct sclassname,sclasstypecode 
    				 FROM viewclassforweb 
    				 WHERE nlevel=' . abs($clsLevel) . '
    				   AND sclasscode LIKE ' . $dao->quote($codePre . '%') . '
    				   AND sclasstypecode IN (
    				   	  SELECT scode FROM bs_classtype
    				   	  WHERE nxueke=' . abs($xueke) . '
    				   	    AND nxuebu=' . abs($xuebu) . '
    				   )';
    	$clsList = $dao->getAll($strQuery);
    	return $clsList;
    }
    
    public static function getSuggestClass($classInfo, $areaCodeArray) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$clsCodePre = $classInfo['class_codepre'];
    	$classTypeCode = $classInfo['class_type'];
    	$strQuery = 'SELECT TOP 5 * FROM viewclassforweb
    				 WHERE sclasscode LIKE ' . $dao->quote($clsCodePre . '%') . '
    				   AND sclasstypecode=' . $dao->quote($classTypeCode);
    	if($areaCodeArray) {
    		$areas = array();
    		foreach ($areaCodeArray as $area) {
    			$areas[] = $dao->quote($area['area_code']);
    		}
    		$strQuery .= ' AND sareacode IN (' . implode(',', $areas) . ')';
    	}
    	$strQuery .= ' ORDER BY newid()';
    	$classList = $dao->getAll($strQuery);
    	foreach ($classList as $key=>$class) {
    		$classList[$key]['signup_url'] = self::getSignupUrl($class['sclasscode']);
    		$classList[$key]['teacherInfo'] = self::getClassTeacherInfo($class);
    	}
    	return $classList;
    }
    
    public static function getClassTeacherInfo($class) {
    	import('COM.Gaosi.Teacher');
    	$clsTeacherInfo = array('teacher_link'=>$class['sprintteachers']);
    	if($class['sdefaultteachercode']) {
    		$teacher = Teacher::getTeacher($class['sdefaultteachercode']);
    		$teacherInfo = $teacher->getInformation();
    		if($teacherInfo['teacherPic']) {
    			$clsTeacherInfo['teacher_img'] = $teacherInfo['teacherPic'];
    			$clsTeacherInfo['img_link'] = $teacherInfo['teacherLink'];
    		}
    		
    		$clsTeacherInfo['teacher_link'] = str_replace($teacherInfo['teacherName'], '<a href="' . $teacherInfo['teacherLink'] . '" target="_blank">' . $teacherInfo['teacherName'] . '</a>', $clsTeacherInfo['teacher_link']);
    	}
    	if($class['sdefaultteachercode2']) {
    		$teacher = Teacher::getTeacher($class['sdefaultteachercode2']);
    		$teacherInfo = $teacher->getInformation();
    		if(false == $clsTeacherInfo['teacher_img']) {
    			$clsTeacherInfo['teacher_img'] = $teacherInfo['teacherPic'];
    			$clsTeacherInfo['img_link'] = $teacherInfo['teacherLink'];
    		}
    		$clsTeacherInfo['teacher_link'] = str_replace($teacherInfo['teacherName'], '<a href="' . $teacherInfo['teacherLink'] . '" target="_blank">' . $teacherInfo['teacherName'] . '</a>', $clsTeacherInfo['teacher_link']);
    	}
    	if($class['sdefaultteachercode3']) {
    		$teacher = Teacher::getTeacher($class['sdefaultteachercode3']);
    		$teacherInfo = $teacher->getInformation();
    		if(false == $clsTeacherInfo['teacher_img']) {
    			$clsTeacherInfo['teacher_img'] = $teacherInfo['teacherPic'];
    			$clsTeacherInfo['img_link'] = $teacherInfo['teacherLink'];
    		}
    		$clsTeacherInfo['teacher_link'] = str_replace($teacherInfo['teacherName'], '<a href="' . $teacherInfo['teacherLink'] . '" target="_blank">' . $teacherInfo['teacherName'] . '</a>', $clsTeacherInfo['teacher_link']);
    	}
    	
    	return $clsTeacherInfo;
    }
    
    /**
     * 竞赛考试分班信息
     */
    public static function getExamClassInfo($clsArray) {
    	$clsText = array();
		$clsLinks = array();
		$clsSearchLinks = array();
		$infoHrefs = array();
		$searchHrefs = array();
		$clsInfoLinks = array();
		$clsDesc = '';
		$clsDescs = array();
		foreach ($clsArray as $cls) {
			$searchHrefs[] = $cls['class_search_link'];
			$infoHrefs[] = $cls['class_info_link'];
			$clsText[] = $cls['class_name'];
			$clsLinks[] = '<a class="classLink" href="#rule_' . md5($cls['id']) . '">' . $cls['class_name'] . '</a>';
			$clsSearchLinks[] = '<a href="' . $cls['class_search_link'] . '" target="_blank">搜课报班</a>';
			$clsInfoLinks[] = '<a href="' . $cls['class_info_link'] . '" target="_blank">班型介绍</a>';
			$clsDescs[] = '<div id="rule_' . md5($cls['id']) . '" style="display:none" class="classDesc">' . $cls['class_desc'] . '<span style="infoLink"><a href="' . $cls['class_info_link'] . '" target="_blank">班型介绍</a></span></div>';
			if(false == $clsDesc) {
				$clsDesc = $cls['class_desc'];
			}
		}
		$clsCfg = array('text'=>implode("\n", $clsText),
						'link'=>implode('<br />', $clsLinks),
						'searchLink'=> implode('&nbsp;,&nbsp;', $clsSearchLinks),
						'searchLinks'=>$clsSearchLinks,
						'infoLink'=>$clsInfoLinks,
						'clsNames'=>$clsText,
						'searchHrefs'=>$searchHrefs,
						'infoHrefs'=>$infoHrefs,
						'infoDiv'=>implode('', $clsDescs),
						'clsDesc'=>$clsDesc);
		return $clsCfg;
    }
    
    public static function getClsCardKey($classInfo, $stuInfo, $examInfo) {
    	if($classInfo && $classInfo['text']){
	    	import('ORG.Crypt.Xxtea');
	    	import('COM.Dao.Dao');
	    	$sAliasCode = $stuInfo['saliascode'];
	    	return Xxtea::encrypt($stuInfo['sname'] . '|' . $sAliasCode . '|' . $stuInfo['exam_code'] . '|' . $examInfo['exam_caption'] . '|' . $classInfo['text'], C('ENCRYPT_KEY'));
    	}
    	return '';
    }
    
    public static function getClassInfoByCode($classCode) {
    	$dao = Dao::getDao();
    	$strQuery = 'SELECT * FROM viewclassforweb WHERE scode=' . $dao->quote($classCode);
    	$classInfo = $dao->getRow($strQuery);
    	if($classInfo) {
    		$classInfo['sbegindate'] = date('Y-m-d', Dao::msStrtotime($classInfo['dtbegindate']));
    		$classInfo['senddate'] = date('Y-m-d', Dao::msStrtotime($classInfo['dtenddate']));
    	}
    	return $classInfo;
    }
    
};
?>
<?php
import('COM.Dao.Dao');
class Chart{
	private static $dao = null;
	private static $chartTable = 'ex_chart_json';
	private static $chartJs = 'highcharts-convert.js';
	
	
	private static function getDao() {
		if (null  === self::$dao) {
			self::$dao = Dao::getDao('MSSQL_CONN');
		}
		return self::$dao;
	}
	
	public static function getQuesChartJson($stuCode, $paperId, &$analyData=array(), $quesChartArgs=array()) {
		$dao = self::getDao();
		$chartType = 'line';
		$background = 'rgba(255,255,255,0)';
		$width = 780;
		$height = 240;
		$lineColors = array();
		if($radarChartArgs['lineColors']) $lineColors = $quesChartArgs['lineColors'];
		if($quesChartArgs['background']) $background = $quesChartArgs['background'];
		if($quesChartArgs['width']) $width = $quesChartArgs['width'];
		if($quesChartArgs['height']) $height = $quesChartArgs['height'];
		$stuCode = SysUtil::safeString($stuCode);
		$examId = $analyData['paperInfo']['exam_id'];
		$paperId = abs($paperId);
		
		$chartKey = md5('quesChart_' . $stuCode . '_' . $paperId . '_' . serialize($quesChartArgs));
		$strQuery = 'SELECT * FROM ' . self::$chartTable . '
					 WHERE chart_type=' . $dao->quote($chartType) . '
					   AND paper_id=' . abs($paperId) . '
					   AND chart_key=' . $dao->quote($chartKey) . '
					   AND stu_code=' . $dao->quote($stuCode);
		$jsonInfo = $dao->getRow($strQuery);
		if($jsonInfo && $jsonInfo['json_expires'] < time()) {
			return $jsonInfo['json_text'];
		}
		if($analyData) {
			$paperInfo = $analyData['quesArray']['real'];
			$quesNames = array();
			$quesRatios = array();
			$stuRatios = array();
			foreach ($paperInfo as $partId=>$quesArray) {
				foreach ($quesArray as $ques) {
					$quesKey = md5($ques['ques_id']);
					$quesNames[] = $ques['ques_seq'] . '小题';
					$quesRatios[] = floatval(sprintf('%.2f', $analyData['stuScore'][$paperId]['ques_ratios'][$quesKey]['ques_ratio']));
					$stuRatios[] = floatval(sprintf('%.2f', $analyData['stuScore'][$paperId]['score_info']['ques_score_' . $ques['ques_seq']] / $ques['ques_score'] * 100));
				}
			}
			
			$jsonData = array('xAxis'=>array('categories'=>$quesNames, 'labels'=>array('rotation'=>300, 'y'=>20)),
							  'yAxis'=>array('title'=>array('text'=>'得分率'), 
							  				 'allowDecimals'=>false,
							  				 'min'=>0,
							  				 'max'=>100,
							  				 'tickPixelInterval'=>20),
							  'tooltip'=>array('shared'=>true,
							  				   'valueSuffix'=>'%'),
							  'series'=>array(array('name'=>'平均得分率', 'color'=>$lineColors[0] ? $lineColors[0] : null, 'data'=>$quesRatios),
							  				  array('name'=>'个人得分率', 'color'=>$lineColors[1] ? $lineColors[1] : null, 'data'=>$stuRatios)),
							  'chart'=>array('renderTo'=>'heartbeat',
							  				 'type'=>'line',
							  				 'backgroundColor'=>$background,
							  				 'width'=>$width, 
							  				 'height'=>$height),
							  'title'=>array('text'=>'试卷成绩心跳图'),);
			$jsonStr = json_encode($jsonData);
			$expires = abs(time() + 600);
			if($jsonInfo) {
				$strQuery = 'UPDATE ' . self::$chartTable . '
							 SET json_expires=' . $expires . ',
							 	 json_text=' . $dao->quote($jsonStr) . '
							 WHERE id=' . $dao->quote($jsonInfo['id']);
			} else {
				$id = SysUtil::uuid();
				$strQuery = 'INSERT INTO ' . self::$chartTable . '
							 (id,exam_id,paper_id,stu_code,chart_type,chart_key,json_expires,json_text)
							 VALUES (' . $dao->quote($id) . ',
							 		 ' . abs($examId) . ',
							 		 ' . abs($paperId) . ',
							 		 ' . $dao->quote($stuCode) . ',
							 		 ' . $dao->quote($chartType) . ',
							 		 ' . $dao->quote($chartKey) . ',
							 		 ' . abs($expires) . ',
							 		 ' . $dao->quote($jsonStr) . ')';
			}
			$dao->execute($strQuery);
			return $jsonStr;
		}
		return $jsonInfo['json_text'];
	}
	
	public static function getRadarChartJson($stuCode, $paperId, &$analyData=array(), $radarChartArgs = array()) {
		$chartType = 'radar';
		$lineType = 'line';
		$height= 260;
		$width = 260;
		$background = 'rgba(255,255,255,0)';
		$lineColors = array();
		if($radarChartArgs['lineColors']) $lineColors = $radarChartArgs['lineColors'];
		if($radarChartArgs) {
			if($radarChartArgs['type']) $lineType = $radarChartArgs['type'];
			if($radarChartArgs['width']) $width = $radarChartArgs['width'];
			if($radarChartArgs['height']) $height = $radarChartArgs['height'];
			if($radarChartArgs['background']) $background = $radarChartArgs['background'];
		}
		$chartKey = md5($chartType . '_' . $lineType . '_' . $width . '_' . $height . '_' . $background);
		$dao = self::getDao();
		$stuCode = SysUtil::safeString($stuCode);
		$examId = $analyData['paperInfo']['exam_id'];
		$paperId = abs($paperId);
		$strQuery = 'SELECT * FROM ' . self::$chartTable . '
					 WHERE chart_type=' . $dao->quote($chartType) . '
					   AND chart_key=' . $dao->quote($chartKey) . '
					   AND paper_id=' . abs($paperId) . '
					   AND stu_code=' . $dao->quote($stuCode);
		$jsonInfo = $dao->getRow($strQuery);
		if($jsonInfo && $jsonInfo['json_expires'] < time()) {
			return $jsonInfo['json_text'];
		}
		if($analyData) {
			$moduleAnaly = $analyData['moduleAnaly'];
			$moduleNames = array();
			$moduleRatios = array();
			$stuRatios = array();
			foreach ($moduleAnaly as $moduleCode=>$moduleCfg) {
				$moduleNames[] = $moduleCfg['module_caption'];
				$moduleRatios[] = floatval(sprintf('%.2f', $moduleCfg['module_ratio']));
				$stuRatios[] = floatval(sprintf('%.2f', $moduleCfg['stu_ratio']));
			}
			
			$jsonData = array('xAxis'=>array('categories'=>$moduleNames, 
											 'lineWidth'=>0,
											 'tickmarkPlacement'=>"on",
											 'labels'=>array('style'=>array('fontSize'=>'12px'))),
							  'yAxis'=>array('lineWidth'=>0, 
							  				 'allowDecimals'=>false,
							  				 'min'=>0,
							  				 'max'=>100,
							  				 'title'=>array('text'=>$lineType == 'line'? null : '模块得分率'),
							  				 'tickPixelInterval'=>20),
							  'tooltip'=>array('shared'=>true,
							  				   'valueSuffix'=>'%'),
							  'series'=>array(array('name'=>'平均得分率', 'color'=>$lineColors[0] ? $lineColors[0] : null, 'data'=>$moduleRatios),
							  				  array('name'=>'个人得分率', 'color'=>$lineColors[1] ? $lineColors[1] : null, 'data'=>$stuRatios)),
							  'chart'=>array('renderTo'=>'radar',
							  				 'polar'=>$lineType == 'line',
							  				 'type'=>$lineType,
							  				 'backgroundColor'=>$background,
							  				 'width'=>$width, 
							  				 'height'=>$height),
							  'title'=>array('text'=>null),
							  'pane'=>array('size'=>'70%'),
							  );
			$jsonStr = json_encode($jsonData);
			$expires = abs(time() + 600);
			if($jsonInfo) {
				$strQuery = 'UPDATE ' . self::$chartTable . '
							 SET json_expires=' . $expires . ',
							 	 json_text=' . $dao->quote($jsonStr) . '
							 WHERE id=' . $dao->quote($jsonInfo['id']);
			} else {
				$id = SysUtil::uuid();
				$strQuery = 'INSERT INTO ' . self::$chartTable . '
							 (id,exam_id,paper_id,stu_code,chart_type,chart_key,json_expires,json_text)
							 VALUES (' . $dao->quote($id) . ',
							 		 ' . abs($examId) . ',
							 		 ' . abs($paperId) . ',
							 		 ' . $dao->quote($stuCode) . ',
							 		 ' . $dao->quote($chartType) . ',
							 		 ' . $dao->quote($chartKey) . ',
							 		 ' . abs($expires) . ',
							 		 ' . $dao->quote($jsonStr) . ')';
			}
			$dao->execute($strQuery);
			return $jsonStr;
		}
		return $jsonInfo['json_text'];
	}
	
	private static function getChartImg($jsonStr, $imgFile) {
		$jsonStr = preg_replace('/\"#[a-f0-9]{6}\"/i', '"#FFFFFF"', $jsonStr);
		false == defined('PHANTOMJS_PATH') && define ('PHANTOMJS_PATH', C('PHANTOMJS_PATH'));
		$phantomScript = C('PHANTOMJS_SCRIPT') . '/' . self::$chartJs;
        $outputDir = C('REPORT_OUTPUT_DIR');
        $imgFile = $outputDir . '/' . $imgFile;
        if(file_exists($imgFile)) {
        	$time = filemtime($imgFile);
        	if(time() - $time < 3600) {
        		return ;
        	}
        }
        $imgDir = dirname($imgFile);
        if(false == is_dir($imgDir)) {
        	@mkdir($imgDir, 0777, true);
        }
        $jsonFile = $imgFile . '.json';
        file_put_contents($jsonFile, $jsonStr);
        $cmd = "";
        $cmd = "$cmd -infile $jsonFile";
        $cmd = "$cmd -outfile " . $imgFile;
        
        $command = PHANTOMJS_PATH . " " . $phantomScript . " $cmd ";
        
        exec($command);
	}
	
	public static function quesChartImg($stuCode, $paperId, &$analyData=array(), $quesChartArgs=array()) {
		$jsonStr = self::getQuesChartJson($stuCode, $paperId, $analyData, $quesChartArgs);
		$img = $stuCode . '/ques-' . $paperId . '.png';
		self::getChartImg($jsonStr, $img);
		return '/chart/' . $img;
	}
	
	public static function radarChartImg($stuCode, $paperId, &$analyData=array(), $radarChartArgs=array()) {
		$jsonStr = self::getRadarChartJson($stuCode, $paperId, $analyData, $radarChartArgs);
		$img =  $stuCode . '/radar-' . $paperId . '.png';
		self::getChartImg($jsonStr, $img);
		return '/chart/' . $img;
	}
	
	public static function getStepPieChartJson($examId, $paperId, $step, $stepCfg, $pieArgs = array()) {
		$dao = self::getDao();
		$chartType = 'stepPie';
		$background = 'rgba(255,255,255,0)';
		if($pieArgs['background']) $background = $pieArgs['background'];
		$chartKey = md5($chartType . '_' . $examId . '_' . $paperId . '_' . $step . '_' . $background);
		$strQuery = 'SELECT * FROM ' . self::$chartTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND paper_id=' . abs($paperId) . '
					   AND chart_key=' . $dao->quote($chartKey) . '
					   AND chart_type=' . $dao->quote($chartType);
		$dbChartInfo = $dao->getRow($strQuery);
		if($dbChartInfo && $dbChartInfo['json_expires'] < time()) {
			return $dbChartInfo['json_text'];
		}
		
		$stepNames = array('', '低档题', '中档题', '高档题');
		$jsonData = array(
					'chart'=>array('plotBackgroundColor'=>null,
            						'plotBorderWidth'=>null,
            						'plotShadow'=>false,
            						'backgroundColor'=>$background),
            		'title'=>false,
            		'tooltip'=>array('pointFormat'=> '模块比例: <b>{point.percentage:.2f}%</b>'),
            		'plotOptions'=>array(
            				'pie'=> array('allowPointSelect'=>true,
                				  		  'cursor'=>'pointer',
                				  		  'size'=>'85%',
                				  		  'showInLegend'=> true,
                				  		  'dataLabels'=>array(
                				  		  				'align'=>'right',
                    									'enabled'=>true,
                    									'color'=> '#FFFFFF',
                    									'connectorColor'=>'#000000',
                    									'distance'=> -15,
                    									'format'=>'{point.moduleCount}/{point.totalCount}',
                    							),
                    				),),
					);
		$series = array(
						'type'=>'pie',
						'name'=>null,
						'data'=>array()
					);
		
		foreach ($stepCfg['modules'] as $moduleCode=>$moduleCfg) {
			$series['data'][] = array('name'=>$moduleCfg['caption'] , 'y'=>floatval(sprintf('%.2f', $moduleCfg['countRatio'])), 'moduleCount'=>$moduleCfg['quesCount'], 'totalCount'=>$stepCfg['quesCount']);
		}
		$jsonData['series'] = array($series);
		$chartJson = json_encode($jsonData);
		$expires = time() + 3600;
		if($dbChartInfo) {
			$strQuery = 'UPDATE ' . self::$chartTable . '
						 SET json_text=' . $dao->quote($chartJson) . ',
						 	 json_expires=' . abs($expires) . '
						 WHERE id=' . $dao->quote($dbChartInfo['id']);
		} else {
			$strQuery = 'INSERT INTO ' . self::$chartTable . '
						 (exam_id,paper_id,chart_type,chart_key,json_expires,json_text)
						 VALUES (' . abs($examId) . ', 
						 		 ' . abs($paperId) . ',
						 		 ' . $dao->quote($chartType) . ', 
						 		 ' . $dao->quote($chartKey) . ',
						 		 ' . abs($expires) . ',
						 		 ' . $dao->quote($chartJson) . ')';
		}
		$dao->execute($strQuery);
		return $chartJson;
	}
	
	public static function getStepPieChartImg($examId, $paperId, $step, $stepCfg, $pieArgs=array()) {
		$jsonStr = self::getStepPieChartJson($examId, $paperId, $step, $stepCfg, $pieArgs);
		$img =  '/stepPie/stepPie-' . $paperId . '-' . $step . '.png';
		self::getChartImg($jsonStr, $img);
		return '/chart/' . $img;
	}
}
?>
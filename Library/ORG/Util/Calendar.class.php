<?php
/**
 * 日历类
 *
 */
class Calendar {
	var $YEAR,$MONTH,$DAY,$ESP,$EY,$EM,$ED;
	var $WEEK=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
	var $_MONTH=array("01"=>"一月","02"=>"二月","03"=>"三月","04"=>"四月","05"=>"五月","06"=>"六月","07"=>"七月","08"=>"八月","09"=>"九月","10"=>"十月","11"=>"十一月","12"=>"十二月"
	);
	function __construct($year='',$month='',$day='') {
		$this->YEAR = !empty($year)?$year:date("Y");
		$this->MONTH=!empty($year)?$month:date("m");
		$this->DAY=!empty($year)?$day:date("d");
	}

	//获得月份
	function getMonth() {
		return $this->MONTH;
	}
	//设置日期
	function setDay($day) {
		$this->DAY=$day;
	}
	//获得日期
	function getDay() {
		return $this->DAY;
	}

	function dateSelect(){
		$days=array();
		for($i=0;$i<=30;$i++){//这里数字根据需要变动
			$days[]=date("Y-m-d",strtotime('+'.$i.'day'));
		}
		return $days;
	}

	function monthSelect(){
		$currentYear = date('Y');
		$monthSelectData = array();
		for($i=9;$i<13;$i++) {//往前打印4个月
			$monthSelectData[] = array('name'=>($currentYear-1).'年'.$i.'月','key'=>($currentYear-1).'_'.$i,'is_current'=>(date('Y-n')==($currentYear-1).'-'.$i)?1:0);
		}
		for($i=1;$i<13;$i++) {//打印12个月
			$monthSelectData[] = array('name'=>$currentYear.'年'.$i.'月','key'=>$currentYear.'_'.$i,'is_current'=>(date('Y-n')==$currentYear.'-'.$i)?1:0);
		}
		for($i=1;$i<13;$i++) {//往前打印12个月
			$monthSelectData[] = array('name'=>($currentYear+1).'年'.$i.'月','key'=>($currentYear+1).'_'.$i,'is_current'=>(date('Y-n')==($currentYear+1).'-'.$i)?1:0);
		}
		return $monthSelectData;
	}

	function monthData(){
		$monthData = array();
		$daysNum = date('j', strtotime("+1 month ".$this->YEAR."-".$this->MONTH."-1")-86400);
		//$daysNum = cal_days_in_month(CAL_GREGORIAN, $this->MONTH, $this->YEAR);
		for($Tmpb=1;$Tmpb<=$daysNum;$Tmpb++) {
			$monthData[$Tmpb-1]['day'] = $Tmpb;
			$monthData[$Tmpb-1]['month'] = $this->MONTH;
			$monthData[$Tmpb-1]['year'] = $this->YEAR;
			$monthData[$Tmpb-1]['week'] = $this->getWeek($this->YEAR,$this->MONTH,$Tmpb);
			$monthData[$Tmpb-1]['weekStr'] = $this->WEEK[$monthData[$Tmpb-1]['week']];
			if(strcmp($Tmpb,$this->DAY)==0) {//获得当前日期，做标记
				$monthData[$Tmpb-1]['is_current'] = 1;
			}else {
				$monthData[$Tmpb-1]['is_current'] = 0;
			}
		}
		return $monthData;
	}


	function weekSelect(){
		$weekSelect = array();
		date_default_timezone_set("Asia/Shanghai");
		$year = (int)date('Y');
		$next_year = (int)((int)date('Y')+1);
		//按给定的年份计算本年周总数
		$date = new DateTime;
		$date->setISODate($year, 53);
		$now = time();
		$weeks = max($date->format("W"),52);
		for ($temp=1;$temp<=$weeks;$temp++){
			$week = ($temp<10)?'0'.$temp:$temp;
			$weekSelect[$temp-1]['starttime'] = strtotime($year.'W'.$week);
			$weekSelect[$temp-1]['endtime'] = strtotime('+1 week -1 day',strtotime($year.'W'.$week));
			$weekSelect[$temp-1]['start'] = date('Y-m-d',$weekSelect[$temp-1]['starttime']);
			$weekSelect[$temp-1]['end'] = date('Y-m-d',$weekSelect[$temp-1]['endtime']);
			if(date('Y',$weekSelect[$temp-1]['starttime']) == date('Y',$weekSelect[$temp-1]['endtime'])){
				$weekSelect[$temp-1]['name'] = date('Y年n月j日',$weekSelect[$temp-1]['starttime']).'-'.date('n月j日',$weekSelect[$temp-1]['endtime']);
			}else{
				$weekSelect[$temp-1]['name'] = date('Y年n月j日',$weekSelect[$temp-1]['starttime']).'-'.date('Y年n月j日',$weekSelect[$temp-1]['endtime']);
			}
			$weekSelect[$temp-1]['is_current'] = 0;
			if($now>=$weekSelect[$temp-1]['starttime'] && $now<=$weekSelect[$temp-1]['endtime']){
				$weekSelect[$temp-1]['is_current'] = 1;
			}
		}
		//按给定的年份计算明年周总数
		$date2 = new DateTime;
		$date2->setISODate($next_year, 53);
		$now = time();
		$weeks2 = max($date2->format("W"),52);
		for ($temp=1;$temp<=$weeks2;$temp++){
			$week2 = ($temp<10)?'0'.$temp:$temp;
			$weekSelect[$temp-1+$weeks]['starttime'] = strtotime($next_year.'W'.$week2);//echo strtotime($next_year.'W'.$week2);die;
			$weekSelect[$temp-1+$weeks]['endtime'] = strtotime('+1 week -1 day',strtotime($next_year.'W'.$week2));
			$weekSelect[$temp-1+$weeks]['start'] = date('Y-m-d',$weekSelect[$temp-1+$weeks]['starttime']);
			$weekSelect[$temp-1+$weeks]['end'] = date('Y-m-d',$weekSelect[$temp-1+$weeks]['endtime']);
			if(date('Y',$weekSelect[$temp-1+$weeks]['starttime']) == date('Y',$weekSelect[$temp-1+$weeks]['endtime'])){
				$weekSelect[$temp-1+$weeks]['name'] = date('Y年n月j日',$weekSelect[$temp-1+$weeks]['starttime']).'-'.date('n月j日',$weekSelect[$temp-1+$weeks]['endtime']);
			}else{
				$weekSelect[$temp-1+$weeks]['name'] = date('Y年n月j日',$weekSelect[$temp-1+$weeks]['starttime']).'-'.date('Y年n月j日',$weekSelect[$temp-1+$weeks]['endtime']);
			}
			$weekSelect[$temp-1+$weeks]['is_current'] = 0;
			//if($now>=$weekSelect[$temp-1+$weeks]['starttime'] && $now<=$weekSelect[$temp-1+$weeks]['endtime']){
			//	$weekSelect[$temp-1+$weeks]['is_current'] = 1;
			//}
		}
		return $weekSelect;
	}

	public function weekData($start, $end) {
		$weekData = array();
		$dt_start = strtotime($start);
		$dt_end   = strtotime($end);
		do {
			$tempWeek = $this->getWeek(date('Y',$dt_start),date('m',$dt_start),date('d',$dt_start));
			$weekData[] = array('day'=>date('Y-m-d', $dt_start),'name'=>date('n月j日', $dt_start),'week'=>$tempWeek,'weekStr'=>$this->WEEK[$tempWeek]);
		} while (($dt_start += 86400) <= $dt_end);
		return $weekData;
	}


	//获得方法内指定的日期的星期数
	function getWeek($year,$month,$day) {
		$week=date("w",mktime(0,0,0,$month,$day,$year));//获得星期
		return $week;//获得星期
	}

	function _env() {
		if(isset($_REQUEST['month'])) {//有指定月
			$month=$_REQUEST['month'];
		}else {
			$month=date("m");//默认为本月
		}
		if(isset($_REQUEST['year'])) {//有指年
			$year=$_REQUEST['year'];
		}else {
			$year=date("Y");//默认为本年
		}
		$this->setYear($year);
		$this->setMonth($month);
		$this->setDay(date("d"));
	}
}

?>
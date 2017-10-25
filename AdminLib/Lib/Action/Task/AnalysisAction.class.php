<?php
/**
 * 语文作业管理
 *
 */
class AnalysisAction extends AppCommAction{

	public function index() {

		$this->getList();
	}

	private function getList() {
		$model = D('Task');
		import('ORG.Util.Page');
		if(!empty($_GET['nYear'])) {
			$data['nYear'] = (int)$_GET['nYear'];
		} else {
			$data['nYear'] = (int)date('Y');
		}
		 $nMonth = (int)date('m');

         $nSeason = season($nMonth);
		if(!empty($_GET['nSeason'])) {
			$data['nSeason'] = (int)$_GET['nSeason'];
		} else {
			$data['nSeason'] =  $nSeason;
		}
		if(!empty($_GET['nType'])) {
			$data['nType'] = (int)$_GET['nType'];
		} else {
			$data['nType'] = 1;
		}
		if(!empty($_GET['sClassTypeCode'])) {
			$data['sClassTypeCode'] = preg_replace('|[^a-zA-Z0-9]|i', '', $_GET['sClassTypeCode']) ;
		} else {
			$data['sClassTypeCode'] = 0;
		}
		if(!empty($_GET['sClassCode'])) {
			$data['sClassCode'] = preg_replace('|[^a-zA-Z0-9]|i', '', $_GET['sClassCode']) ;
		} else {
			$data['sClassCode'] = 0;
		}
		$nCurrYear = (int)date('Y');

		$years = array(0=>'年');
		for($i=$nCurrYear-1;$i<=$nCurrYear+1;$i++) {
			$years[$i] = $i;
		}
		$seasons = array(
			0=>'学期',
			3=>'春',
			4=>'夏',
			1=>'秋',
			2=>'冬'
			);
		$types = array(
			0=>'题型',


			1=>'选择题',
			2=>'主观题',
			3=>'填空题'
			);
		$tables = array(1=>'task_student_main',2=>'task_student_main_subjective',3=>'task_student_main_fillin');
		$results = $model->getClassTypeList($data['nYear'],$data['nSeason']);
		$classTypeCodes = array(0=>'班型');
		foreach($results as $value) {
			$classTypeCodes[$value['scode']] = $value['sname'];
		}
		//url($array,$key,$value)












		/*$total = $model->getQuestionListTotal();
		$Page       = new Page($total);
		//$Page->setConfig('theme','"%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first% %prePage% %linkPage% %nextPage% %end%"');
		$page = $Page->show();


		$data['page'] = empty($_GET['p'])?'1':$_GET['p'];*/
		$results = $model->getAnalysisList($data,$tables[$data['nType']]);
		//dumps($results);
		$aAnalysisList = array();
		$nTotal = 0;
		$classCodes = array(0=>'班级');
		foreach($results as $value) {
			$adata = $model->getQuestionInfoByCode($value['nquestionid']);
			$adata['num'] = $value['num'];
			$adata['sclasscode'] = $value['sclasscode'];
			$nTotal += $value['num'];
			$classCodes[$value['sclasscode']] = $value['sclasscode'];
			$adata['sTeacher'] = $model->getTeacher($value['sclasscode'],$adata['stopic']);
			$adata['totalNum'] = $model->getClassInfoByCode($value['sclasscode'],$adata['stopic']);

			$aAnalysisList[] = $adata;
		}

		echo '';
		if(!empty($_GET['process'])) {
			$this->xls($data,$aAnalysisList);exit();
		}
		$this->assign(get_defined_vars());
		$this->display('getList');
	}


	private function xls($data,$list) {

		header("Content-type:application/vnd.ms-excel,charset=utf8");
        header("Content-Disposition:attachment;filename=".implode('_', $data).".xls");
        echo  "
    	<table>

        <tr>
            <th>序号</th>

            <th>时段</th>


            <th>班型名称</th>
            <th>班级编码</th>
            <th>课节</th>
            <th>任课老师</th>
            <th>提交人数</th>
            <th>出勤人数</th>
            <th>提交率</th>



            <th>题型</th>


        </tr>";



        $i=1;
        foreach($list as $value){
        echo "<tr>";
        echo "<td>", $i,"</td>";$i++;

        echo "<td>",$value['nyear'],'年 ',seasonName($value['nseason']),"</td>";



         echo "<td>",$value['sname'],"</td>";
            echo "<td>",$value['sclasscode'],"</td>";
            echo "<td>",trim($value['stopic']),"</td>";
            echo "<td>".$value['sTeacher'],"</td>";
            echo "<td>",$value['num'],"</td>";
            echo "<td>",$value['totalNum'],"</td>";
            echo "<td><font color='red'>[",round($value['num']*100/$value['totalNum'],1),"%]</font></td>";

            echo "<td>",questionTyoe($value['ntype']),"</td>";


        echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
	}

	public function changeStatus() {
		$model = D('Task');
		$resuts = $model->getAllobjectiveList();
		$data = array();
		foreach($resuts as $value) {
			$sClassCode = $model->getClassCode($value['saliascode'],$value['stopic']);
			$data[] = array('id'=>$value['mid'],'sAliasCode'=>$value['saliascode'],'topic'=>$value['stopic'],'sClassCode'=>$sClassCode);

			$model->updateClassCode($value['mid'],$sClassCode);
		}
		echo '<pre>';
		print_r($data);
	}
	protected function notNeedLogin(){
		return array('UPLOADER');
	}

}


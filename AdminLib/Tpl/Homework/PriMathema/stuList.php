<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
<style type="text/css">
body, h1, h2 {
	font-family: "Microsoft Yahei";
}
@media (min-width: 600px) {
.container {
	width: 95%;
}
}
h2 {
	border-bottom: 3px solid #EEEEEE;
	color: #575765;
	font-size: 24px;
	margin: 40px 0;
	padding-bottom: 10px;
}
</style>
<script type="text/javascript">
function search(){
	var username = $("#username").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
	var url = getUrl('username');
	if(username == ''){
		window.location.href = thisUrl + ''+url;
	}else{
		window.location.href = thisUrl + '/username/'+username+url;
	}
	return false;
}
var thisUrl = '/homework/pri_mathema/stuWorkList';
function getUrl(my){
	var nClassYear = $("#nClassYear").val();
	var nSemester = $("#nSemester").val();
	var classTypeCode = $("#classTypeCode").val();
	var nLessonNo = $("#nLessonNo").val();
	var username = $("#username").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');;
    var dept = $('#dept').val();
	var url = '';
    url += '/dept/' + dept;
	if(nClassYear != 0 && my != 'nClassYear'){
		url += '/nClassYear/'+nClassYear;
	}
	if(nSemester != 0 && my != 'nSemester'){
		url += '/nSemester/'+nSemester;
	}
	if(nLessonNo !=0 && my != 'nLessonNo'){
		url += '/nLessonNo/'+nLessonNo;
	}
	if(classTypeCode != 0 && my != 'classTypeCode'){
		url += '/classTypeCode/'+classTypeCode;
	}
	if(username != '' && my != 'username'){
		url += '/username/'+username
	}
	return url;
}
function deleted(id,type){
	if(confirm('确实要删除该内容吗?')){
		$.get('/homework/PriMathema/ajaxDeleteStuWork/infoid/'+id+'/type/'+type,function(data){
			if(data == 1){
				alert('删除成功');
				if(type == 1){
					$('#'+id).find("abc[attr = '1']").remove();;
				}else if (type == 2){
					$('#'+id).find("abc[attr = '2']").remove();
				}else if (type == 3){
					$('#'+id).remove();
				}
			}else{
				alert('删除失败');
			}
		})
	}
}
jQuery(function($) {
//	$("#nClassYear").change(function(){
//		var url = getUrl('nClassYear');
//		var nClassYear = $("#nClassYear").val();
//		if(nClassYear == 0){
//			window.location.href = thisUrl+url;
//		}else{
//			window.location.href = thisUrl + '/nClassYear/'+nClassYear+url;
//		}
//	})
//	$("#nSemester").change(function(){
//		var url = getUrl('nSemester');
//		var nSemester = $("#nSemester").val();
//		if(nSemester == 0){
//			window.location.href = thisUrl + ''+url;
//		}else{
//			window.location.href = thisUrl + '/nSemester/'+nSemester+url;
//		}
//	})
    $('#nClassYear,#nSemester,#dept').change(function(){
        location = getBaseUrl();
    })
	$("#classTypeCode").change(function(){
		$('#nLessonNo').val(0);
		var url = getUrl('classTypeCode');
		var classTypeCode = $("#classTypeCode").val();
		if ($("#classTypeCode").val()=='0'){
			window.location.href = thisUrl + ''+url;
		}else{
			window.location.href = thisUrl + '/classTypeCode/'+classTypeCode+url;
		}
	})
	$("#nLessonNo").change(function(){
		var nLessonNo = $("#nLessonNo").val();
		var url = getUrl('nLessonNo');
		if ($("#nLessonNo").val()==0){
			window.location.href = thisUrl + ''+url;
		}else{
			window.location.href = thisUrl + '/nLessonNo/'+nLessonNo+url;
		}
	})
})
function getBaseUrl() {
    var url = thisUrl;
    var nClassYear = $("#nClassYear").val();
    var nSemester = $("#nSemester").val();
    var dept = $('#dept').val();
    if(nClassYear != 0) {
        url += '/nClassYear/' + nClassYear;
    }
    if(nSemester != 0) {
        url += '/nSemester/' + nSemester;
    }
    url += '/dept/' + dept;
    return url;
}
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10" style="width:95%">
      <h2>管理<span style="font-weight:bold;color:red"><?=$deptCfg['deptName']?></span>学生作业</h2>
      <form class="form-horizontal" onsubmit="return search();" method="GET">
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;">筛选：</label>
            <?php if(sizeof($empDeptArray) > 1):?>
                <div class="col-md-1" style="width:150px;">
                    <select class="form-control choose-year" id="dept" name="dept">
                        <?php foreach($empDeptArray as $dName=>$dCfg):?>
                            <option value="<?php echo $dName?>" <?php if($deptName == $dName){echo 'selected="true"';}?>><?php echo $dCfg['deptName']?></option>
                        <?php endforeach?>
                    </select>
                </div>
            <?php else:?>
                <input type="hidden" name="dept" id="dept" value="<?php echo $deptName?>" />
            <?php endif?>
          <div class="col-md-1" style="width:150px;">
            <select class="form-control choose-year" id="nClassYear">
              <option value="0">请选择学年</option>
              <option value="2014"<?php if($_GET['nClassYear'] == 2014): ?> selected<?php endif; ?>>2014</option>
              <option value="2013"<?php if($_GET['nClassYear'] == 2013): ?> selected<?php endif; ?>>2013</option>
            </select>
          </div>
          <div class="col-md-1" style="width:150px;">
            <select class="form-control choose-semester" id="nSemester">
            <option value="0">请选择学期</option>
              <?php foreach($xueqiArr as $xueqi): ?>
              <option value="{$xueqi.id}"<?php if($_GET['nSemester'] == $xueqi['id']): ?> selected<?php endif; ?>>{$xueqi.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:250px;">
            <select class="form-control choose-classtype" id="classTypeCode">
              <option value="0">请选择班型</option>
               <?php
		        	foreach ($AllClass as $one){
		        		if (!empty($_GET['classTypeCode']) && urldecode($_GET['classTypeCode']) == $one['classtypecode']){
		        			echo "<option  selected='selected' value='".$one['classtypecode']."'>".$one['classtypename']."</option>";
		        		}else{
		        			echo "<option value='".$one['classtypecode']."'>".$one['classtypename']."</option>";
		        		}
		        	} 
		    	?>
            </select>
          </div>
          <div class="col-md-1" style="width:150px;">
            <select class="form-control choose-classlesson" id="nLessonNo">
              <option value="0">请选择讲次</option>
              <?php foreach($nLessonNos as $jiangci): ?>
              <option value="{$jiangci['nlessonno']}"<?php if($_GET['nLessonNo'] == $jiangci['nlessonno']): ?> selected<?php endif; ?>>{$jiangci['nlessonno']}</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
        	<label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
        	<div class="col-md-1" style="width:170px;">
	        	<input type="text" id="username" value="<?php echo urldecode($_GET['username']);?>" class="form-control" placeholder="输入学生姓名查找">
	        </div>
	        <div class="col-md-1" style="width:170px;">
	        	<button type="submit" class="btn btn-primary">查找</button>
	        </div>
        </div>
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($list)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
                <th>姓  名</th>
                <th>学  年</th>
                <th>学  期</th>
                <th>班级编号</th>
                <th>班级名称</th>
                <th>讲次</th>
                <th>得&nbsp;&nbsp;分<br>(客|主)</th>
                <th>操作</th>
              </tr>
              <?php foreach($list as $item): ?>
              <tr id="{$item.id}">
             	<td>{$item['sname']}</td>
                <td>{$item['nclassyear']}</td>
                <td><?php
                	switch ($item['nsemester']){
                		case 1:
                			echo '秋季';break;
                		case 2:
                			echo '寒假';break;
                		case 3:
                			echo '春季';break;
                		case 4:
                			echo '暑假';break;
                	} 
                ?></td>
                <td>{$item['sclasscode']}</td>
                <td>{$item['sclassname']}</td>
                <td>{$item['nlessonno']}</td>
                <td>{$item['score']}|{$item['subscore']}</td>
                <td>
                	<a href="/homework/pri_mathema/editStuWork/infoid/{$item.id}" title="编辑学生作业">编辑</a>
                	<?php if ($item['objective'] == 1){?>
                	<abc attr = 1> |
                	<a href="javascript:void(0)" onclick='deleted(<?php echo $item['id'];?>,1)' title="只删客观，该记录继续存在">删客</a>
                	</abc> 
                	<?php }?>
                	<?php if ($item['subjective'] == 1){?>
                	<abc attr = 2> | 
                	<a href="javascript:void(0)" onclick='deleted(<?php echo $item['id'];?>,2)' title="只删主观，该记录继续存在">删主</a> 
                	</abc>
                	<?php }?>
                	|
                	<a href="javascript:void(0)" onclick='deleted(<?php echo $item['id'];?>,3)' title="全部删除，该记录会消失">全删</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </table>
            <div>{$show}</div>
            </div>
          <?php else: ?>
          <div class="col-md-10" style="background-color: #66CCFF; border: 1px solid #6699FF; color: #ffffff; font-size: 16px;width:95%">
            <div style="width: 200px; margin: 80px auto;">当前筛选条件下没有结果。</div>
          </div>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <div id="msg" class="col-md-offset-1 col-md-4 msg failed"></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="succeed" class="jGrowl top-center" style="background-color:#009900; color:#FFF;"></div>
<div id="failed" class="jGrowl top-center" style="background-color:#F60; color:#FFF;"></div>
</body>
</html>
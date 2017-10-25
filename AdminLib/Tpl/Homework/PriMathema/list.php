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
var thisUrl = '/homework/pri_mathema/operate';
function ajaxDelete(id){
	if(confirm("确定要删除这条数据吗?")){
		$.post('/homework/PriMathema/ajaxdeleteInfo',{"infoid":id},function(data){
			if(data == 1){
				alert("删除成功");
				$('#'+id).remove();
			}else{
				alert(data);
				alert("删除失败");
			}
		});
	}
}
function getUrl(my){
	var nClassYear = $("#nClassYear").val();
	var nSemester = $("#nSemester").val();
	var ClassName = $("#ClassName").val();
	var Explain = $("#Explain").val();
    var dept = $('#dept').val();
	var url = '';
    url += '/dept/' + dept;
	if(nClassYear != 0 && my != 'nClassYear'){
		url += '/nClassYear/'+nClassYear;
	}
	if(nSemester != 0 && my != 'nSemester'){
		url += '/nSemester/'+nSemester;
	}
	if(Explain !=0 && my != 'Explain'){
		url += '/Explain/'+Explain;
	}
	if(ClassName != 0 && my != 'ClassName'){
		url += '/ClassName/'+ClassName;
	}
	return url;
}
jQuery(function($) {

    $('#nClassYear,#nSemester,#dept').change(function(){
        location = getBaseUrl();
    })
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
	$("#ClassName").change(function(){
		$('#Explain').val(0);
		var url = getUrl('ClassName');
		var ClassName = $("#ClassName").val();
		if ($("#ClassName").val()=='0'){
			window.location.href = thisUrl + ''+url;
		}else{
			window.location.href = thisUrl + '/ClassName/'+ClassName+url;
		}
	})
	$("#Explain").change(function(){
		var Explain = $("#Explain").val();
		var url = getUrl('Explain');
		if ($("#Explain").val()==0){
			window.location.href = thisUrl + ''+url;
		}else{
			window.location.href = thisUrl + '/Explain/'+Explain+url;
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
      <h2>管理<span style="font-weight:bold;color:red"><?php echo $deptCfg['deptName']?></span>作业</h2>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
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
              <option value="0">选择学年</option>
              <option value="2014"<?php if($_GET['nClassYear'] == 2014): ?> selected<?php endif; ?>>2014</option>
              <option value="2013"<?php if($_GET['nClassYear'] == 2013): ?> selected<?php endif; ?>>2013</option>
            </select>
          </div>
          <div class="col-md-1" style="width:150px;">
            <select class="form-control choose-semester" id="nSemester">
            <option value="0">选择学期</option>
              <?php foreach($xueqiArr as $xueqi): ?>
              <option value="{$xueqi.id}"<?php if($_GET['nSemester'] == $xueqi['id']): ?> selected<?php endif; ?>>{$xueqi.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:250px;">
            <select class="form-control choose-classtype" id="ClassName">
              <option value="0">请选择班型</option>
               <?php
		        	foreach ($AllClass as $one){
		        		if (!empty($_GET['ClassName']) && urldecode($_GET['ClassName']) == $one['classtypename']){
		        			echo "<option  selected='selected'>".$one['classtypename']."</option>";
		        		}else{
		        			echo "<option>".$one['classtypename']."</option>";
		        		}
		        	} 
		    	?>
            </select>
          </div>
          <div class="col-md-1" style="width:150px;">
            <select class="form-control choose-classlesson" id="Explain">
              <option value="0">请选择讲次</option>
              <?php foreach($Explains as $jiangci): ?>
              <option value="{$jiangci['explainno']}"<?php if($_GET['Explain'] == $jiangci['explainno']): ?> selected<?php endif; ?>>{$jiangci['explainno']}</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($list)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
                <th>学年</th>
                <th>学期</th>
                <th>班型</th>
                <th>班号</th>
                <th>讲次</th>
                <th>知识点</th>
                <th>操作</th>
              </tr>
              <?php foreach($list as $item): ?>
              <tr id="{$item.id}">
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
                <td>{$item['classname']}</td>
                <td><?php
                if ($item['classno']){
                	$arr = explode(',',$item['classno']);
                	$i = 1;
                	foreach ($arr as $one){
                		if ($i == 1){
                			echo $one;
                		}else{
                			echo "<br>".$one;
                		}
                		$i++;
                	}
                } 
                ?></td>
                <td>{$item['explainno']}</td>
                <td>
                <?php if (strpos($item['knowledgename'],',')){
                	$arr = explode(',',$item['knowledgename']);
                	$j = 1;
                	foreach ($arr as $one){
                		if ($j == 1){
                			echo $one;
                		}else{
                			echo "<br>".$one;
                		}
                		$j++;
                	}
                }else{
                	echo $item['knowledgename'];
                }?>
                </td>
                <td>
                	<?php
                	if (!empty($item['knowledge'])){
                	?>
                	<a href="/homework/pri_mathema/add/dept/{$deptName}/type/1/infoid/{$item.id}" title="上传客观作业"><i class="glyphicon glyphicon-upload"></i></a>
                	<a href="/homework/pri_mathema/edit/dept/{$deptName}/type/1/infoid/{$item.id}" title="编辑客观作业"><i class="glyphicon glyphicon-edit"></i></a>
                	<a href="/homework/pri_mathema/objList/dept/{$deptName}/infoid/{$item.id}" title="已做试题编辑"><i class="glyphicon glyphicon-edit"></i></a>
                	<a href="/homework/pri_mathema/add/dept/{$deptName}/type/2/infoid/{$item.id}" title="上传主观作业"><i class="glyphicon glyphicon-plus"></i></a>
                	<a href="/homework/pri_mathema/edit/dept/{$deptName}/type/2/infoid/{$item.id}" title="编辑主观作业"><i class="glyphicon glyphicon-list-alt"></i></a>
                	<?php }?>
                	<a href="/homework/pri_mathema/addnuncupate/dept/{$deptName}/infoid/{$item.id}" title="添加口述题"><i class="glyphicon glyphicon-cloud-upload"></i></a>
                	<!--<a href="/homework/pri_mathema/" title="预览"><i class="glyphicon glyphicon-eye-open"></i></a>-->|&nbsp;&nbsp; 
                	<a href="javascript:void(0)" onclick="ajaxDelete({$item.id})" title="删除当前作业"><i class="glyphicon glyphicon-trash"></i></a>
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
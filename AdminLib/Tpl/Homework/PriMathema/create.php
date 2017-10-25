<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
<!--zTree-->
<link rel="stylesheet" href="/static/css/zTree/zTreeStyle.css" type="text/css">
<script type="text/javascript" src="/static/js/zTree/jquery.ztree.core-3.5.js"></script>
<script type="text/javascript" src="/static/js/zTree/jquery.ztree.excheck-3.5.js"></script>
<!--zTree-->
<script type="text/javascript">
	jQuery(function($) {
		$("#sub-hw").live('click', function(e) {
			e.preventDefault();
			var classtypecode = $("#ClassType").val();
			var classname = $("#ClassName").val();
			var classlesson = $("#ExplainNo").val();
			var codeSel = $("#codeSel").val();
			var classNos = $("#RadioClassNo input:checked");
			var tmpStr = '';
			for(var i=0;i<classNos.length;i++){
				if(i == 0){
					tmpStr = classNos.eq(i).val();
				}else{
					tmpStr += '||'+classNos.eq(i).val();
				}
				 
			}
			classNos = tmpStr;
			var classyear = $("#nClassYear").val();
			var semester = $("#nSemester").val();
			if (typeof(classtypecode) == "undefined" || classtypecode == '0') {
				failedTip('还未选择班型');
				return false;
			}
			if (typeof(classlesson) == "undefined" || classlesson == '0') {
				failedTip('还未选择讲次');
				return false;
			}
			if (typeof(codeSel) == "undefined" || codeSel == '') {
				failedTip('还未选择知识点');
				return false;
			}
			$.post('/Homework/PriMathema/ajaxCheckQuestion',{
				classType:classtypecode,
				className:classname,
				ExplainNo:classlesson,
				ClassNo:classNos,
				nClassYear:classyear,
				nSemester:semester
			},function(data){
				if(data != '1' && data != 1){
					failedTip('该作业已存在，请检查后再次提交');
					return false;
				}else{
					$("#addHomework").submit();
				}
			})
		})
		$("select[ate='selectcs']").change(function(){
			var y = $("#nClassYear option:selected").val();
			var s = $("#nSemester option:selected").val();
			window.location.href = '/homework/pri_mathema/create/y/'+y+'/s/'+s + '/dept/' + $('#dept').val();
		})
		$("#ClassType").change(function(){
			var k = $("#ClassType option:selected").val();
			var v = $("#ClassType option:selected").text();
			var y = $("#nClassYear").val();
			var s = $("#nSemester").val();
            var d = $('#dept').val();
			var c = v.indexOf('尖子');
			$("#ClassName").val(v);
			if(k){
				if(c != -1){
					$("#RadioClassNo").css({'display':'block'});
					$(".checkbox").remove();
					$.post('/Homework/PriMathema/ajaxGetClassNo',{_k:k},function(data){
						var arrno  = $.parseJSON(data);
						for(var a=0;a<arrno.length;a++){
							var b = a+1;
							$("#RadioClassNo").find('div').append("<label class='checkbox'><input type=\"checkbox\" name=\"ClassNo[]\" value=\""+arrno[a]['sname']+"\" id=\"ClassNo_"+a+"\"><label for=\"ClassNo_"+a+"\" class=\"labelForRadio\">"+arrno[a]['sname']+"</label></label>");
						}
					})
				}else{
					$("#RadioClassNo").css({'display':'none'});
					$(".checkbox").remove();
				}
				$.post('/Homework/PriMathema/ajaxGetExplainno',{_k:k,_v:v,_y:y,_s:s, _d:d},function(data){
					if(data){
						$("#ExplainNo").removeAttr('disabled');
						var arr  = $.parseJSON(data);
						$("#ExplainNo").empty();
						$("#ExplainNo").append("<option value='0'>请选择讲次</option>");
						for(var i=0;i<arr.length;i++){
							var m = i+1;
							$("#ExplainNo").append("<option value='"+arr[i]['nlessonno']+"'>"+arr[i]['nlessonno']+"</option>");
						}  
					}
				});
			}else{
				$("#ExplainNo").empty();
				$("#ExplainNo").append("<option value='0'>请选择讲次</option>");
			}
		})
	})
function failedTip(msg){
	$('#failed').jGrowl(msg, { life: 1000, closeTemplate: '' });
}
</script>

<SCRIPT type="text/javascript">
		<!--
		var setting = {
			check: {
				enable: true,
				chkboxType: {"Y":"", "N":""}
			},
			view: {
				dblClickExpand: false
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				beforeClick: beforeClick,
				onCheck: onCheck
			}
		};

		var zNodes =[{$tree}];

		function beforeClick(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.checkNode(treeNode, !treeNode.checked, null, true);
			return false;
		}
		
		function onCheck(e, treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
			nodes = zTree.getCheckedNodes(true),
			v = "";
			var code = "";
			for (var i=0, l=nodes.length; i<l; i++) {
				v += nodes[i].name + ",";
				code += nodes[i].id;
				if(i != l-1){
					code += ",";
				}
			}
			if (v.length > 0 ) v = v.substring(0, v.length-1);
			var cityObj = $("#citySel");
			var codeObj = $("#codeSel");
			cityObj.attr("value", v);
			codeObj.attr("value",code);
		}

		function showMenu() {
			var cityObj = $("#citySel");
			var cityOffset = $("#citySel").offset();
			$("#menuContent").css({left:cityOffset.left + "px", top:cityOffset.top + cityObj.outerHeight() + "px"}).slideDown("fast");

			$("body").bind("mousedown", onBodyDown);
		}
		function hideMenu() {
			$("#menuContent").fadeOut("fast");
			$("body").unbind("mousedown", onBodyDown);
		}
		function onBodyDown(event) {
			if (!(event.target.id == "menuBtn" || event.target.id == "citySel" || event.target.id == "menuContent" || $(event.target).parents("#menuContent").length>0)) {
				hideMenu();
			}
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
		});
		//-->
	</SCRIPT>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>新建<span style="color:red;font-weight:bold"><?php echo $deptCfg['deptName']?></span>作业</h2>
      <form class="form-horizontal" role="form" id="addHomework" action="" method="post">
      <?php if(sizeof($empDeptArray) >1):?>
          <div class="form-group">
              <label for="account_id" class="col-md-1 control-label" style="width:120px;">选择学科：</label>
              <div class="col-md-1" style="width: 270px;">
                  <select class="form-control choose-semester" name="dept" id="dept" ate="selectcs">
                      <?php foreach($empDeptArray as $dName=>$dCfg):?>
                          <option value="<?php echo $dName?>" <?php if($dName == $deptName){echo 'selected="true"';}?>><?php echo $dCfg['deptName']?></option>
                      <?php endforeach?>
                  </select>
              </div>
          </div>
      <?php else:?>
          <input type="hidden" name="dept" id="dept" value="<?php echo $deptName?>" />
      <?php endif?>
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:120px;">选择学期：</label>
          <div class="col-md-1" style="width: 130px;">
            <select class="form-control choose-semester" name="nClassYear" id="nClassYear" ate="selectcs">
              <option value="2014" <?php if($_GET['y'] == 2014): ?> selected<?php endif; ?>>2014年</option>
              <option value="2015"<?php if($_GET['y'] == 2015): ?> selected="selected"<?php endif; ?>>2015年</option>
            </select>
          </div>
          <div class="col-md-1" style="width:120px;">
            <select class="form-control choose-semester" name="nSemester" id="nSemester" ate="selectcs">
              <?php foreach($xueqiArr as $xueqi): ?>
              <option value="{$xueqi.id}"
              <?php 
              	if (!empty($_GET['s'])){
              		if ($xueqi['id'] == $_GET['s']){
              			echo 'selected="selected"';
              		}
              	}else if ($xueqi['id'] == $nSemester['id']){
					echo 'selected="selected"';
				} ?>
              
              >{$xueqi.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">选择班型：</label>
          <div class="col-md-1" style="width:270px;">
            <select class="form-control choose-classtype" name="ClassType" id="ClassType">
              <option value="0">请选择班型</option>
              <?php foreach($classType as $banxing): ?>
              <option value="{$banxing.scode}"<?php if($classtypecode == $banxing['scode']): ?> selected<?php endif; ?>>{$banxing.sname}</option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="ClassName" id="ClassName" value="" />
          </div>
        </div>
        
        <div class="form-group" style="display:none" id="RadioClassNo">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">选择班号：</label>
          <div class="col-md-1" style="width:270px;">
          </div>
        </div>
        
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">选择讲次：</label>
          <div class="col-md-1" style="width:200px;">
            <select class="form-control choose-classlesson" disabled id="ExplainNo" name="ExplainNo">
              <option value="0">请选择讲次</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">选择知识点：</label>
          <div class="col-md-1" style="width:400px;">
			<input id="citySel" name="knowledgeName" class="form-control" type="text" value="" style="display:inline-block;width:220px;background-color:#FFF;cursor:default" readonly onclick="showMenu();" />
			<input type="hidden" name="codeSel" id="codeSel" value=""/>
			&nbsp;<a id="menuBtn" href="#" onclick="showMenu(); return false;">选择</a>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1" style="margin-left:120px;">
            <button type="submit" class="btn btn-success btn-lg" id="sub-hw">提交</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="succeed" class="jGrowl top-center" style="background-color:#009900; color:#FFF;"></div>
<div id="failed" class="jGrowl top-center" style="background-color:#F60; color:#FFF;"></div>
<div id="menuContent" class="menuContent" style="display:none; position: absolute;border:1px solid #617775;background-color:#F0F6E4;">
	<ul id="treeDemo" class="ztree" style="margin-top:0; width:220px; height: 300px;overflow:auto;"></ul>
</div>
</body>
</html>
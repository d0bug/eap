<!doctype html>
<html>
    <head>
	<meta charset="utf-8" />
	<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
	<script type="text/javascript" src="/static/js/popup.js"></script>
	<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
	<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
	var tools = [{iconCls:'icon-cancel',
                        handler:function(){
                            jQuery('body').layout('remove', 'east');
                        }}];
	function getCheckbox(heluId, data) {
		return str = '<input type="checkbox" name="helu_id[]" value="'+heluId+'_'+data.type+'"> ';
	}

	function getTitle(title, data) {
		//var point = data.url.lastIndexOf(".");
		//return str = title+data.url.substr(point);
		return str = title;
	}

	function getManage(val,data){
		//return str = '<a href="javascript:moveFile(\'' + data.helu_id + '_' + data.type + '\', \'0\')">移动</a> ' + '| <a href="javascript:delFile(\'' + data.helu_id + '_' + data.type + '\', \'0\')">删除</a> | <a href="<?php echo U('Vip/VipStudents/download')?>/id/'+data.id+'/type/'+parseInt(parseInt(data.type)+1)+'" target="_blank">下载</a> | <a href="javascript:void(0)" onclick="previewFile(event,\'' + data.helu_id+ '\', \''+data.type+'\')">预览</a>';
		return str = '<a href="javascript:moveFile(\'' + data.helu_id + '_' + data.type + '\', \'0\')">移动</a> ' + '| <a href="javascript:delFile(\'' + data.helu_id + '_' + data.type + '\', \'0\')">删除</a> ';
	}

	function get_content(link) {
		var dept = jQuery(link).attr('dept');
		var xueke = jQuery(link).attr('xueke');
		var teacher = jQuery(link).attr('teacher');
		var student = jQuery(link).attr('student');
		var url = jQuery(link).attr('href');
		jQuery('body').layout('remove','center');
		jQuery('#fileGrid').datagrid({
			view:detailview,
			url:url,
			detailFormatter:function(index,data){

			},
		})
		return false;
	}

	function checkAll(id){
		if($('#'+id).attr("checked")=='checked'){
			$("input[name='helu_id[]']:checkbox").attr("checked", true);
		}else{
			$("input[name='helu_id[]']:checkbox").attr("checked", false);
		}
	}

	function filterFiles(){
		var filter_url = '<?php echo U('Vip/VipContent/getContent');?>'+$('#filter_url').val();
		var keyword = jQuery('#keyword').val();
		if($('#keyword').val()!=''){
			filter_url = filter_url+'/keyword/'+keyword;
		}
		if($('#start').val()!=''){
			filter_url = filter_url+'/start/'+$('#start').val();
		}
		if($('#end').val()!=''){
			filter_url = filter_url+'/end/'+$('#end').val();
		}

		jQuery('#fileGrid').datagrid({
			view:detailview,
			url:filter_url,
			detailFormatter:function(index,data){

			},
		})
	}

	function moveFile(idStr,is_batch){
		if(is_batch == 1){
			var idStr = getSelectHeluId();
		}
		jQuery('body').layout('remove','east');
		jQuery('body').layout('add', {
			region:'east',
			width:400,
			title:'移动文档',
			href:'<?php echo $moveFileUrl;?>/idStr/'+idStr,
			tools:tools,
			collapsible:false
		});

	}

	function delFile(idStr,is_batch){
		if(is_batch == 1){
			var idStr = getSelectHeluId();
		}
		$.post('<?php echo $delFileUrl;?>',
		{idStr:idStr,is_batch:is_batch},
		function(data){
			alert(data);
			jQuery('#fileGrid').datagrid('reload');
		}
		);
	}


	function previewFile(ev,helu_id,type,fileUrl){
		var url = "<?php echo U('Vip/VipContent/viewFile');?>/helu_id/"+helu_id+"/type/"+type+"/url/"+fileUrl;
		var objPos = mousePosition(ev);
		messContent="<div class=\"mesWindowsBox\" style=\"height:650px;\"><iframe width=\"980\" height=\"650\" src=\""+url+"\" style=\"border:0px;\"></iframe></div>";
		showMessageBox('文档预览',messContent,objPos,1000,0);
	}


	function getSelectHeluId(){
		var idStr = '';
		$("input[name='helu_id[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				idStr += $(this).val()+"|"
			}
		})
		if(idStr == ''){
			alert('请选择要操作的记录');
			return false;
		}
		return idStr;
	}

	jQuery(function(){
		jQuery('#tree').tree({
			url:'<?php echo $menuTreeUrl?>',
			onClick: function(node){
				if(node.attributes!=''){
					$('#filter_url').val(node.attributes);
					jQuery('#fileGrid').datagrid({
						view:detailview,
						url:'<?php echo U('Vip/VipContent/getContent')?>'+node.attributes,
						detailFormatter:function(index,data){
						},
					})
				}
			},
			onBeforeLoad:function(node, param){
				$('#loader').html('<img src="/static/images/loading.gif" /> 正在加载菜单，请稍后...');
			},
			onLoadSuccess:function(node, data){
				$('#loader').html('');
			}

		});
	})

	</script>
	</head>
<body class="easyui-layout">
<?php if($permValue==3):?>
	<div region="west" style="width:230px;">
		<div class="treeMenu">菜单</div>
	    <span id="loader"></span>
		<ul id="tree"  class="easyui-tree"></ul>
    </div>
    <div region="center">
		<table id="fileGrid" class="easyui-datagrid" fit="true" border="false" rownumbers="true" singleselect="true" toolbar="#toolbar" pagination="true" pageList="[20,30,50]">
			<thead>
			<tr>
				<th width="30" field="helu_id" formatter="getCheckbox"></th>
				<th width="800" field="title" formatter="getTitle">文档列表&nbsp;&nbsp;</th>
				<th width="150" field="manage_row" formatter="getManage">操作</th>
			</tr>
			</thead>
		</table>
	</div>
	<div id="toolbar">
		<input type="hidden" id="filter_url" name="filter_url" value="" size="50"> 
		<input type="text" name="keyword" id="keyword" placeholder="关键词" value="" style="width:120px" />&nbsp;&nbsp;
		<input type="text" name="start" id="start" placeholder="起始时间" value="" onClick="WdatePicker()" class="Wdate"/>-<input type="text" name="end" id="end" placeholder="截止时间" value="" onClick="WdatePicker()" class="Wdate"/>&nbsp;
		<a onclick="javascript:filterFiles()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>&nbsp;&nbsp;	
		<input type="checkbox" name="checkAll" id="checkAll" value="1" onclick="checkAll(this.id)">全选	&nbsp;&nbsp;
		<a href="javascript:delFile('', 1)" class="easyui-linkbutton" iconCls="icon-remove" plain="true">批量删除</a>&nbsp;&nbsp;
		<a href="javascript:moveFile('', 1)" class="easyui-linkbutton" iconCls="icon-cut" plain="true">批量移动</a>
	</div>
<?php else:?>
	您没有权限操作此模块!
<?php endif;?>
</body>
</html>
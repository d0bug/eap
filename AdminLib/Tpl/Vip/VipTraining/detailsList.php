<script type="text/javascript" src="/static/js/training.js"></script>

<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
    
        <span style="float: right; padding-right: 20px;"  >
            <a href="#" style="font-size: 14px; color: blue;" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: export_teach('<?php echo $teachInfo[0]['id'];?>')">导出</a>
        </span>    
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_kaoping">
        <input type="hidden" name="id" value="<?php echo $teInfo['id'];?>" />
        <input type="hidden" name="tr_id" value="<?php echo $teInfo['tr_id']?>"/>
        
                <p style="font-size: 16px; padding-top: 15px; padding-bottom: 15px;">——————————— 详情 —————————————</p>
			<table width="90%" border="0" cellpadding="0" cellspacing="0" class="tableInfo" style="border-top: 1px silver;">                
		         
        		 <?php foreach($teachInfo as $key=>$val){?>
                 <tr>
					<td class="alt left">姓名：</td>
					<td>
					<span><?php echo $val['te_name']?></span>
					</td>
                    	<td class="alt left">性别：</td>
					<td>
						<span><?php echo $val['sex_name']?></span>
					</td>
				</tr>			
				<tr>
					<td class="alt left">毕业学校：</td>
					<td>
						<span><?php echo $val['school']?></span>
					</td>
                    <td class="alt left">专业：</td>
					<td>
						<span><?php echo $val['professional']?></span>
					</td>
				</tr>               
               		<tr>
					<td class="alt left">最高学历：</td>
					<td>
						<span><?php echo $val['level_school']?></span>
					</td>
                    <td class="alt left">毕业年份：</td>
					<td>
						<span><?php echo $val['graduation']?></span>
					</td>
				</tr> 
                	<tr>
					<td class="alt left">电话：</td>
					<td>
						<span><?php echo $val['phone']?></span>
					</td>
                    <td class="alt left">邮箱：</td>
					<td>
						<span><?php echo $val['mail']?></span>
					</td>
				</tr> 
                	<tr>
					<td class="alt left">学科：</td>
					<td>
						<span><?php echo $val['xueke_name']?></span>
					</td>
                    <td class="alt left">性质：</td>
					<td>
						<span><?php echo $val['formal_name']?></span>
					</td>
				</tr> 
   	            <tr>
					<td class="alt left">是否通过：</td>
					<td>
						<span><?php echo $val['through_name']?></span>
					</td>
                    <td class="alt left">关闭账号：</td>
					<td>
						<span><?php echo $val['status_name']?></span>
					</td>
				</tr>
               <?php }?>
               			
            </table>
       
            <p style="font-size: 16px; padding-top: 15px; padding-bottom: 15px;">—————————— 线下考试情况 ——————————</p>
            <table width="90%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">                
				 <tr style=" ">
					<td class="alt " style="width: 8%;">次数</td>
					<td class="alt " style="width: 10%;">考试时间</td>
                    <td class="alt " style="width: 13%;">考试时长</td>
                    <td class="alt " style="width: 8%;">满分</td>
                    <td class="alt " style="width: 8%;">得分</td>
                    <td class="alt " style="width: 10%;">本次考试排名</td>
				</tr>
                <tr style="">			
                <?php foreach($fenInfo as $key=>$val){?>
                    <?php foreach($val as $v){?>
    			     
    					<td class=" " style="width: 10%;">第<?php echo $v['test_ci']?>次</td>
				    	<td class=" " style="width: 10%;"><?php echo $v['test_time']?></td>
                        <td class=" " style="width: 10%;"><?php echo $v['shichang']?></td>
                        <td class=" " style="width: 10%;"><?php echo $v['zongfen']?></td>
                        <td class=" " style="width: 10%;"><?php echo $v['test_score']?></td>
                        <td class=" " style="width: 10%;"><?php echo $v['test_level']?></td>
    			
               	</tr>
                <?php } ?>
                 <?php } ?>
            </table>
             <p style="font-size: 16px; padding-top: 15px; padding-bottom: 15px;">————————————  点评  ——————————————</p>
             <div style="overflow-y: auto; height: 200px; width:90%;  ">            
                 
                     <?php foreach($pingjiaInfo as $key=>$val){?>
                     <div style="border:1px solid silver; padding:5px 5px 5px 5px;">
                        <div><?php echo $val['create_time']?>                        
                        </div>
                        <?php if($val['comments'] == ''){?>
                        <div><?php echo $val['test_comments']?>                        
                        </div>
                        <?php }elseif($val['test_comments'] == ''){?>
                        <div><?php echo $val['comments']?></div>
                        <?php }else{?>
                        <div><?php echo $val['test_comments']?></div>
                        <?php }?>
                        <div>点评人：<?php echo $val['create_name']?></div>                    
                     </div>
                     <?php }?>
                 
             </div>            
            <!--input type="submit" value="提交"-->
            <!--a style="color: blue;" href=""  onclick="javascript: editDianping()">修改</a-->
		</form>
	</div>
</div>
<div style="padding-bottom: 55px;"></div>
<script language='javascript' type='text/javascript'>
		var id = '';		
		var opts = {
            region: 'east',
            width: 300,
            collapsible: false,
            split: true,
            tools: [{
            		iconCls: 'panel-tool-close',
                  	handler: function(){
                      	$('#basic-dict-layout').layout('remove', 'east');
                    }}],
            minWidth: 300
        };
        
        //老师--考评记录
        function kaoping(id){
            //alert(id);exit;
             var params = {
                height: 500,
    			href: '/Vip/VipTraining/addKaoPing?id='+id,
    			iconCls: 'icon-add',
    			title: '考评编辑',
                
    		};
    				
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		//action = '/Vip/VipTraining/dict_add_people';
    		form = 'dict-add-form';
        }
        
        //老师-导出列表
        function export_teach(id){
            window.location.href='/Vip/VipTraining/exportTeachListWord?id=' + id;
            
        }
        
        function editDianping() {
			var row = $('#easyui-datagrid').datagrid('getSelected');
			if (row) {
				$.messager.confirm('操作提示', '您确实要删除吗？', function (r) {
					if (r) {
						$.post('/Vip/VipTraining/dict_delete', { id: row.id }, function (result) {
							if (result.status) {
				                $('#easyui-datagrid').datagrid('reload').datagrid('unselectAll');
							} else {
								$.messager.alert('错误信息', '操作失败!', 'error');
							}
						}, 'json');
					}
				});
			} else {
				$.messager.alert('提示信息', '请选择要操作的数据!', 'info');
			}
		}
        
 </script>       
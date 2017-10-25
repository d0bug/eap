<style type="text/css">
    #posInfoForm em{color:red}
    #posInfoForm .item{line-height:15px;padding-top:1px;margin-left:10px}
    #posInfoForm span{font-size:15px;font-weight:bold}
    #posInfoForm input{width:260px;height:18px;}
    #posInfoForm textarea{height:60px;width:95%}
    #posInfoForm .file{height:22px;width:240px;}
    #posInfoForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
    #posMap{margin-left:10px;margin-top:3px}
    .input_text{ width:250px;}
	#addTable *{ font-size:12px; color:#444}
    .addTable th{ font-size:12px; border:#95B8E7 solid 1px;}
    .addTable td{ padding: 3px; border:#95B8E7 solid 1px;}
    .char{ ime-mode:disabled}
</style>

 

<div class="demo-tip icon-tip"></div>
<div class="easyui-layout" fit="true" border="false">
    <div region="center">

				<table id="addTable" border="1" width="100%" class="addTable" style="border-collapse:collapse; border:0; border-color:#95B8E7">
				
					<tr>
						<th  class="th" align="right" width="120">&nbsp;卡组名称</th> 
						<td class="td" align="left">&nbsp;<?php echo $group_name;?></td>
					</tr>
					
					
					
					<tr>
						<th  class="th" align="right">&nbsp;卡号前缀</th> 
						<td class="td" align="left">&nbsp;<?php echo $card_pre;?></td>
					</tr>
					
					<tr>
						<th  class="th" align="right">&nbsp;卡号长度</th> 
						<td class="td" align="left">&nbsp;<?php echo $card_length;?></td>
					</tr>
					<tr>
						<th  class="th" align="right">&nbsp;密码长度</th> 
						<td class="td" align="left">&nbsp;<?php echo $pass_length;?></td>
					</tr>
					
					<tr>
						<th  class="th" align="right">&nbsp;密码组成</th> 
						<td class="td" align="left">&nbsp;
                        <?php 
							$element = array('d' => '数字', 'c' => '字母', 'dc' => '数字+字母' );
						echo $element[$pass_element];?>
                      </td>
					</tr>
					
 					<tr>
						<th  class="th" align="right">&nbsp;当前卡数量</th> 
						<td class="td" align="left">&nbsp;<?php echo $card_max_num;?></td>
					</tr>
 
				</table>
                
                <br /><br />
                <h12>生成记录</h12>
                <table class="addTable gs_table">
                	<tr>
                    	<th>序号</th>
                        <th>操作时间</th>
                        <th>操作人</th>
                        <th>起始号</th>
                        <th>生成数</th>
                        <th>导出</th>
                    </tr>
                
                	<?php for($i = 0, $n = count($logList); $i < $n; $i++):?>
                		<tr>
                        	<td><?php echo $i+1;?></td>
                            <td><?php echo $logList[$i]['create_at']?></td>
                            <td><?php echo $logList[$i]['create_user_id']?></td>
                            <td><?php echo $logList[$i]['start_num']?></td>
                            <td><?php echo $logList[$i]['card_count']?></td>
                            
                            <td><a href="<?php echo $exportUrl . '?log_id=' . $logList[$i]['id'] . '&c=' . urlencode($group_name . '卡号(部分' . ($i + 1) . ')')   ;?>">导出</a></td>
                        </tr>
                    <?php endfor?>
                    
                    <tr>
                    	<td colspan="20" align="right">卡号纵量：<?php echo $card_max_num;?>  &nbsp;&nbsp;&nbsp;&nbsp; <a href="<?php echo $exportUrl . '?gid=' . $gid . '&c=' . urlencode($group_name . '卡号(全)') ;?>">全部导出</a> </td>
                    </tr>
                    
                </table>
                
				 
    </div>
 
</div>

 


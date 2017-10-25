 
      
      <script type="text/javascript">
		
        jQuery(function(){
            jQuery('#codeGrid').datagrid({
                  
            })
        })
        </script>
 	<div class="easyui-layout" fit="true" border="false">
        <div region="center">
            <table id="codeGrid" url="<?php echo $jsonPageCodeUrl?>" singleSelect="true" rownumbers="true" pagination="true" border="false" fit="true"  pageList="[20,30,40,50]">
                <thead>
                    <tr>
                    	
                        <th field="card_num">卡号</th>
                        <th field="card_pwd">密码</th>
                        <!--th field="card_status">状态</th>
                        <th field="student_id">学生ID</th>
                        <th field="udate">激活时间</th-->
                       
                      	<th field="card_group">卡组ID</th>
                    </tr>
                </thead>
            </table>
 
        </div>
 	</div>
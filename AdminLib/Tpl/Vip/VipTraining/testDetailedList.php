<script type="text/javascript" src="/static/js/training.js"></script>

<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
	
        <input type="hidden" name="id" value="<?php echo $teInfo['id'];?>" />
        <input type="hidden" name="tr_id" value="<?php echo $teInfo['tr_id']?>"/>
        
			<table width="95%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
            <tr>
                <td>序号</td>
                <td>姓名</td>
                <td>状态</td>
                <td>答题次数</td>
                <td>答题时间</td>
            </tr>  
            <?php foreach($teachInfo as $key=>$val){?>
            <tr>
                <td><?php echo $val['id']?></td>
                <td><?php echo $val['te_name']?></td>                
                <td><?php echo $val['recommended_name']?></td>
                <td><?php echo $val['answer_num']?></td>
                <td><?php echo $val['answer_time']?></td>
            </tr>                
            <?php } ?>
		    </table>     
        		
	</div>
</div>
       
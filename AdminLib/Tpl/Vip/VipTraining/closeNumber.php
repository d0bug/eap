<script type="text/javascript" src="/static/js/training.js" xmlns="http://www.w3.org/1999/html"></script>
<script type="text/javascript">
var selAll = document.getElementById("selAll");
function selectAll()
{
  var obj = document.getElementsByName("teach_arr[]");
  console.log(obj);
  if(document.getElementById("selAll").checked == false)
  {
      for(var i=0; i<obj.length; i++){
        obj[i].checked=false;
      }
  }else{
          for(var i=0; i<obj.length; i++){  
            obj[i].checked=true;
          }
  }
 
} 

</script>
<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="get" novalidate action="/Vip/VipTraining/closeNumber" >
        <!--input type="hidden" name="tr_id" value="<?php echo $id;?>" /-->
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
                <tr>
                    <td>培训期:
                        <?php
                        if(!empty($trNameInfo)) {
                            ?>
                            <select name="peixun_name">
                                <option value="">请选择</option>
                                <?php
                                    foreach($trNameInfo as $k=>$trName) {
                                        if(in_array($trName['id'],array($arrGet['peixun_name'])))
                                            $chk = 'selected';
                                        else
                                            $chk = '';
                                        ?>
                                 ?>
                                        <option value="<?php echo $trName['id']?>" <?php echo $chk?>><?php echo $trName['tr_name']?></option>
                                <?php
                                  }
                                ?>
                            </select>
                            <?php
                        }
                        ?>
                        学科:
            			<?php
            			if(!empty($dictInfo)){
                        ?>
                            <select name="xueke_name">
                                <option value="">请选择</option>
                            <?php
            					foreach ($dictInfo as $k=>$module){
            						$id = $module['id'];
            						$name = $module['nianji'].$module['title'];
            						if(in_array($id,array($arrGet['xueke_name'])))
            							$chk = 'selected';
            						else
            							$chk = '';
            						?>
									<option value="<?php echo $id?>" <?php echo $chk?>><?php echo $name;?></option>
            					<?php
            					}
                            ?>
                            </select>
                       <?php
                        }
                       ?>
                        <input type="submit" name="submit" value="搜索">
                    </td>
                </tr>
            </table>
        </form>
            <form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/closeNumber" >
            <div>
                <table border="1" >
                    <span style="color: red">注:只显示未关闭账号信息</span>
                    <tr>
                        <td>选择<input type="checkbox" id="selAll" onclick="selectAll();" />全选&nbsp;&nbsp;</td>
                        <td>姓名</td>
                        <td>科目</td>
                    </tr>
                        <?php
                        if(!empty($seTrTeachInfo)) {
                            foreach ($seTrTeachInfo as $key=>$val){
                                ?>
                    <tr>
                                <td>
                                    <input type="checkbox" id="<?php echo $val['id']?>" name="teach_arr[]" value="<?php echo $val['id']?>" size="10">
                                </td>
                                <td>
                                    <?php echo $val['te_name'];?>
                                </td>
                                <td>
                                    <?php echo $val['xueke_name']?>
                                </td>
                    </tr>
                        <?php
                            }
                        }else{
                            ?>
                            <td rowspan="3">暂无信息</td>
                    <?php
                        }
                        ?>
                </table>
                <input type="hidden" name="px_name" value="<?php echo $arrGet['peixun_name']?>">
                <input type="hidden" name="xk_name" value="<?php echo $arrGet['xueke_name']?>">
                <p style="padding-left: 100px;">
                <input type="submit" name="submit" value="关闭账号"  style="height: 40px; width: 80px; font-size: 14px; "  onclick="javascript:return ConfirmDel();">
                </p>
            </div>
		</form>
	</div>
</div>
<script>
    function ConfirmDel() {
        if (confirm("确定关闭吗？")){
            return true;
        }else{
            return false;
        }
    }
</script>
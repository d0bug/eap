
		<table class="tableList" border="1" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			 <tr align="center">
           <th> 姓名  </th>
           <th> 学号  </th>
           <th> 电话  </th>
           <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
           <th><?php echo $formFormatArr['class_name'][$i];?></th>

           <?php endfor ?>

       </tr>
			<?php foreach($stuInfoArr as $stucode => $value):?>
          <tr>
          <td align="center"><?php echo $stuInfoArr[$stucode]['sstudentname'];?></td>
          <td align="center"><?php echo $stucode;?></td>
          <td align="center"><?php echo $stuInfoArr[$stucode]['sparents1phone'];?> </td>
          <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
            <?php $week_id = $data[$stucode][$i];?>
        <?php if(isset($data[$stucode][$i])):?>
          <td align="center"><?php echo createFormat($formInfoArr[$i][$week_id],$week_id,$i,2);?></td>
        <?php else:?>
          <td align="center"><div style="width:85px;height:70px;">无</div></td>
        <?php endif?>

           <?php endfor ?>

           </tr>
       <?php endforeach?>
		</table>

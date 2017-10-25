
    	<table>
        <thead>
        <tr>
            <th>序号</th>

            <th>时段</th>

           <!--  <th>班型编码</th> -->
            <th>班型名称</th>
            <th>班级编码</th>
            <th>课节</th>
            <th>任课老师</th>
            <th>提交人数</th>
            <th>出勤人数</th>
            <th>提交率</th>



            <th>题型</th>


        </tr>

        </thead>
        <tbody>
        <?php $i=1;foreach($aAnalysisList as $value){?>
        <tr>
            <td><?php echo $i;$i++?></td>

            <td><?php echo $value['nyear'],'年 ',seasonName($value['nseason']);?></td>


            <!-- <td><?php echo $value['sclasstypecode'];?></td> -->
            <td><?php echo $value['sname'];?></td>
            <td><a href="<?php echo urla($data,'sClassCode',$value['sclasscode']);?>"><?php echo $value['sclasscode'];?></a></td>
            <td><?php echo trim($value['stopic']);?></td>
            <td><?php echo $value['sTeacher'];?></td>
             <td><?php echo $value['num'];?></td>
            <td><?php echo $value['totalNum'];?></td>
            <td><font color="red">[<?php echo round($value['num']*100/$value['totalNum'],1);?>%]</font></td>

            <td><?php echo questionTyoe($value['ntype']);?></td>


        </tr>
        <?php }?>
        </tbody>
        </table>

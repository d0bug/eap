<form id="paper-edit-form" method="post" novalidate>
	<input type="hidden" name="id" value="{$info['id']}" />
	<table cellpadding="6" cellspacing="5" border="0" width="100%" class="main_table">
		<tr>
			<td width="15%"><span class="ft_red"></span> 文件名：</td>
			<td width="85%"><input style="width:100%;" name="file_name" value="{$info['file_name']}" class="easyui-validatebox textbox" data-options="required:true" autocomplete="off" /></td>
		</tr>
		<tr>
			<td width="15%"><span class="ft_red"></span> 前端显示名：</td>
			<td width="85%"><input style="width:100%;" name="show_name" value="{$info['show_name']}" class="easyui-validatebox textbox"  autocomplete="off" /></td>
		</tr>
        <tr>
            <td width="15%"><span class="ft_red"></span>前端是否显示:</td>
            <td width="85%">
                <?php
                $ckd = $chkdd = '' ;
                if($info['show_start']  == 1 ) {
                    $ckd = 'checked';
                }
                if($info['show_start']== 2) {
                    $ckdd = 'checked';
                }
                ?>
                是<input type="radio" name="show_start" value="1" <?php echo $ckd;?> >
                否<input type="radio" name="show_start" value="2" <?php echo $ckdd;?> >
            </td>
        </tr>
		<tr>
			<table cellpadding="6" cellspacing="5" border="0" width="100%" >
				<tr>
					<td colspan="2">套卷属性（保存到单题下）：</td>
				</tr>
				<tr>
					<td width="20%" align="right"><label style="color: red;">*</label>学部：</td>
					<td width="30%"><input class="easyui-combobox" style="width: 130px;" name="grade_id" id="grade_id" data-options="required:true,url:'/Question/Basic/getComboboxData?cate=GRADE_DEPT', value:{$info['grade_id']}, valueField:'grade_id', textField:'title',  editable: false, panelHeight:'auto',onSelect:function(row) {
					$('#subject_id').combobox({value:'',url: '/Question/Basic/getComboboxData?cate=SUBJECT&grade_id=' + row.grade_id});
					$('#grades').combobox({value:'',url: '/Question/Knowledge/getGradeSubject?grade_id=' + row.grade_id});
					$('#name').combobox({value:'',url:'/Question/Knowledge/getTypeName?grade_id='+row.grade_id});
					$('#test_name').combobox({value:'',url:'/Question/Basic/getTestByparams?gradeid='+$('input[name=grade_id]').val()+'&city_id='+$('input[name=city_id]').val()+'&country_id='+$('input[name=country_id]').val()});
					}">
					</td>
					<td width="20%" align="right"><label style="color: red;">*</label>学科：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="subject_id" name="subject_id" data-options="required:true,url:'/Question/Basic/getComboboxData?cate=SUBJECT&grade_id={$info['grade_id']}',value:'{$info['subject_id']}',valueField:'subject_id', textField:'title', method: 'get', editable: false, panelHeight:'auto',onSelect:function(row){
						$('#grades').combobox({value:'',url: '/Question/Knowledge/getGradeSubject?grade_id='+$('input[name=grade_id]').val()+'&subject_id='+$('input[name=subject_id]').val()});
						$('#name').combobox({value:'',url:'/Question/Knowledge/getTypeName?grade_id='+$('input[name=grade_id]').val()+'&subject_id='+$('input[name=subject_id]').val()});
					}">
					</td>
				</tr>
				<tr>
					<td width="20%" align="right"><label style="color: red;">*</label>省份：</td>
						<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="city_id" name="city_id" data-options="required: true, url: '/Question/Basic/getByCity', value:'{$info['province']}' ,valueField: 'city',textField: 'city',onSelect: function(row) {$('#country_id').combobox({value:'',url: '/Question/Basic/getCountryNameByCityId?cityid=' + row.id});}">
					</td>
					<td width="20%" align="right">市\地区：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="country_id" name="country_id" data-options="url: '/Question/Basic/getCountryIdByName?city={$info['province']}', value:'{$info['city']}' ,valueField: 'city',textField: 'city',onSelect:function(row){
					$('#test_name').combobox({value:'',url:'/Question/Basic/getTestByparams?gradeid='+$('input[name=grade_id]').val()+'&city_id='+$('input[name=city_id]').val()+'&country_id='+$('input[name=country_id]').val()})
					}" >
					</td>
				</tr>
				<tr>
					<td width="20%" align="right">考区：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="test_name" name="test_name" data-options="url: '', value:'{$info['test_name']}' ,valueField: 'test_name',textField: 'test_name'" >
					</td>
					<td width="20%" align="right"><label style="color: red;">*</label>年份：</td>
					<td width="30%">
						<input class="easyui-combobox" id="year" name="year"></input> 
					</td>
				</tr>
				<tr>
					<td width="20%" align="right">学校：</td>
					<td width="30%">
					<input style="width: 130px;" id="school" name="school" value="{$info['school']}">
					</td>
					<td width="20%" align="right">学期：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="term" name="term" data-options="url: '/Question/Knowledge/getGradeTerm', value:'{$info['term']}' ,valueField: 'term',textField: 'term'" >
					</td>
				</tr>
				<tr>
<!-- 					<td width="20%" align="right">类型：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="source" name="source" data-options="url: '/Question/Knowledge/getSource', value:'{$info['source']}',valueField: 'source',textField: 'source'" >
					</td> -->
					<td width="20%" align="right">来源：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="name" name="name" data-options="url: '/Question/Knowledge/getTypeName?grade_id='+$('input[name=grade_id]').val()+'&subject_id='+$('input[name=subject_id]').val(), value:'{$info['source']}' ,valueField: 'name',textField: 'name'" >
					</td>
				</tr>
				<tr>
					<td colspan="4" align="center"><label style="color: blue;">（以下为套卷属性，不保存到单题下）</label></td>
				</tr>
				<tr>
					<td width="20%" align="right">满分：</td>
					<td width="30%"><input type="text" style="width: 130px;"  name="score" value="{$info['score']}">分</td>
					<td width="20%" align="right">考试时间：</td>
					<td width="30%"><input type="text" style="width: 130px;"  name="duration" value="{$info['duration']}">分钟</td>
				</tr>
				<tr>
					<td width="20%" align="right">总题数：</td>
					<td width="30%"><input type="text" style="width: 130px;"  name="question_number" value="{$info['question_number']}">题</td>
					<td width="20%" align="right">年级：</td>
					<td width="30%">
					<input class="easyui-combobox" style="width: 130px;" id="grades" name="grades" data-options="url: '/Question/Knowledge/getGradeSubject?grade_id={$info['grade_id']}&subject_id={$info['subject_id']}', value:'{$info['grades']}' ,valueField: 'title',textField: 'title'" >
					</td>
				</tr>
			</table>
		</tr>
	</table>
</form>
<script type="text/javascript">
$("#year").combobox({
	value:'{$info["year"]}',
	valueField:'year',    
	textField:'year',  
	panelHeight:'auto'
});
var data = [];//创建年度数组
var startYear;//起始年份
var thisYear=new Date().getUTCFullYear();//今年
var endYear=thisYear+20;//结束年份
//数组添加值（2012-2016）//根据情况自己修改
for(startYear=thisYear-10;startYear<endYear;startYear++)
{
	data.push({"year":startYear});
}
$("#year").combobox("loadData", data);//下拉框加载数据
</script>
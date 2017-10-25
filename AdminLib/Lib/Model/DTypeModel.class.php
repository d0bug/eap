<?php
class DTypeModel{
	private $handlerPre = '';
	public function __construct() {
		$this->handlerPre = C('UFORM_HANDLER_PRE');
	}
	
	public function getDtypes() {
		return array(
			'text'=>'单行文本',
			'textarea'=>'多行文本',
			'number'=>'数值',
			'grade'=>'年级',
			'options'=>'选项组',
			'telephone'=>'电话',
		);
	}
	
	public function renderOpts($attrType, $attrName) {
		$attrName = preg_replace('/[^a-z0-9_-]/i', '', $attrName);
		if(false == $attrName) return array('errorMsg'=>'请输入字段英文标识');
		$dTypes = $this->getDtypes();
		$dTypeCaption = $dTypes[$attrType];
		$searchOpt = '';
		if($attrType != 'textarea') {
			$searchOpt = '<label style="color:green">&nbsp;&nbsp;可查询:<input type="checkbox" name="' . $attrName . '[searchable]" value="1" /></label>&nbsp;&nbsp;';
		}
		$html = '<fieldset class="attrItem item_' . $attrName . '" style="margin-top:3px">
			<legend><span><b>排序</b>：<input type="text" class="attrSeq attrSeq_' . $attrName . '" name="attrSeq[' . $attrName . ']" size="3" style="text-align:center" onkeyup="this.value=this.value.replace(/\D/g, \'\')" />&nbsp;<b>字段标识：</b><span style="color:red;font-size:14px">' .$attrName . '</span>【<span style="color:blue">' . $dTypeCaption . '</span>】</span>' . $searchOpt . '[<a href="javascript:void(0)" onclick="removeAttr(\'' . $attrName . '\')">移除</a>]</legend>
			<input type="hidden" class="attrType attrType_' . $attrName . '" name="attrType[' . $attrName . ']" value="' . $attrType . '" />
			<div><span><b>中文名称：</b><input type="text" size="12" class="attrCaption attrCaption_' . $attrName . '" name="attrCaption[' . $attrName . ']" /></span>';
		$methodName = 'render' . ucfirst($attrType) . 'Opts';
		$html .= $this->$methodName($attrName);
		$html .= '</div>
		</fieldset>';
		return array('html'=>$html);
	}
	
	private function renderTextOpts($attrName) {
		return '<span><label><input type="checkbox" name="' . $attrName . '[dis_ime]" value="1" />输入法关闭</label>
				<label><input type="checkbox"  name="' . $attrName . '[required]" value="1" />必填</label>
		</span>';
	}
	
	private function renderTextareaOpts($attrName) {
		return '<span><label><input type="checkbox" name="' . $attrName . '[required]" value="1" />必填</label></span>';
	}
	
	private function renderNumberOpts($attrName) {
		return '<span><label><input type="checkbox"  name="' . $attrName . '[required]" value="1" />必填</label>
			<label><input type="checkbox"  name="' . $attrName . '[decimal]" value="1" />允许小数</label>
			范围：<input type="text" name="' . $attrName . '[range_min]" size="2" onkeyup="this.value=this.value.replace(/[^\d\.]/g, \'\')" />-<input type="text" name="' . $attrName . '[range_max]" size="2" onkeyup="this.value=this.value.replace(/[^\d\.]/g, \'\')" />
		</span>';
	}
	
	private function renderGradeOpts($attrName) {
		return '<span>&nbsp;年级选项将默认从活动信息获取数据</span>';
	}
	
	private function renderOptionsOpts($attrName) {
		return '<span>渲染方式:<select name="'.$attrName . '[render_type]">
				<option value="radio">单选按钮</option>
				<option value="checkbox">复选框</option>
				<option value="select">下拉菜单</option>
		</select><br />数据源设置(每行一条，值,文本,限额(若需要)以|分隔,举例：1|数学|15)</span>：<div><textarea style="resize:none;width:300px;height:50px;" name="' . $attrName . '[data_source]"></textarea></div>';
	}
	
	private function renderTelephoneOpts($attrName) {
		return '<span>
		<label><input type="checkbox" name="' . $attrName . '[required]" value="1" />必填</label>
		<label><input type="checkbox" name="' . $attrName . '[sms_valid]" value="1" />短信校验</label></span>';
	}
	
	
	public function renderItem($item) {
		$typeName = $item['attr_type'];
		$methodName = 'render' . ucfirst($typeName);
		return $this->$methodName($item);
	}
	
	private function renderHidden($item) {
		$opts = $item['attr_opts'];
		if(false == is_array($opts)) {
			$opts = json_decode($opts, true);
		}
		$attrValue = trim($opts['value']);
		return array('html'=>'<input type="hidden" name="' . $item['attr_name'] . '" value="' . $attrValue . '" class="uform_item uform_hidden" />');
	}
	
	private function renderText($item) {
		$html = '<input {{attrs}} type="text" name="' . $item['attr_name'] . '" class="uform_item uform_text  uform_'. $item['attr_name'] .' {{class}}';
		$opts = $item['attr_opts'];
		if(isset($opts['required'])) {
			$html .= ' uform_required ';
		}
		$html .= ' {{css}}"';
		if(isset($opts['dis_ime'])) {
			$html .= ' style="ime-mode:disabled"';
		}
		
		$html .=' {{js}} />';
		return array('attr_type'=>'text',
					 'label'=>$item['attr_caption'],
					 'html'=>$html,
					 'attrs'=>'',
					 'css'=>'',
					 'class'=>'',
					 'js'=>'');
	}
	
	private function renderTextarea($item) {
		$html = '<textarea {{attrs}} name="' . $item['attr_name'] . '" class="uform_item uform_textarea  uform_'. $item['attr_name'] .' {{class}}';
		$opts = $item['attr_opts'];
		if(false == is_array($opts)) {
			$opts = json_decode($opts, true);
		}
		if($opts['required']) {
			$html .= ' uform_required';
		}
		$html .=' {{css}}" {{js}}></textarea>';
		return array('label'=>$item['attr_caption'],
					 'html'=>$html,
					 'attrs'=>'',
					 'css'=>'',
					 'class'=>'',
					 'js'=>'');
	}
	
	private function renderNumber($item){
		$html = '<input {{attrs}} type="tel" name="' . $item['attr_name'] . '" class="uform_item uform_number  uform_'. $item['attr_name'] .' {{class}}';
		$opts = $item['attr_opts'];
		if(false == is_array($opts)) {
			$opts = json_decode($opts, true);
		}
		if($opts['required']) {
			$html .= ' uform_required';
		}
		$html .= ' {{css}}"';
		if($opts['decimal']) {
			$patn = '/[^\d\.]/g';
		} else {
			$patn = '/[^\d]/g';
		}
		if(isset($opts['min'])) {
			$html .= ' min="' . $opts['min'] . '"';
		}
		if(isset($opts['max'])) {
			$html .= ' max="' . $opts['max'] . '"';
		}
		$html .= ' onkeyup="this.value=this.value.replace(' . $patn . ', \'\')" {{js}}/>';
		
		return array('attr_type'=>'text',
					 'label'=>$item['attr_caption'],
					 'html'=>$html,
					 'attrs'=>'',
					 'css'=>'',
					 'class'=>'',
					 'js'=>'');
		
	}
	
	private function renderOptions($item) {
		$opts = $item['attr_opts'];
		if (false == is_array($opts)) {
			$opts = json_decode($opts, true);
		}
		if($opts['data_source'] && false == is_array($opts['data_source'])) {
			$rows = explode("\n", $opts['data_source']);
			$opts['data_source'] = array();
			foreach ($rows as $row) {
				$row = trim($row);
				list($key, $val, $limit) = explode('|', $row);
				$opts['data_source'][$key] = $val;
				if(null !== $limit) {
					$opts['data_limit'][$key] = $limit;
				}
			}
		}
		file_put_contents('/data/wwwroot/eap/AdminLib/Runtime/Data/opt', var_export($opts, true));
		if(isset($opts['data_limit'])) {
			$totalCnt =0;
			foreach ($opts['data_limit'] as $value=>$limit) {
				$totalCnt += abs($limit);
			}
			$uFormModel = D('UForm');
			$opts['data_source'] = $uFormModel->getFreeOptions($item, $opts);
			if(false == $opts['data_source']) {
				return false;
			}
		}
		switch ($opts['render_type']) {
			case 'checkbox';
				$html = '';
				foreach ($opts['data_source'] as $key=>$val) {
					$html .= '<label class="uform_label uform_label_' . $item['attr_name'] . ' {{class}} "><input type="checkbox" class="uform_item uform_checkbox" {{css}} name="' . $item['attr_name'] . '[]" itemName="' . $item['attr_name'] . '" value="' . $key . '" />' . $val . '</label>&nbsp;';
				}
			break;
			case 'radio':
				$html = '';
				$i = 0;
				foreach ($opts['data_source'] as $key=>$val) {
					$html .= '<label class="uform_label uform_label_' . $item['attr_name'] . ' {{class}}"><input type="radio" itemName="' . $item['attr_name'] . '" class="uform_item uform_radio" {{css}} name="' . $item['attr_name'] . '" value="' . $key . '"';
					if($i ==0) {
						$html .= ' checked="true"';
					}
					$html .= ' />' . $val . '</label>&nbsp;';
					$i++;
				}
			break;
			case 'select':
				$html = '<select name="' . $item['attr_name'] . '" class="uform_item uform_select uform_required  uform_'. $item['attr_name'] .' {{class}}" {{css}} {{js}}>';
				$html .= '<option value="-9999">请选择' . $item['attr_caption'] . '</option>';
				foreach ($opts['data_source'] as $key=>$val) {
					$html .= '<option value="' . $key . '">' . $val . '</option>';
				}
				$html .= '</select>';
			break;
		}
		return array('label'=>$item['attr_caption'],
				 	 'html'=>$html,
				 	 'css'=>'',
				 	 'class'=>'',
				 	 'js'=>'');
	}
	
	private function renderTelephone($item) {
		$id = md5($item['id']);
		$html = '<input {{attrs}} type="tel" id="telephone_' . $id . '" name="' . $item['attr_name'] . '" class="uform_item uform_telephone {{class}}';
		$opts = $item['attr_opts'];
		if(false == is_array($opts)) {
			$opts = json_decode($opts, true);
		}
		if($opts['required']) {
			$html .= ' uform_required';
		}
		$html .= ' {{css}}"';
		$html .= ' onkeyup="this.value=this.value.replace(/\D/g, \'\');if(false == /^1\d*/.test(this.value)){this.value=\'\'};if(this.value.length >11){this.value=this.value.substring(0,11)}" {{js}}/>';
		if($opts['sms_valid']) {
			$smsHtml = '
					<input {{smsAttrs}} type="tel" class="uform_item uform_text uform_required uform_smsCode {{smsClass}}" style="width:110px;ime-mode:disabled;{{smsCss}}" name="smsCode_' . $item['attr_name'] . '" placeholder="输入短信校验码" /><input class="uform_item uform_button" type="button" value="获取短信校验码" id="smsBtn_' . $id . '" />
			<script type="text/javascript">
				window.timers = window.timers || {}
				jQuery(function(){
					jQuery("#smsBtn_' . $id . '").click(function(){
						var telephone = jQuery.trim(jQuery("#telephone_' . $id . '").val());
						if(telephone) {
							jQuery.getJSON("' . $this->handlerPre . 'SendSms?callback=?", {_tm:(new Date()).getTime(), actId:"' . $item['act_id'] . '", telephone:telephone}, function(data){
								if(data.errorMsg) {
									alert(data.errorMsg);
								} else {
									alert("校验短信已发送")
									jQuery("#smsBtn_' . $id . '").attr("disabled", true).data("_tm", 60);
									window.timers["timer_' . $id . '"] = setInterval(function(){
										var _tm = parseInt(jQuery("#smsBtn_' . $id . '").data("_tm"), 10);
										jQuery("#smsBtn_' . $id . '").val(_tm + "秒后重新发送短信！");
										_tm --;
										if(_tm == 0) {
											clearInterval(window.timers["timer_' . $id . '"]);
											jQuery("#smsBtn_' . $id . '").val("获取短信校验码").attr("disabled", false);
										}
										jQuery("#smsBtn_' . $id . '").data("_tm", _tm);
									}, 1000)
								}
							})
						} else {
							alert("请输入手机号码");
						}
					})
				})
			</script>';
			
		}
		return array('attr_type'=>'text',
					 'label'=>$item['attr_caption'],
				 	 'html'=>$html,
				 	 'attrs'=>'',
				 	 'css'=>'',
				 	 'js'=>'',
				 	 'smsHtml'=>$smsHtml,
				 	 'smsCss'=>'',
				 	 'smsClass'=>'');
	}
	
	private function renderGrade($item) {
		$grades = $item['attr_opts']['data_source'];
		$gradeYearModel = D('GradeYear');
		$gradeYears = $gradeYearModel->getGradeYears();
		$dataSrouce = array();
		foreach ($gradeYears as $year=>$grade){
			if (in_array($year, $grades)) {
				$dataSrouce[$year] = $grade;
			}
		}
		$item['attr_opts']['data_source'] = $dataSrouce;
		return $this->renderOptions($item);
	}
	
	
}
?>
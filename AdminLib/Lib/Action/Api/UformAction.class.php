<?php
class UformAction extends ApiCommAction {
	private  $smsSessId = 'smsSessId';
	protected function getFormInfo($formId) {
		$uFormModel = D('UForm');
		return $uFormModel->renderFormInfo($formId);
	}
	
	protected function getFormHtml($formId) {
		$uFormModel = D('UForm');
		return $uFormModel->renderFormHtml($formId);
	}
	
	protected function findGradeAct($dept, $grade, $filter) {
		$uFormModel = D('UForm');
		$filter = array('filter'=>$filter);
		$act =  $uFormModel->findGradeAct($dept, $grade, $filter);
		if(null  === $act) {
			return $this->findGradeAct($dept, $grade, $filter);
		}
		return $act;
	}
	
	protected function findLevelAct($dept, $level, $filter) {
		$uFormModel = D('UForm');
		$filter = array('filter'=>$filter);
		$act = $uFormModel->findLevelAct($dept, $level, $filter);
		if(null === $act) {
			return $this->findLevelAct($dept, $level, $filter);
		}
		return $act;
	}
	
	protected function saveRecord($record) {
		$uFormModel = D('UForm');
		$formId = $record['form_id'];
		$formInfo = $uFormModel->formInfo($formId);
		foreach ($formInfo['attrList'] as $attr) {
			if($attr['attr_type'] == 'telephone' && $attr['attr_opts']['sms_valid']) {
				if($this->validSms($formId, $record[$attr['attr_name']], $record['smsCode_' . $attr['attr_name']])) {
					unset($record['smsCode_' . $attr['attr_name']]);
				} else {
					return false;
				}
				break;
			}
		}
		
		return $uFormModel->saveRecord($record);
	}
	
	protected function sendSms($formId, $mobile) {
		if(SysUtil::isMobile($mobile)) {
			$key = md5($this->smsSessId . '_' . $formId . '_' . $mobile);
			$cache = NCache::getCache();
			import('ORG.Util.String');
			import('COM.MsgSender.SmsSender');
			$smsCode = String::randString(6, 1);
			if(SmsSender::sendSms($mobile, '短信校验码为：' . $smsCode . ',本验证码仅用作手机校验使用！')) {
				$cache->set('smsCode', $key, $smsCode);
				return $smsCode;
			}
		}
		return false;
	}
	
	protected function validSms($formId, $mobile, $smsCode) {
		$key = md5($this->smsSessId . '_' . $formId . '_' . $mobile);
		$cache = NCache::getCache();
		$cacheSmsCode = $cache->get('smsCode', $key);
		if(false == $smsCode || false == $cacheSmsCode) return false;
		if($cacheSmsCode == $smsCode) {
			$isValid = true;
		} else {
			$isValid = false;
		}
		
		$cache->delete('smsCode', $key);
		
		return $isValid;
	}
}
?>
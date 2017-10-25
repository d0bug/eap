<?php
/* 审核管理 */
class AuditAction extends QuestionCommAction {
	public function audit_list() {
		$basicModel = D ( 'Basic' );
		$condition ['is_submit'] = 1;
		$condition = $this->get_roleCondition ( $condition, 1 );
		$count = $basicModel->getQuestionStatistics ( $condition );
		$total = $basicModel->getQuestionsCountByWhere ( $condition );
		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	public function render_audit_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		
		$condition = array (
				'gradedept' => $params ['gradedept'],
				'subject' => $params ['subject'],
				'knowledge' => $params ['knowledge'],
				'startdate' => $params ['startdate'],
				'enddate' => $params ['enddate'],
				'is_submit' => 1,
				'order' => 'status' 
		);
		
		$condition = $this->get_roleCondition ( $condition, 1 );
		$questions = D ( 'Basic' )->getQuestionsByWhere ( $condition, $currentPage, $pageSize );
		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	public function get_audit_list_count() {
		$params = $this->_param ();
		$condition = array (
				'gradedept' => $params ['gradedept'],
				'subject' => $params ['subject'],
				'knowledge' => $params ['knowledge'],
				'startdate' => $params ['startdate'],
				'enddate' => $params ['enddate'],
				'is_submit' => 1 
		);
		
		$total = D ( 'Basic' )->getQuestionsCountByWhere ( $condition );
		echo $total;
	}
	public function auditQuestion() {
		if (D ( 'Basic' )->auditQuestion ( $_POST )) {
			$this->success ();
		}
		$this->error ();
	}
}
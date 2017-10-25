<?php
/**
 * 分为添加预约课程，预约课程管理
 *
 */
class IndexAction extends AppCommAction{
	/**
	 * 默认的入口，暂时只有一个课程，所以直接跳转了
	 * @return [type] [description]
	 */
	public function index() {
		$mol = D('Reserve');
		$active_info =  $mol->getReserveList();
		$files = $mol->getReserveInfo($active_info['id']);
		$time_name_arr = C('TIME_TYPE');

		foreach($files as $value) {
			$data[$value['week_id']][$value['class_id']]               = $value;
		}
		dump($data);
	}
	public function addReserve() {
		//
	}

	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	protected function notNeedLogin(){

	}

}


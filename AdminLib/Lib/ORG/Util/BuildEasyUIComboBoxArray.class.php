<?php

/**
 * 由一个带parentid的数组生成一个带children的树形数组
 * 生成EasyUI的Tree的json格式
 */
class BuildEasyUIComboBoxArray {
	private $idKey = 'id'; // 主键的键名
	private $pidKey = 'parent_id'; // 父ID的键名
	private $textField = 'name'; // 显示text字段
	private $root = 0; // 最顶层fid
	private $data = array (); // 源数据
	private $treeArray = array (); // 属性数组
	function __construct($data, $idKey, $pidKey, $root, $textField) {
		if ($idKey)
			$this->idKey = $idKey;
		if ($pidKey)
			$this->pidKey = $pidKey;
		if ($textField)
			$this->textField = $textField;
		if ($root)
			$this->root = $root;
		if ($data) {
			$this->data = $data;
			$this->getChildren ( $this->root );
		}
	}
	
	/**
	 * 获得一个带children的树形数组
	 *
	 * @return multitype:
	 */
	public function getTreeArray() {
		// 去掉键名
		return array_values ( $this->treeArray );
	}
	
	/**
	 *
	 * @param int $root
	 *        	父id值
	 * @return null or array
	 */
	private function getChildren($root) {
		foreach ( $this->data as &$node ) {
			if ($root == $node [$this->pidKey]) {
				$node ['text'] = $node [$this->textField];
				$node ['children'] = $this->getChildren ( $node [$this->idKey] );
				$children [] = $node;
			}
			// 只要一级节点
			if ($this->root == $node [$this->pidKey]) {
				$this->treeArray [$node [$this->idKey]] = $node;
			}
		}
		return $children;
	}
}

?>
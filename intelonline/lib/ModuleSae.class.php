<?php
class ModuleSae {
	public $db;
	public $modelName = '';
	public $config = '';
	public $pages = '';
	function __construct() {
		$this->db = new SaeDb ( SAE_MYSQL_HOST_M . ':' . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS );
	}
	function listinfo($tableName, $where = "1=1", $fields = '*', $page = 1, $offset = 20, $order = '') {
		if (is_array ( $tableName )) {
			$tableName = implode ( ',', $tableName );
		}
		
		$order = ! empty ( $order ) ? ' order by ' . $order : '';
		
		$sql = "SELECT count(*) as num FROM `" . $tableName . "` where " . $where;
		$nums_array = $this->db->getArray ( $sql );
		$sql2 = "SELECT " . $fields . "  FROM `" . $tableName . "`  where " . $where . ' ' . $order . " limit " . ($page - 1) * $offset . "," . $offset;
		$nums = $nums_array [0] ['num'];
		$result = $this->db->getArray ( $sql2 );
		$subPages = new SubPages ( $offset, $nums, $page, 5, "/index.php/user/user/p/", 2 );
		$this->pages = $subPages->subPageCss2 ();
		return $result;
	}
	function __call($name, $arguments) {
		echo "方法名：" . $name . '不存在！';
		die ();
	}
	function __set($name, $value) {
		echo "设置一个对象的属性时：" . $name . ' 不存在！';
		die ();
	}
	function __get($name) {
		echo "读取一个对象的属性：" . $name . ' 不存在！';
		die ();
	}
}
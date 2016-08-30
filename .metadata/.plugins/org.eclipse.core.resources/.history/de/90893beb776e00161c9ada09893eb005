<?php

/**
 * 模型基类
 * 主要是针对数据库操作
 * @author xiaofeng 2012 <john.doe@example.com>
 * @copyright (c) zeverTech, 2012
 * @version $Id
 */
require_once LIB_PATH . 'Mysql.class.php';
class Module {
	public $db;
	public $modelName = '';
	public $config = '';
	public $userinfo = '';
	public $userid = 0;
	
	/**
	 * 构造方法
	 * 
	 * @param type $modelName        	
	 */
	function __construct($modelName) {
		$this->modelName = ($modelName);
		$this->connect ();
	}
	
	/**
	 * 链接数据库
	 * 
	 * @global type $dbConfig
	 */
	public function connect() {
		global $dbConfig;
		var_dump ( $dbConfig );
		$this->config = $dbConfig ['default'];
		var_dump ( $dbConfig ['default'] );
		var_dump ( $this->config );
		$con = $this->config;
		$tableName = $con ['DBname'];
		$this->db = new Mysql ( $this->config, $tableName );
	}
	
	/**
	 * 查询记录集
	 *
	 * @param mixed $where
	 *        	条件
	 * @return array
	 *
	 */
	function select($fields = NULL, $where = NULL, $groupby = NULL, $orderby = NULL, $limit = NULL) {
		$result = $this->db->select ( $fields, $where, $groupby, $orderby, $limit );
		
		return $result;
	}
	
	/**
	 * 查询一条记录集
	 *
	 * @param mixed $where
	 *        	条件
	 * @return array
	 *
	 */
	function getOne($fields = NULL, $where = NULL, $groupby = NULL, $orderby = NULL) {
		$result = $this->db->select ( $fields, $where, $groupby, $orderby, array (
				0,
				1 
		) );
		return $result [0];
	}
	
	/**
	 * 查询并返回结果集
	 *
	 * @param string $sql        	
	 * @return array[2]
	 */
	function query($sql) {
		$data = $this->db->query ( $sql );
		return $data;
	}
	
	/**
	 * 统计满足条件的记录数
	 *
	 * @param mixed $where
	 *        	条件
	 * @return int
	 */
	function count($where = NULL) {
		return $this->db->count ( $where );
	}
	
	/**
	 * 取第一条数据
	 *
	 *
	 * @param mixed $where
	 *        	条件
	 * @return array
	 */
	function first($where = NULL) {
		return $this->db->first ( $where );
	}
	function newFirst($where = NULL) {
		return $this->db->newFirst ( $where );
	}
	
	/**
	 * 插入一条数据
	 *
	 * @param array[1] $row
	 *        	<列名>=><值>
	 * @return int 新插入行的ID
	 */
	function insert($row) {
		return $this->db->insert ( $row );
	}
	
	/**
	 * 插入或更新一条数据
	 *
	 * @param array[1] $row
	 *        	<列名>=><值>
	 * @return int 影响的行数
	 */
	function replace($row) {
		return $this->db->replace ( $row );
	}
	
	/**
	 * 修改表中的部分数据
	 *
	 * @param array[1] $row
	 *        	<列名>=><值>
	 * @param mixed $where
	 *        	请参考createWhere
	 * @return int
	 */
	function update($row, $where = null) {
		return $this->db->update ( $row, $where );
	}
	
	/**
	 * 删除表中的部分数据
	 *
	 * @param mixed $where
	 *        	请参考createWhere
	 * @return int
	 */
	function delete($where) {
		return $this->db->delete ( $where );
	}
	
	/**
	 * 删除表中全部数据
	 *
	 * @return int
	 */
	function deleteAll() {
		return $this->db->deleteAll ();
	}
	
	/**
	 * 清空表数据
	 *
	 * @return int
	 */
	function truncate() {
		return $this->db->truncate ();
	}
	
	/**
	 * 事务回滚开始标记
	 */
	function beginTransaction() {
		$this->db->beginTransaction ();
	}
	function getSql() {
		return $this->db->getSql ();
	}
	
	/**
	 * 事务回滚提交标记
	 */
	function commit() {
		$this->db->commit ();
	}
	
	/**
	 * 事务开始回滚标记
	 */
	function rollBack() {
		$this->db->rollBack ();
	}
	function __call($name, $arguments) {
		return apiData ( '方法名：' . $name . '不存在！', 1027, '/module' );
	}
	function __set($name, $value) {
		// 设置需要操作的表名
		if ($name == 'tableName') {
			$this->db->tableName = gtn ( $value );
		}
		return apiData ( '设置一个对象的属性时：' . $name . ' 不存在！', 1027, '/module' );
	}
	function __get($name) {
		return apiData ( '读取一个对象的属性：' . $name . ' 不存在！', 1027, '/module' );
	}
}
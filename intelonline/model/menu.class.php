<?php
/**
 * menu.class.php
 * 
 * @author:Harry
 * @link:http://haoshengzhide.com/
 * @since:2013.12.04 
 */
class menu extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'menu';
	}
	
	/**
	 * 获取所有自定义菜单内容
	 * 
	 * @return array
	 */
	public function getAllMenu() {
		// $sql = 'SELECT * FROM b_menu WHERE pid=0 AND account_id=' . $aid;
		$result = $this->select ( NULL, 'pid = 0', NULL, 'id' );
		if ($result) {
			foreach ( $result as $key => $val ) {
				// $sql = 'SELECT * FROM b_menu WHERE pid=' . $val['id'] . ' AND account_id=' . $aid;
				$childResult = $this->select ( '*', 'pid = ' . $val ['id'], NULL, 'id' );
				if ($childResult) {
					$result [$key] ['child'] = $childResult;
				}
			}
		}
		return $result;
	}
	
	/**
	 * 获取菜单内容
	 *
	 * @param varchar $where        	
	 * @return array
	 */
	public function getMenu($where = NULL) {
		$this->tableName = 'menu';
		$return = $this->select ( NULL, $where );
		return $return;
	}
	
	/**
	 * 获取配置信息
	 *
	 * @return array
	 */
	public function readAppConfig() {
		return array (
				'appid' => appid,
				'appsecret' => appsecret 
		);
	}
	
	/**
	 * 保存菜单名
	 * 
	 * @param type $data        	
	 * @param type $aid        	
	 * @return type
	 */
	public function saveMenu($data) {
		$saveData = array ();
		$saveData ['name'] = $data;
		// 生成菜单click时间键值,不重复,不允许修改
		$saveData ['key'] = passwordProcess ( $data . time (), 5, 32, true );
		// 设定菜单所属用户id
		$saveData ['uid'] = 1;
		$saveData ['pid'] = 0;
		$saveData ['ctime'] = time ();
		$id = $this->insert ( $saveData );
		return ( int ) $id;
	}
	
	/**
	 * 更新菜单名称
	 *
	 * @param int $id
	 *        	ID
	 * @param string $value
	 *        	名子
	 * @param string $where
	 *        	其他条件
	 *        	
	 */
	public function UpdateNameMenu($id, $value) {
		$saveData = array ();
		$saveData ['name'] = $value;
		$saveData ['key'] = passwordProcess ( $value . time (), 5, 32, true );
		
		$result = $this->update ( $saveData, 'id = ' . $id );
		return ( int ) $result;
	}
	
	/**
	 * 取消一级菜单设置
	 * 
	 * @param type $data        	
	 * @param type $aid        	
	 * @return type
	 */
	public function cancelMenuSet($data) {
		$saveData = array ();
		$saveData ['rid'] = '';
		$saveData ['url'] = '';
		$saveData ['type'] = '1';
		$result = $this->update ( $saveData, 'id = ' . $data ['id'] );
		return ( int ) $result;
	}
	
	/**
	 * 保存子菜单名
	 * 
	 * @param type $data        	
	 * @param type $aid        	
	 * @return type
	 */
	public function saveChildMenu($data, $pid) {
		$saveData = array ();
		$saveData ['name'] = $data;
		// 生成菜单click时间键值,不重复,不允许修改
		$saveData ['key'] = passwordProcess ( $data . time (), 5, 32, true );
		// 设定菜单所属用户id
		$saveData ['uid'] = 1;
		// $saveData['rid'] = '';
		// $saveData['url'] = '';
		$saveData ['pid'] = $pid;
		$saveData ['ctime'] = time ();
		
		$id = $this->insert ( $saveData );
		return ( int ) $id;
	}
	
	/**
	 * 子菜单统计
	 *
	 * @param int $id
	 *        	ID
	 * @param string $where
	 *        	其他条件
	 *        	
	 */
	public function CountChildMenu($id) {
		return ( int ) $this->count ( 'pid = ' . $id );
	}
	
	/**
	 * 删除菜单
	 *
	 * @param int $id
	 *        	ID
	 * @param string $where
	 *        	其他条件
	 *        	
	 */
	public function DeleteMenu($id) {
		return $this->delete ( 'id = ' . $id );
	}
	
	/**
	 * 获取菜单详情
	 *
	 * @param int $id
	 *        	the ID of menu
	 * @return array
	 */
	public function GetMeunInfo($id) {
		return $this->select ( NULL, 'id = ' . $id );
	}
	
	/**
	 * 更新菜单内容
	 *
	 * @param int $id        	
	 * @param varchar $content        	
	 * @return int
	 */
	public function updateMenuMsg($id, $content, $type) {
		$info = array ();
		$info ['content'] = $content;
		$info ['type'] = $type;
		$return = $this->update ( $info, 'id = ' . $id );
		return ( int ) $return;
	}
}
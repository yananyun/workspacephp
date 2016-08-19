<?php
/**
 * 自动应答model
 * 
 * @author wangying
 * @copyright zevertech
 * 2014/06/12
 */
class autoresponse extends Module {
	private $scanPrize;
	public function __construct($platform = 'default') {
		// 设定平台调用的数据库配置项
		$this->platform = $platform;
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		$this->scanPrize = new scanPrize ();
		// 连接数据库
		$this->connect ();
	}
	public function add_autoresponse($info) {
		$this->tableName = 'autoresponse';
		return $this->db->insert ( $info );
	}
	public function edit_response($id) {
		$sql = "select * from sys_autoresponse where id={$id} limit 1";
		$res = $this->query ( $sql );
		$res [0] ['response_restime_start'] = substr ( $res [0] ['response_restime_start'], 0, 10 );
		$res [0] ['response_restime_end'] = substr ( $res [0] ['response_restime_end'], 0, 10 );
		return $res [0];
	}
	public function edit_response_do($id, $info) {
		$this->tableName = "autoresponse";
		$res = $this->db->update ( $info, " id = {$id}" );
		return $res;
	}
	public function add_subscriberesponse($info) {
		$this->tableName = 'autoresponse';
		return $this->db->insert ( $info );
	}
	public function del_response($id) {
		$sql = 'delete from `sys_autoresponse` where id = ' . $id;
		$result = $this->query ( $sql );
		file_put_contents ( './aaaaaa.html', $this->getSql () );
		return $result;
	}
	public function autoresponse_list($param) {
		$p = $param ['p'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$where = '';
		$sql = "select * from `sys_autoresponse`  WHERE 1 " . $where . 'order by response_genre' . $limit;
		$data = $this->query ( $sql );
		foreach ( $data as &$val ) {
			$val ['response_restime_start'] = substr ( $val ['response_restime_start'], 0, 10 );
			$val ['response_restime_end'] = substr ( $val ['response_restime_end'], 0, 10 );
		}
		$sql_count = "select count(*) as num from `sys_autoresponse`  WHERE 1 " . $where;
		$tmp = $this->db->query ( $sql_count );
		$total = $tmp [0] ['num'];
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
	/**
	 * 查询用户分组
	 * 
	 * @return Ambigous <multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	public function groupmsg() {
		$sql = "select id,name from sys_membergroup where status='1'";
		return $this->query ( $sql );
	}
	/**
	 * 查询素材详情
	 * 
	 * @param int $mid        	
	 */
	public function sel_material($mid) {
		$sql = "select * from sys_material a left join sys_material_article b on a.id=b.mid where a.id={$mid}";
		$res = $this->query ( $sql );
		foreach ( $res as &$val ) {
			$val ['ctime'] = date ( 'Y-m-d', $val ['ctime'] );
			$val ['content'] = $val ['description'];
		}
		return $res [0];
	}
	public function findKeyword($keyword = '') {
		$sql = "select * from sys_autoresponse where response_keyword='$keyword' and response_genre='keyword' and isdel='0'";
		$result = $this->query ( $sql );
		return $result ? $result [0] : array ();
	}
}
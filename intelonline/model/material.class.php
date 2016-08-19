<?php
/**
 * 动态素材模型类
 * @author zever
 *
 */
class material extends Module {
	public function __construct() {
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		// 连接数据库
		$this->connect ();
		$this->tableName = 'material';
	}
	public function getNewMid() {
		$sql = "select max(id) as mid from " . gtn ( 'material' );
		$tmp = $this->db->query ( $sql );
		$mid = 1;
		if ($tmp) {
			$mid = $tmp [0] ['mid'] + 1;
		}
		return $mid;
	}
	
	/**
	 * 根据文章的id获取素材信息
	 * 
	 * @param number $id        	
	 * @return Ambigous <multitype:, multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	function getArticleById($id = 0) {
		$sql = "select ma.*,m.ctime from sys_material as m INNER JOIN sys_material_article as ma on ma.mid = m.id where ma.id = $id and ma.isdel ='0' and m.isdel='0'";
		$result = $this->db->query ( $sql );
		return $result ? $result [0] : array ();
	}
	/**
	 * 添加素材
	 * 
	 * @param unknown $info        	
	 * @return Ambigous <number, boolean, string>
	 */
	public function addMaterial($info = array()) {
		$this->tableName = 'material';
		$result = $this->insert ( $info );
		return $result;
	}
	/**
	 * 编辑素材
	 * 
	 * @param unknown $info        	
	 * @param string $where        	
	 * @return Ambigous <number, boolean, string>
	 */
	public function updateMaterial($info = array(), $where = '') {
		$this->tableName = 'material';
		$result = $this->update ( $info, $where );
		return $result;
	}
	
	/**
	 * 添加图文
	 */
	public function addNews($news = array(), $mid = 0) {
		$sql = "update " . gtn ( 'material' ) . "_article set isdel='1' where mid = $mid";
		$this->db->query ( $sql );
		$result = false;
		if (is_array ( $news )) {
			$this->tableName = 'material_article';
			foreach ( $news as $new ) {
				$aid = $new ['aid'];
				if ($aid) {
					unset ( $new ['aid'] );
					$result = $this->update ( $new, "id = $aid" );
				} else {
					$result = $this->insert ( $new );
				}
			}
		}
		return $result;
	}
	/**
	 * 移除素材
	 * 
	 * @param number $mid        	
	 * @return Ambigous <multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	public function deleteMaterial($mid = 0) {
		$sql = "update " . gtn ( 'material' ) . "_article set isdel='1' where mid = $mid";
		$this->db->query ( $sql );
		
		$sql = "update " . gtn ( 'material' ) . " set isdel='1' where id = $mid";
		$result = $this->db->query ( $sql );
		return $result;
	}
	public function getList($param) {
		$type = $param ['type'];
		$title = $param ['title'];
		$p = $param ['p'] ? $param ['p'] : 1;
		$pagesize = $param ['pagesize'] ? $param ['pagesize'] : 10;
		
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$where = " where 1 = 1 and type='{$type}' and isdel='0' ";
		
		$orderby = ' order by uptime desc ';
		
		$sql = 'select * from ' . gtn ( 'material' ) . $where . $orderby . $limit;
		
		$data = $this->db->query ( $sql );
		if ($data) {
			foreach ( $data as $k => $v ) {
				$mid = $v ['id'];
				$wer = " where mid = $mid and isdel='0' ";
				if ($title) {
					$wer .= " and title like '%" . $title . "%' ";
				}
				$sql = "select * from " . gtn ( 'material' ) . "_article " . $wer . " order by id asc";
				$tmp = array ();
				$tmp = $this->db->query ( $sql );
				if (count ( $tmp )) {
					$data [$k] ['articles'] = $tmp;
				} else {
					unset ( $data [$k] );
				}
			}
		}
		// total
		if ($title) {
			$sql = "select count(*) as num from sys_material as m INNER JOIN sys_material_article as a on m.id = a.mid  where m.isdel='0' and a.isdel='0' and m.type='$type' and a.title like '%" . $title . "%'  ";
		} else {
			$sql = 'select count(*) as num from ' . gtn ( 'material' ) . $where;
		}
		$tmp = $this->db->query ( $sql );
		$total = $tmp [0] ['num'];
		
		$result = array ();
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
	/**
	 * 根据mid 获取素材图文
	 * 
	 * @param number $mid        	
	 * @return Ambigous <multitype:, multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	function getArticleByMid($mid = 0) {
		$sql = "select * from " . gtn ( 'material' ) . "_article where mid = $mid and isdel='0' order by id asc";
		$result = $this->db->query ( $sql );
		return $result ? $result : array ();
	}
	public function getMInfo($where, $single = FALSE) {
		$this->tableName = 'material';
		if ($single) {
			$return = $this->newFirst ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	
	// 搜索
	public function getInfo($where, $select = 1) {
		$this->tableName = 'material_article';
		if ($select == 1) {
			return $this->select ( NULL, $where );
		} else {
			return $this->first ( $where );
		}
	}
	
	/**
	 * 获取素材详情信息
	 * 
	 * @param number $id        	
	 */
	function getMaterialInfo($id = 0) {
		// 获取素材
		$sql = "select * from " . gtn ( 'material' ) . " where isdel='0' and id = $id";
		
		$this->tableName = 'material';
		$data = $this->select ( NULL, array (
				'isdel' => '0',
				'id' => $id 
		) );
		$result = $data [0];
		$return = array ();
		$return ['uptime'] = $result ['uptime'];
		if ($result) {
			if ($result ['type'] == 'news') {
				$return ['type'] = 'news';
				$sql = "select id,title,description,url,thumb as picurl from `" . gtn ( 'material_article' ) . "` where mid = $id and isdel='0' order by id asc";
				$tmp = array ();
				$tmp = $this->db->query ( $sql );
				if ($tmp) {
					foreach ( $tmp as $k => $v ) {
						if (strpos ( $val ['picurl'], 'http://' ) === FALSE) // 本地URL的时候
{
							$tmp [$k] ['picurl'] = rtrim ( APP_PATH, '/' ) . $v ['picurl'];
						}
						if (empty ( $v ['url'] )) {
							$tmp [$k] ['url'] = APP_PATH . 'index.php/article/material_article/id/' . $v ['id'];
						}
						unset ( $tmp [$k] [id] );
					}
				}
				$return ['type'] = 'news';
				$return ['content'] ['articles'] = $tmp;
			} elseif ($result ['type'] == 'image') {
				$WECHAT = new wechatAction ();
				$access_token = $WECHAT->getAccessTokenByOpenid ();
				$API = new WechatApi ( $access_token );
				$picMediaInfo = $API->getMediaIdByUpload ( 'image', $result ['filepath'] );
				$return ['type'] = 'image';
				$return ['content'] = array (
						'media_id' => $picMediaInfo ['media_id'] 
				);
			}
		}
		// return $result;
		return $return;
	}
}
<?php

/**
 * 会员模型类
 * @author zever
 *
 */
class member extends Module {
	public function __construct($platform = 'default') {
		// 设定平台调用的数据库配置项
		$this->platform = $platform;
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		// 连接数据库
		$this->connect ();
		$this->db->tableName = 'sys_member';
	}
	public function getCountByWhere($where = NULL) {
		$return = $this->select ( 'count(*) as num', $where );
		return isset ( $return [0] ['num'] ) ? $return [0] ['num'] : 0;
	}
	
	/**
	 * 根据订阅状况差查询昨天的数量
	 */
	public function getCount($subscribe = 0) {
		$today = strtotime ( 'today' );
		$sql = "select count(*) as num from sys_member where subscribe = '$subscribe' and subscribe_time<$today and subscribe_time > " . ($today - 86400);
		$result = $this->query ( $sql );
		return isset ( $result [0] ['num'] ) ? $result [0] ['num'] : 0;
	}
	/**
	 *
	 * @param type $where        	
	 * @param type $issingle        	
	 * @return type
	 */
	public function getMemInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'sys_member';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	
	/**
	 * 获取列表
	 * 
	 * @param string $param        	
	 * @return unknown
	 */
	public function getList($param = null) {
		$p = $param ['p'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$provincecode = $param ['provincecode'];
		$citycode = $param ['citycode'];
		$sex = $param ['sex'];
		$interactionStime = strtotime ( $param ['interactionStime'] );
		$interactionDtime = strtotime ( $param ['interactionDtime'] . " 23:59:59" );
		$interactionNum = $param ['interactionNum'];
		$interactionMin = $param ['interactionMin'];
		$interactionMax = $param ['interactionMax'];
		$nickname = $param ['nickname'];
		$where = ' where 1 = 1 and m.subscribe = 1 ';
		
		if ($provincecode && $provincecode != 0) {
			$where .= ' and m.provincecode = "' . $provincecode . '"';
			if ($citycode && $citycode != 0) {
				$where .= ' and m.citycode = "' . $citycode . '"';
			}
		}
		if ($sex) {
			$where .= " and m.sex = {$sex} ";
		}
		if ($interactionStime) {
			$where .= " and m.lastintracttime >= {$interactionStime} ";
		}
		
		if ($interactionDtime) {
			$where .= " and m.lastintracttime <= {$interactionDtime} ";
		}
		
		if ($nickname) {
			$where .= " and m.nickname like '%" . $nickname . "%' ";
		}
		
		if ($interactionNum) {
			$where .= " and c.fromusername = m.openid ";
			if ($interactionNum == 'custom' && $interactionMin && $interactionMax) {
				$sql = 'select * from ' . gtn ( 'member' ) . ' as m,(select fromusername,COUNT(id) from sys_get_msg group by fromusername HAVING count(id)>="' . $interactionMin . '" and count(id)<="' . $interactionMax . '") as c ' . $where . $limit;
			} else {
				$sql = 'select * from ' . gtn ( 'member' ) . ' as m,(select fromusername,COUNT(id) from sys_get_msg group by fromusername HAVING count(id)>"' . $interactionNum . '") as c ' . $where . $limit;
			}
		} else {
			$sql = 'select * from ' . gtn ( 'member' ) . ' as m ' . $where . $limit;
		}
		
		$data = $this->db->query ( $sql );
		// print_r($this->getSql());
		// total
		if ($interactionNum) {
			if ($interactionNum == 'custom' && $interactionMin && $interactionMax) {
				$sql = 'select count(*) as num from ' . gtn ( 'member' ) . ' as m,(select fromusername,COUNT(id) from sys_get_msg group by fromusername HAVING count(id)>="' . $interactionMin . '" and count(id)<="' . $interactionMax . '") as c ' . $where;
			} else {
				$sql = 'select count(*) as num from ' . gtn ( 'member' ) . ' as m,(select fromusername,COUNT(id) from sys_get_msg group by fromusername HAVING count(id)>"' . $interactionNum . '") as c ' . $where;
			}
		} else {
			$sql = 'select count(*) as num from ' . gtn ( 'member' ) . ' as m ' . $where;
		}
		
		$tmp = $this->db->query ( $sql );
		$total = $tmp [0] ['num'];
		
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
	
	/**
	 * 获取会员信息
	 * 
	 * @param number $id        	
	 * @return Ambigous <multitype:, 2>
	 */
	public function getInfoById($id = 0) {
		$sql = 'select * from ' . gtn ( 'member' ) . ' where id = ' . $id;
		$result = $this->db->query ( $sql );
		return $result ? $result [0] : array ();
	}
	
	/**
	 * 获取会员信息
	 * 
	 * @param number $id        	
	 * @return Ambigous <multitype:, 2>
	 */
	public function getInfoByOpenid($openid = 0) {
		$sql = 'select * from ' . gtn ( 'member' ) . ' where openid = "' . $openid . '"';
		$result = $this->db->query ( $sql );
		return $result ? $result [0] : array ();
	}
	/**
	 * 获取用户基础信息
	 * 
	 * @param number $openid        	
	 * @return Ambigous <multitype:, 2>
	 */
	public function getBaseinfoByOpenid($openid = 0) {
		$sql = 'select openid,nickname,headimgurl,sex,subscribe,subscribe_time,lastlocation,language,country,province,city from ' . gtn ( 'member' ) . ' where openid = "' . $openid . '"';
		$result = $this->db->query ( $sql );
		$info = array ();
		if ($result) {
			$info = $result [0];
			$sql = "select * from xinqingnian where openid = '$openid'";
			$tmp = $this->db->query ( $sql );
			$info ['sid'] = 0;
			if ($tmp) {
				$info ['sid'] = 1;
			}
		}
		return $info;
	}
	
	/**
	 * 会员数量
	 * 
	 * @param unknown $param        	
	 * @return number
	 */
	public function member_num($param) {
		$where = ' where 1 = 1 and subscribe = "1"';
		if (isset ( $param ['type'] ) && $param ['type']) {
			$where .= ' and type = ' . $param ['type'];
		}
		$sql = 'select count(*) as num from ' . gtn ( 'member' ) . '' . $where;
		$result = $this->db->query ( $sql );
		return $result ? $result [0] ['num'] : 0;
	}
	
	/**
	 * 统计符合条件的会员的数量
	 * 
	 * @return 返回会员数量
	 */
	public function memberCount() {
		$memberCity = ( int ) $_POST ['memberCity'];
		$memberProvince = ( int ) $_POST ['memberProvince'];
		$memberType = ( int ) $_POST ['memberType'];
		$where = array ();
		if ($memberType) {
			$where ['type'] = $memberType;
		}
		if ($memberProvince) {
			$where ['province'] = $this->getProvince ( $memberProvince );
		}
		if ($memberCity) {
			$where ['city'] = $this->getCity ( $memberCity );
		}
		$this->tableName = "member";
		$count = $this->count ( $where );
		if (is_numeric ( $count )) {
			return $count;
		} else {
			return 0;
		}
	}
	
	/**
	 * 获取id对应的省份的名称
	 * 
	 * @param int $province_id
	 *        	省在location表中的id
	 * @return string 返回名称
	 */
	public function getProvince($province_id) {
		$this->tableName = "sys_location";
		$where ['id'] = $province_id;
		$location = $this->first ( $where );
		if (is_array ( $location )) {
			return $location ['name'];
		} else {
			return '北京';
		}
	}
	
	/**
	 * 获取id对应的城市的名称
	 * 
	 * @param int $city_id
	 *        	城市在location表中的id
	 * @return string 返回名称
	 */
	public function getCity($city_id) {
		$this->tableName = "sys_location";
		$where ['id'] = $city_id;
		$location = $this->first ( $where );
		if (is_array ( $location )) {
			return $location ['name'];
		} else {
			return '朝阳';
		}
	}
	
	/**
	 * 生成二维码
	 */
	public function makeQr($id) {
		$this->tableName = 'member';
		$info = array ();
		$info ['is_qr'] = 1;
		
		$this->update ( $info, 'id = ' . $id );
	}
	
	/**
	 * 生成二维码
	 */
	public function cancelQr($id) {
		$this->tableName = 'member';
		$info = array ();
		$info ['is_qr'] = 4;
		
		$this->update ( $info, 'id = ' . $id );
	}
	public function getMember($WHERE = NULL) {
		$this->tableName = 'member';
		
		$return = $this->select ( NULL, $WHERE );
		return $return;
	}
	
	/**
	 * 根据会员ID得到当前区域 坐标
	 * 
	 * @param type $uid        	
	 * @return type
	 */
	function getUserLoction($uid) {
		$sql = 'SELECT * FROM ' . gtn ( 'member' ) . ' WHERE openid="' . $uid . '"';
		$result = $this->db->query ( $sql );
		$resultArray = array ();
		if ($result) {
			if ($result [0] ['lastlocation']) {
				$array = json_decode ( $result [0] ['lastlocation'], true );
				$resultArray ['x'] = $array ['Longitude'];
				$resultArray ['y'] = $array ['Latitude'];
			}
			// 通过curl到得
			if (empty ( $resultArray ['x'] )) {
				$city = $result [0] ['province'] . $result [0] ['city'];
				$url = 'http://api.map.baidu.com/geocoder/v2/?address=' . urlencode ( $city ) . '&output=json&ak=81231E65D090e62ea493d00d1861bf61';
				$ch = curl_init ();
				curl_setopt ( $ch, CURLOPT_URL, $url );
				curl_setopt ( $ch, CURLOPT_HEADER, 0 );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				$rep = curl_exec ( $ch );
				$dataArray = json_decode ( $rep, true );
				$resultArray ['x'] = $dataArray ['result'] ['location'] ['lng'];
				$resultArray ['y'] = $dataArray ['result'] ['location'] ['lat'];
				curl_close ( $ch );
			}
			$resultArray ['province'] = $result [0] ['province'];
			$resultArray ['city'] = $result [0] ['city'];
		}
		return $resultArray;
	}
	
	/**
	 * 更新会员信息
	 *
	 * @param array $info        	
	 * @param array|varchar $where        	
	 */
	public function updateMember($info, $where) {
		$this->tableName = 'member';
		$res = $this->update ( $info, $where );
		return $res;
	}
	public function deleteHeadimg($where) {
		$this->tableName = 'headimg';
		$this->delete ( $where );
	}
	
	/**
	 * 根据类型获取会员列表
	 * 
	 * @param unknown $type        	
	 * @return Ambigous <multitype:, unknown>
	 */
	public function getMemberList($type, $stime = 0, $etime = 0) {
		$etime = $etime ? $etime : time ();
		$where = " where type = $type";
		$where .= " and subscribe_time >= $stime and subscribe_time <=$etime ";
		$sql = 'select * from ' . gtn ( 'member' ) . ' ' . $where;
		$result = $this->query ( $sql );
		if ($result) {
			foreach ( $result as $k => $val ) {
				// 领取礼券数量
				$sqldraw = "SELECT COUNT(*) AS num  FROM sys_gift_used_relation WHERE uid='" . $val ['openid'] . "' AND status = 2";
				// $sqldraw = "SELECT COUNT(*) AS num FROM sys_gift_used_relation_online WHERE uid='" . $val['openid'] . "' AND status = 2";
				$drawNum = $this->query ( $sqldraw );
				$result [$k] ['drawNum'] = $drawNum [0] ['num'];
				// 激活使用礼券数量
				$sqlused = "SELECT COUNT(*) AS num  FROM sys_gift_used_relation WHERE uid='" . $val ['openid'] . "' AND status = 1";
				// $sqlused = "SELECT COUNT(*) AS num FROM sys_gift_used_relation_online WHERE uid='" . $val['openid'] . "' AND status = 1";
				$usedNum = $this->query ( $sqlused );
				$result [$k] ['usedNum'] = $usedNum [0] ['num'];
				// 收藏礼券数量
				$sqlcoll = "SELECT COUNT(*) AS num  FROM sys_gift_used_relation WHERE uid='" . $val ['openid'] . "' AND collection = 2";
				// $sqlcoll = "SELECT COUNT(*) AS num FROM sys_gift_used_relation_online WHERE uid='" . $val['openid'] . "' AND collection = 2";
				$collNum = $this->query ( $sqlcoll );
				$result [$k] ['collNum'] = $collNum [0] ['num'];
				// 过期礼券数量
				$sqlgift = "SELECT COUNT(*) AS num  FROM sys_gift_used_relation as ur, sys_gift AS g WHERE ur.uid='" . $val ['openid'] . "' AND ur.gid=g.id AND ur.status = 2 AND g.status>4";
				// $sqlgift = "SELECT COUNT(*) AS num FROM sys_gift_used_relation_online as ur, sys_gift_online AS g WHERE ur.uid='" . $val['openid'] . "' AND ur.gid=g.id AND ur.status = 2 AND g.status>4";
				$giftNum = $this->query ( $sqlgift );
				$result [$k] ['giftNum'] = $giftNum [0] ['num'];
			}
		}
		return $result ? $result : array ();
	}
	function record($openid = 0) {
		$sql = "insert into a (`openid`) values('$openid')";
		$this->db->query ( $sql );
	}
	/**
	 * 获取最新交互的用户
	 * 分组id为0 是代表全部 100 为IDF
	 * 
	 * @param number $groupid        	
	 * @return Ambigous <multitype:, unknown>
	 */
	function getLatestIntractUser($groupid = 999) {
		$stime = time () - 3600 * 48;
		$where = " where 1=1 and wg.ctime > $stime";
		$groupby = " group by wg.fromusername";
		$sql = "select * from sys_get_msg as wg " . $where . $groupby;
		if ($groupid != 0) {
			$where .= " and wg.fromusername = m.openid";
			$sql = "select wg.* from sys_get_msg as wg,sys_member as m " . $where . $groupby;
		}
		$result = $this->db->query ( $sql );
		return $result ? $result : array ();
	}
	
	/**
	 * 获取当前公众号的粉丝数
	 */
	public function getTotal() {
		$WECHAT = new wechatAction ();
		$access_token = $WECHAT->getAccessTokenByOpenid ();
		$API = new WechatApi ( $access_token );
		$result = $API->getUsers ();
		$now = getdate ( time () - 86400 );
		if ($result ['total']) {
			$info = array ();
			$info ['total'] = $result ['total'];
			$info ['type'] = '2';
			$info ['year'] = $now ['year'];
			$info ['month'] = $now ['mon'];
			$info ['day'] = $now ['mday'];
			$info ['ctime'] = time ();
			$info ['uptime'] = time ();
			
			$this->tableName = 'statistical_log';
			$this->insert ( $info );
		}
	}
	
	/**
	 * 向member表中添加用户
	 */
	public function addMemberInfo($data) {
		$this->tableName = 'member';
		$insert_id = $this->insert ( $data );
		return $insert_id;
	}
}

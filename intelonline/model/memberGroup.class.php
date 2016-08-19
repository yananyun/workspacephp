<?php

/**
 * 微信开发者公众号用户的用户分组管理以及获取粉丝关注列表
 * @author 赵伟  2014.05.21
 */
class memberGroup extends Module {
	function __construct() {
		parent::__construct ( __CLASS__ );
	}
	/**
	 * 找出所有符合此分组的粉丝
	 * 
	 * @param unknown $group_id        	
	 * @return unknown
	 */
	public function conform($group_id, $where = '') {
		$sql_group = "select * from sys_member_group where id={$group_id} limit 1";
		$res_group = $this->query ( $sql_group );
		$group = $res_group [0];
		$fans_where = ' (1';
		$fans_where .= " and subscribe=1";
		$fans_where .= $where;
		// 性别,来源,类型,地域,等级,最小交互,最大交互,排除openid,增加openid
		! empty ( $group ['condition_sex'] ) && $fans_where .= " and sex={$group['condition_sex']}";
		! empty ( $group ['condition_source'] ) && $fans_where .= " and sid in({$group['condition_source']})";
		! empty ( $group ['condition_type'] ) && $fans_where .= " and type in({$group['condition_type']})";
		! empty ( $group ['condition_city'] ) && $fans_where .= " and city='{$group['condition_city']}'";
		! empty ( $group ['condition_province'] ) && $fans_where .= " and province='{$group['condition_province']}'";
		// !empty($group['condition_level']) && $fans_where .= " and level={$group['condition_level']}";
		! empty ( $group ['condition_cross_min'] ) && $fans_where .= " and interactive>={$group['condition_cross_min']}";
		! empty ( $group ['condition_cross_max'] ) && $fans_where .= " and interactive<={$group['condition_cross_max']}";
		if (! empty ( $group ['condition_exclude'] ) && $group ['condition_exclude']) {
			$exclude = explode ( ',', $group ['condition_exclude'] );
			foreach ( $exclude as &$val ) {
				$val = "'{$val}'";
			}
			$exclude = implode ( ',', $exclude );
			$fans_where .= " and openid not in({$exclude})";
		}
		$fans_where .= ")";
		if (! empty ( $group ['condition_added'] ) && $group ['condition_added']) {
			$add = explode ( ',', $group ['condition_added'] );
			foreach ( $add as &$val ) {
				$val = "'{$val}'";
			}
			$add = implode ( ',', $add );
			$fans_where .= " or openid in({$add})";
		}
		
		// $fans_sql = "select 1 as shield,nickname,sex,concat(province,'-',city) as location,interactive,type,openid from sys_member where {$fans_where}";
		$fans_sql = "select openid from sys_member where {$fans_where}";
		$res_fans = $this->query ( $fans_sql );
		
		$res_fansArr = array ();
		foreach ( $res_fans as $k => $v ) {
			$res_fansArr [] = $v ['openid'];
		}
		
		// 带有标签的粉丝数量
		$memberTags = $group ['condition_tags'];
		if (! empty ( $memberTags )) {
			$memberTagsArr = explode ( ",", $memberTags );
			$tagFansArr = array ();
			foreach ( $memberTagsArr as $v4 ) {
				$sql = "select openid from `sys_tags_member`  WHERE tag_id=" . $v4;
				$tmp = $this->db->query ( $sql );
				foreach ( $tmp as $k2 => $v2 ) {
					$tagFansArr [] = $v2 ['openid'];
				}
			}
			$tagFansArrNew = array_flip ( array_flip ( $tagFansArr ) );
			// $tagfansNum = count($tagFansArr);
			// $tag_fans = $tagfansNum; //带有标签的粉丝数量
		} else {
			$tagFansArrNew = array ();
			// $tag_fans = 0;
		}
		// 按条件查询的粉丝 和 带标签的粉丝 总数量
		if (count ( $res_fansArr ) && count ( $tagFansArrNew )) {
			$fansOpenidArr = array_intersect ( $res_fansArr, $tagFansArrNew );
			$arr_fans = array ();
			foreach ( $fansOpenidArr as $key => $openid ) {
				$fans_sql = "select 1 as shield,nickname,sex,concat(province,'-',city) as location,interactive,type,openid from sys_member where openid='" . $openid . "'";
				$data = $this->query ( $fans_sql );
				$arr_fans [] = $data [0];
			}
		} else {
			$arr_fans = array ();
		}
		return $arr_fans;
	}
	public function find_territory($id) {
		$sql = "select name from sys_location where id={$id} limit 1";
		$res = $this->query ( $sql );
		return $res [0] ['name'];
	}
	public function sel_city($city) {
		$sql = "select id,name from sys_location where name='{$city}' limit 1";
		$res = $this->query ( $sql );
		return $res [0];
	}
	/**
	 * 获取当前公众账号的所有用户
	 * 
	 * @param string $apen_id
	 *        	公众号用户openid
	 */
	public function member_list($aopen_id, $group_id = 0) {
		$where = " where b.aopenid = '{$aopen_id}'";
		$group_param = ''; // 用于显示在url做参数用
		$group_str = '';
		if ($group_id) {
			$where .= ' and id = ' . $group_id;
			$group_str = "gid/" . $group_id . '/'; // 用于显示在url做参数用
		}
		//
		$sql2 = "select distinct(a.openid) from " . gtn ( 'mem_group_relation' ) . " as a left join " . gtn ( 'membergroup' ) . " as b on a.groupid=b.id " . $where;
		$sql = "SELECT c.id,c.openid,c.nickname,c.sex,c.city,c.province,c.subscribe,c.subscribe_time,c.lastintracttime " . "from " . gtn ( 'member' ) . "  as c left join ({$sql2}) as d on c.openid = d.openid ";
		$res = $this->query ( $sql );
		$count = count ( $res );
		$p = intval ( $_GET ['p'] ) > 0 ? intval ( $_GET ['p'] ) : 1; // 当前页数
		$limit = 10; // 每页显示条数
		$offset = ($p - 1) * $limit;
		$subPages = 10; // 每次显示的分页数量
		$sql .= " order by subscribe_time desc limit {$offset},{$limit} ";
		$member_list = $this->query ( $sql );
		// 定义openids存贮所有用户openids 共下面调取用
		$openids = array ();
		foreach ( $member_list as $key => $value ) {
			$member_list [$key] ['ctime'] = date ( 'Y-m-d H:i', $value ['ctime'] ); // 入库时间
			$member_list [$key] ['sex'] == 1 ? "男" : $value ['sex'] == 2 ? '女' : '未知'; // 入库时间
			$member_list [$key] ['subscribe_time'] = date ( 'Y-m-d H:i', $value ['subscribe_time'] ); // 订阅时间
			$member_list [$key] ['is_subscribe'] = $value ['subscribe'] == 1 ? '已订阅' : '取消订阅'; // 是否订阅
			$openids [] = $value ['openid'];
		}
		
		// 根据openids 获取所有对应 array(oepnid => groupid,...) 格式数组信息
		$member_group_ids = $this->get_member_groups ( $openids );
		$member_group = array ();
		foreach ( $member_group_ids as $key => $value ) {
			$member_group [$value ['openid']] [$value ['groupid']] = $value ['groupid'];
			$group_id_list [] = $value ['groupid'];
		}
		// 根据所有分组id 信息从获取对应分组名称
		$group_id_list = array_unique ( $group_id_list );
		$group_names_list = $this->get_member_group_names ( $group_id_list );
		/*
		 * 根据$member_group 和 ￥group_names_list 两个数组对应关系，整合到一起形成一个已oepnid为键值的格式内容）
		 * array( 'openid'=> array("分组名称1" ,"分组名称2" ), 'openid'=> array("分组名称3" ,"分组名称1" ), ....);
		 */
		
		foreach ( $member_group as $key => &$value ) {
			foreach ( $value as $k => $v ) {
				$value [$k] = $group_names_list [$v];
			}
		}
		foreach ( $member_list as $key => &$val ) {
			$val ['group_names'] = $member_group [$val ['openid']];
		}
		
		// 生成分页
		$subPages = new SubPages ( $limit, $count, $p, $subPages, APP_PATH . "index.php/" . __CLASS__ . "/" . 'index/' . $group_str . 'p/', 2 );
		$array = array (
				'pages' => $subPages->subPageCss1 (),
				'data' => $member_list,
				'nums' => $count 
		);
		return $member_list ? apiData ( $array, 1000 ) : apiData ( '', 3004 );
	}
	
	/**
	 * 获取当前用户所有的分组列表
	 * 
	 * @param string $apen_id
	 *        	公众号用户openid
	 */
	public function group_list($aopen_id) {
		$sql = "SELECT a.* ,count(b.openid) AS member_count FROM zwca_membergroup AS a left join zwca_mem_group_relation AS b " . " on a.id = b.groupid where a.aopenid='{$aopen_id}'  GROUP BY a.id ";
		
		$group_list = $this->query ( $sql );
		foreach ( $group_list as &$value ) {
			$value ['ctime'] = date ( 'Y-m-d H:i', $value ['ctime'] );
		}
		// echo $group->getSql();exit;
		return $group_list;
	}
	
	/**
	 * 获取一条符合条件分组详情
	 * 
	 * @param int $where
	 *        	指定要获取信息的where条件，可以为分组id，
	 */
	public function get_group_info($where) {
		$this->tableName = 'membergroup';
		$res = $this->select ( '*', $where );
		return $res;
	}
	
	/**
	 * 新增用户分组入库操作
	 * 
	 * @param array $param
	 *        	入库字段数据信息
	 * @return boolean
	 */
	public function group_insert($param) {
		$this->db->tableName = 'zwca_membergroup';
		$res = $this->insert ( $param );
		return intval ( $res );
	}
	
	/**
	 * 更新用户编辑后的用户分组入库操作
	 * 
	 * @param int $group_id
	 *        	要编辑的用户分组id
	 * @param array $param
	 *        	入库字段数据信息
	 * @return boolean
	 */
	public function group_update($group_id, $param) {
		$this->tableName = 'membergroup';
		$res = $this->update ( $param, $group_id );
		if (intval ( $res ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function group_delete($group_id) {
		$group_rela = M ( "mem_group_relation" );
		$group_mem_count = $group_rela->count ( array (
				'groupid' => $group_id 
		) );
		if ($group_mem_count > 0) {
			showmessage ( "该分组下由用户存在不可以删除" );
		} else {
			$group = M ( 'membergroup' );
			$res = $group->delete ( $group_id );
			// echo $group->getSql();exit;
			if (intval ( $res ) > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * 处理用户关注公众账号后操作
	 * 
	 * @param array $member_info
	 *        	用户详情信息
	 * @param string $aopenid
	 *        	开发者公众号openid
	 */
	public function subscribe($member_info, $aopenid) {
		
		/**
		 * 判断该用户之前是否关注过（实际上就是判断是不是在数据库中存在），如果存在则更新相应信息，不存在在进行插入数据库操作
		 * 注：同一用户在不同公众号的openid不一样，只对统一公众号有唯一性，所以不需要考虑多个公众号时该用户信息会重复
		 */
		$this->tableName = 'member';
		$is_exist = $this->first ( array (
				'openid' => $member_info ['openid'] 
		) );
		// 用户已存在
		if ($is_exist) {
			$update_arr = array (
					'subscribe' => $member_info ['subscribe'],
					'nickname' => filterImage ( $member_info ['nickname'] ),
					'sex' => $member_info ['sex'],
					'city' => $member_info ['city'],
					'province' => $member_info ['province'],
					'country' => $member_info ['country'],
					'language' => $member_info ['language'],
					'headimgurl' => $member_info ['headimgurl'],
					'subscribe_time' => $member_info ['subscribe_time'],
					'subscribe' => $member_info ['subscribe'] 
			);
			if ($update_arr ['country'] == '中国') {
				$update_arr ['provincecode'] = $this->locationToCode ( $update_arr ['province'] );
				$update_arr ['citycode'] = $this->locationToCode ( $update_arr ['city'], 2, $update_arr ['provincecode'] );
			} else {
				$update_arr ['provincecode'] = 0;
				$update_arr ['citycode'] = 0;
			}
			$this->tableName = 'member';
			$this->update ( $update_arr, array (
					'openid' => $member_info ['openid'] 
			) );
		} else {
			// 如果用户不存在，及首次关注，则判断是否改公众号是否有默认分组，如果有则获取默认分组id，没有则创建
			$group = $this->get_group_info ( array (
					'is_moren' => 1,
					'aopenid' => $aopenid 
			) );
			$default_id = $group ['id'];
			if (! $group) {
				$param ['is_moren'] = 1;
				$param ['name'] = '默认分组'; // 分组名称
				$param ['remark'] = ''; // 分组备注描述
				$param ['uid'] = 0;
				$param ['aopenid'] = $aopenid;
				$param ['status'] = 1; // 1：启用，2：禁用
				$param ['ctime'] = time ();
				$param ['uptime'] = time ();
				$default_id = $this->group_insert ( $param );
			}
			
			// 将关注者列表openid 同时添加到公众用户对应的分组关系表中zwca_mem_group_relation
			$this->tableName = 'mem_group_relation';
			$this->insert ( array (
					'groupid' => $default_id,
					'openid' => $member_info ['openid'] 
			) );
			
			$insert_arr = array (
					'openid' => $member_info ['openid'],
					'nickname' => $member_info ['nickname'],
					'sex' => $member_info ['sex'],
					'city' => $member_info ['city'],
					'province' => $member_info ['province'],
					'country' => $member_info ['country'],
					'language' => $member_info ['language'],
					'subscribe' => $member_info ['subscribe'],
					'subscribe_time' => $member_info ['subscribe_time'],
					'headimgurl' => $member_info ['headimgurl'],
					'ctime' => time (),
					'lastintracttime' => time () 
			);
			if ($insert_arr ['country'] == '中国') {
				$insert_arr ['provincecode'] = $this->locationToCode ( $insert_arr ['province'] );
				$insert_arr ['citycode'] = $this->locationToCode ( $insert_arr ['city'], 2, $insert_arr ['provincecode'] );
			} else {
				$insert_arr ['provincecode'] = 0;
				$insert_arr ['citycode'] = 0;
			}
			$this->tableName = 'member';
			$this->insert ( $insert_arr );
		}
	}
	
	/**
	 * 用户取消关注处理
	 * 
	 * @param string $openid
	 *        	微信用户openid
	 */
	public function unsubscribe($openid) {
		$this->tableName = 'member';
		$this->update ( array (
				'subscribe' => 0 
		), array (
				'openid' => $openid 
		) );
	}
	
	/**
	 * 将获取的关注列表数据入库操作
	 * 
	 * @param array $guanzhu_users
	 *        	获取的改用的所有关注者用户数据
	 * @param array $aopenid
	 *        	当前公众账号的openid
	 */
	public function insert_guanzhu_user($guanzhu_users, $aopenid) {
		$sql = "insert into zwca_member (subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,ctime,lastintracttime) values ";
		$values_str = '';
		foreach ( $guanzhu_users as $key => $value ) {
			$values_str .= "('{$value['subscribe']}','{$value['openid']}','{$value['nickname']}',{$value['sex']},'{$value['city']}','{$value['province']}','{$value['country']}','{$value['language']}','{$value['headimgurl']}','{$value['subscribe_time']}', " . time () . ", " . time () . "),";
		}
		$values_str = trim ( $values_str, ',' );
		$sql .= $values_str . ';';
		$res = $this->query ( $sql );
		if (intval ( $res )) {
			$group = $this->get_group_info ( array (
					'is_moren' => 1,
					'aopenid' => $aopenid 
			) );
			// 将关注者列表openid 同时添加到公众用户对应的分组关系表中zwca_mem_group_relation
			$sql = "insert into zwca_mem_group_relation (groupid,openid) values ";
			$values_str = '';
			foreach ( $guanzhu_users as $key => $value ) {
				$values_str .= "('{$group['id']}','{$value['openid']}'),";
			}
			$values_str = trim ( $values_str, ',' );
			$sql .= $values_str . ';';
			$res2 = $this->query ( $sql );
		}
		if ($res && intval ( $res2 )) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 获取指定用户的所属分组
	 * 
	 * @param array $openid
	 *        	微信用户粉丝 openid
	 */
	public function get_member_groups($openid_array) {
		$sql = "select groupid,openid from " . gtn ( 'mem_group_relation' ) . " where openid " . db_create_in ( $openid_array );
		return $this->query ( $sql );
	}
	
	/**
	 * 获取指定用户的所属分组
	 * 
	 * @param array $openid
	 *        	微信用户粉丝 openid
	 */
	public function get_member_group_names($groupid_array) {
		$sql = "select id,name from " . gtn ( 'membergroup' ) . " where id " . db_create_in ( $groupid_array );
		$res = $this->query ( $sql );
		foreach ( $res as $key => $val ) {
			$new_arr [$val ['id']] = $val ['name'];
		}
		return $new_arr;
	}
	
	/**
	 * 删除指定用户的指定所属分组
	 * 
	 * @param array $openid
	 *        	微信用户粉丝 openid
	 */
	public function move_member_groups($openid_array, $group_ids) {
		$sql = "delete from " . gtn ( 'mem_group_relation' ) . " where openid " . db_create_in ( $openid_array );
		$this->query ( $sql );
		$newarray = array ();
		foreach ( $openid_array as $key => $val ) {
			foreach ( $group_ids as $key => $value ) {
				$newarray [] = array (
						'openid' => $val,
						'groupid' => $value 
				);
			}
		}
		$sql = "insert into " . gtn ( 'mem_group_relation' ) . '(openid,groupid)values ';
		$values_str = '';
		foreach ( $newarray as $key => $value ) {
			$values_str .= "('{$value['openid']}','{$value['groupid']}'),";
		}
		$values_str = trim ( $values_str, ',' );
		$sql .= $values_str . ';';
		$res = $this->query ( $sql );
		return intval ( $res );
		exit ();
	}
	
	/**
	 * 更改用户location信息
	 * 
	 * @param array $message
	 *        	用户location响应数据
	 * @return int 修改的id
	 */
	public function updateLocation($message) {
		$location = array (
				'Latitude' => $message ['Latitude'],
				'Longitude' => $message ['Longitude'],
				'Precision' => $message ['Precision'] 
		);
		$local_arr = array (
				'lastlocation' => json_encode_zh ( $location ) 
		);
		$this->tableName = 'member';
		return $this->update ( $local_arr, array (
				'openid' => $message ['FromUserName'] 
		) );
	}
	
	/**
	 * 通过地区名字获取地区编码
	 *
	 * @param varchar $name
	 *        	地区名字
	 * @param int $type
	 *        	类型：1省份 2城市
	 */
	public function locationToCode($name, $type = 1, $fid = 0) {
		$L = new location ();
		
		if ($type == 1) {
			$provinceList = $L->provinceList ();
			$code = $provinceList [$name];
		}
		
		if ($type == 2) {
			$this->tableName = 'location';
			$info = $this->first ( 'fid = ' . $fid . ' AND name ="' . $name . '"' );
			$code = $info ['id'];
		}
		return $code;
	}
}

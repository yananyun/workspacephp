<?php

/**
 * 微信开发者公众号用户的用户分组管理以及获取粉丝关注列表
 * @author 赵伟  2014.05.21
 */
class memberGroupAction extends Action {
	public $aopen_id = 'gh_da0c0a755166';
	public $member;
	public $uid; // 用户id（操作员id，user表中id
	             
	// 构造方法
	public function __construct() {
		parent::__construct ();
		$this->member = new memberGroup ();
		$this->uid = $this->userid;
	}
	
	/**
	 * 获取所有微信用户
	 */
	public function index() {
		p ( $_SESSION );
		exit ();
		$group_id = intval ( $_GET ['gid'] ) > 0 ? intval ( $_GET ['gid'] ) : 0;
		$result = $this->member->member_list ( $this->aopen_id, $group_id );
		$member_list = $result ['error_code'] == 1000 ? $result ['result'] ['data'] : array ();
		$this->smarty->assign ( array (
				'member_list' => $member_list,
				'pages' => $result ['result'] ['pages'] 
		) );
		// 获取当前公众账号的所有分组数据
		$group_list = $this->member->group_list ( $this->aopen_id );
		$this->smarty->assign ( 'group_list', $group_list );
		$this->smarty->display ( 'member/member_list.html' );
	}
	
	/**
	 * 获取关注者列表,当用户表没有值时执行该操作，如果已经入库，不要执行该操作
	 */
	public function get_subscribe_list() {
		$wechat = new wechatAction ();
		// 根据公众号openid 获取access_token
		$access_token = $wechat->getAccessTokenByOpenid ( $this->aopen_id );
		$wechat_api = new WechatApi ( $access_token );
		$user_list = $wechat_api->getUsers ();
		$next_openid = $user_list ['next_openid'];
		$open_ids = $user_list ['data'] ['openid'];
		
		while ( $next_openid ) {
			$user_list = $wechat_api->getUsers ( $next_openid );
			if ($user_list ['data'] ['openid'] && is_array ( $user_list ['data'] ['openid'] )) {
				$open_ids = array_merge ( $open_ids, $user_list ['data'] ['openid'] );
			}
			
			$next_openid = $user_list ['next_openid'];
		}
		$member_list = array ();
		foreach ( $open_ids as $key => $value ) {
			$member_list [] = $wechat_api->getUserInfoByOpenid ( $value );
		}
		// 将所有关注用户入库到会员表中
		$res = $this->member->insert_guanzhu_user ( $member_list, $this->aopen_id );
		if ($res) {
			showmessage ( '获取完成', '/index.php/memberGroup/index/' );
		}
	}
	
	/**
	 * 获取当前公众号账号信息的所有分组数据
	 */
	public function group_list() {
		echo $_SESSION ['openid'];
		
		$group_list = $this->member->group_list ( $this->aopen_id );
		$this->smarty->assign ( 'group_list', $group_list );
		$this->smarty->display ( 'member/group_list.html' );
	}
	
	/**
	 * 添加用户分组界面显示管理操作
	 */
	public function group_add() {
		$this->display ( 'member/group_add.html' );
	}
	
	/**
	 * 编辑用户分组界面显示管理操作
	 */
	public function group_edit() {
		$group = $this->member->get_group_info ( intval ( $_GET ['id'] ) );
		$this->smarty->assign ( 'group', $group );
		$this->display ( 'member/group_edit.html' );
	}
	
	/**
	 * 新增一个用户分组的入库管理操作
	 */
	public function group_insert() {
		// 检查是否已经有默认分组
		$is_default = intval ( $_POST ['is_default'] );
		$group = $this->member->get_group_info ( array (
				'is_moren' => 1,
				'aopenid' => $this->aopen_id 
		) );
		if ($is_default == 1 && $group) {
			showmessage ( "已经有默认分组，只能有一个默认分组" );
		} else {
			$param ['is_moren'] = $is_default;
		}
		
		$param ['name'] = trim ( $_POST ['name'] ); // 分组名称
		$param ['remark'] = trim ( $_POST ['remark'] ); // 分组备注描述
		$param ['uid'] = intval ( $this->uid );
		$param ['aopenid'] = $this->aopen_id;
		$param ['status'] = intval ( $_POST ['status'] ) > 0 ? intval ( $_POST ['status'] ) : 1; // 1：启用，2：禁用
		$param ['ctime'] = SYS_TIME;
		$param ['uptime'] = time ();
		
		$res = $this->member->group_insert ( $param );
		if ($res) {
			showmessage ( '添加成功', '/index.php/memberGroup/group_list' );
		}
	}
	
	/**
	 * 编辑更新一个用户分组的入库管理操作
	 */
	public function group_update() {
		$group_id = intval ( $_POST ['group_id'] );
		// 检查是否已经有默认分组
		$is_default = intval ( $_POST ['is_default'] );
		$group = $this->member->get_group_info ( "is_moren = 1 and and id <>{$group_id} aopenid ='" . $this->aopen_id . "'" );
		if ($is_default == 1 && $group) {
			showmessage ( "已经有默认分组，不可以更改改组为默认分组" );
		} else {
			$param ['is_moren'] = $is_default;
		}
		$param ['name'] = trim ( $_POST ['name'] ); // 分组名称
		$param ['remark'] = trim ( $_POST ['remark'] ); // 分组备注描述
		$param ['uid'] = intval ( $this->uid );
		$param ['status'] = intval ( $_POST ['status'] ) > 0 ? intval ( $_POST ['status'] ) : 1; // 1：启用，2：禁用
		$param ['uptime'] = time ();
		$res = $this->member->group_update ( $group_id, $param );
		if ($res) {
			showmessage ( '编辑成功', '/index.php/memberGroup/group_list/' );
		}
	}
	
	/**
	 * 删除一个用户分组的入库管理操作
	 */
	public function group_delete() {
		$group_id = intval ( $_GET ['id'] );
		$this->member->group_delete ( $group_id );
		showmessage ( "删除成功", '/index.php/memberGroup/group_list' );
	}
	public function group_move() {
		// 判断是否选中移动用户
		if (! ($_POST ['checkboxes']) || ! is_array ( $_POST ['checkboxes'] )) {
			showmessage ( '请选择要移动的用户' );
		}
		// 判断是否选中要移动到的分组内容
		if (! ($_POST ['group_ids']) || ! is_array ( $_POST ['group_ids'] )) {
			showmessage ( '请选择分组！' );
		}
		$open_ids = $_POST ['checkboxes'];
		$group_ids = $_POST ['group_ids'];
		$res = $this->member->move_member_groups ( $open_ids, $group_ids );
		if ($res) {
			showmessage ( '操作成功', '/index.php/memberGroup/index/' );
		}
	}
	public function sel_city() {
		$city = $_POST ['city'];
		$result = $this->member->sel_city ( $city );
		echo json_encode_zh ( $result );
	}
}

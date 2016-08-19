<?php

/**
 * 主动推送
 * wangying
 * 2014.06.23
 */
class activepushAction extends Action {
	private $ap;
	function __construct() {
		parent::__construct ();
		$this->ap = new activepush ();
		$this->assign ( 'userinfo', $_SESSION ['userinfo'] );
	}
	public function weixin_msg_publish($members = array()) {
		$groupmsg = $this->groupmsg ();
		$this->assign ( array (
				'group' => $groupmsg 
		) );
		$thisdate = date ( 'Y-m-d' );
		$tags = new tag ();
		$tagsArr = $tags->select ( "*", " status='1' " );
		
		$this->assign ( array (
				'thisdate' => $thisdate,
				'tagsArr' => $tagsArr 
		) );
		if (! empty ( $members )) {
			$this->assign ( array (
					'members' => $members 
			) );
		} else {
			$this->display ( 'admin/message/weixin_msg_publish.html' );
		}
	}
	public function edit_active() {
		$id = ( int ) $_GET ['id'];
		$data = $this->ap->edit_active ( $id );
		! empty ( $data ['members'] ) && $this->assign ( array (
				'members' => $data ['members'] 
		) );
		$this->assign ( array (
				'msg' => $data ['msg'] 
		) );
		$this->display ( 'admin/message/edit_weixin_msg_publish.html' );
	}
	public function edit_active_test() {
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', '300M' );
		$id = ( int ) $_GET ['id'];
		$M = new activepush ();
		$p = isset ( $_GET ['p'] ) ? intval ( $_GET ['p'] ) : 1;
		$data = $M->edit_active_new ( $id );
		$cacheFile = new CacheFile ();
		$active_openids = $data ['active_openids'];
		$tmp_arr = explode ( ',', $active_openids );
		$cacheFile->init ( array (
				'dir' => 'cache/active_' . $id,
				'depth' => 1 
		) );
		$openid_groups = $cacheFile->get ( "openid_groups" );
		if (! $openid_groups) {
			$openid_groups = array_chunk ( $tmp_arr, 20 );
			$cacheFile->set ( "openid_groups", $openid_groups );
		}
		$openids = $openid_groups [$p - 1];
		$member_total = count ( $tmp_arr );
		$members = array ();
		if ($openids) {
			foreach ( $openids as $openid ) {
				$sql = "select * from sys_member where openid = '$openid'";
				$tmp = $M->query ( $sql );
				$members [] = $tmp [0];
			}
		}
		
		$pages = $this->show_pages ( $member_total, $p, 20, 10, 'intelListWrap' );
		$this->assign ( array (
				'msg' => $data,
				'members' => $members,
				'member_total' => $member_total,
				'pages' => $pages 
		) );
		$this->display ( 'admin/message/edit_weixin_msg_publish_test.html' );
	}
	public function show_pages($num, $p, $page_size = 20, $sub_pages = 10, $block = '0') {
		$Url = $_SERVER ['REQUEST_URI'];
		
		$suburl = strpos ( $Url, "?_=13" ) ? strpos ( $Url, "?_=13" ) : strpos ( $Url, "&_=13" );
		if ($suburl) {
			$Url = substr ( $Url, 0, $suburl );
		}
		$UrlCount = strpos ( $Url, '?' );
		$Url = preg_replace ( '/(\/|&)*p(=|\/)[\d]\d*(\&|)/', '', $Url );
		// $Link = substr($Url, -1, 1);
		$Link = strpos ( $Url, '&' );
		
		if ($UrlCount) {
			// $Url = $Link ? $Url . '&p=' : $Url . 'p=';
			$Url = $Link ? $Url . '&p=' : $Url . '&p=';
		} else {
			$Url = $Url . '?p=';
		}
		
		// $subPages = new SubPages($page_size, $num, $p > 100 ? 100 : $p, $sub_pages, $Url, 2);
		$subPages = new SubPages ( $page_size, $num, $p, $sub_pages, $Url, 2 );
		
		$pages = $subPages->subPageCss1 ();
		return $pages;
	}
	public function weixin_msg_list() {
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		// 总页数
		$total = 0;
		$userlist = array ();
		$list = $this->ap->listall ( $param );
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'intelListWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'baseNum' => $baseNum 
		) );
		$this->display ( 'admin/message/weixin_msg_list.html' );
	}
	public function msg_list() {
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		// 总页数
		$total = 0;
		$userlist = array ();
		$list = $this->ap->listall ( $param );
		$ma = new material ();
		foreach ( $list ['data'] as &$vo ) {
			$ma->tableName = 'material_article';
			$result = $ma->first ( "mid=" . $vo ['active_mid'] . " and isdel='0'" );
			$vo ['title'] = $result ['title'];
		}
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'intelListWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'baseNum' => $baseNum 
		) );
		$this->display ( 'admin/message/msg_list.html' );
	}
	/**
	 * 删除消息
	 */
	public function del_active() {
		$id = ( int ) $_POST ['id'];
		$res = $this->ap->del_active ( $id );
		if ($res) {
			echo true;
		} else {
			echo false;
		}
	}
	public function web_msg_publish() {
		$this->display ( 'admin/message/web_msg_publish.html' );
	}
	/**
	 * 查找用户分组
	 */
	public function groupmsg() {
		$result = $this->ap->groupmsg ();
		return ($result && ! empty ( $result )) ? $result : array ();
	}
	/**
	 * 找出所有符合此分组的粉丝
	 */
	public function conformfans() {
		$nickname = $_GET ['nickname'] ? urldecode ( $_GET ['nickname'] ) : '';
		
		$gid = $_GET ['gid'] ? ( int ) $_GET ['gid'] : 0;
		$tid = $_GET ['tid'] ? $_GET ['tid'] : "";
		if ($tid == "0,") {
			$tid = "";
		}
		$fan = $_GET ['fan'] ? $_GET ['fan'] : "";
		
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		// 总页数
		$total = 0;
		if (empty ( $nickname )) {
			if ($gid != 0 || ! empty ( $tid )) {
				$res = $this->ap->conformfans ( $gid, $param, $tid, $fan );
				
				foreach ( $res as &$val ) {
					$val ['nickname'] = ! empty ( $val ['nickname'] ) && $val ['nickname'] != NULL ? $val ['nickname'] : '----';
					$val ['location'] = ($val ['province'] != '' || $val ['city'] != '') ? $val ['province'] . ' ' . $val ['city'] : '----';
				}
				$total = $res ['total'];
				unset ( $res ['total'] );
				$pages = $this->pages ( $total, $p, $pagesize, 10, 'intelListWrap' );
				$this->assign ( array (
						'members' => $res,
						'pages' => $pages,
						'baseNum' => $baseNum 
				) );
			}
		} else {
			$res = $this->ap->according_nickname ( $nickname, $param );
			$total = $res ['total'];
			unset ( $res ['total'] );
			$pages = $this->pages ( $total, $p, $pagesize, 10, 'intelListWrap' );
			$this->assign ( array (
					'members' => $res,
					'pages' => $pages,
					'baseNum' => $baseNum 
			) );
		}
		$this->display ( 'admin/message/web_msg_memberlist.html' );
	}
	/**
	 * 添加群发消息
	 */
	public function add_activepush() {
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', '300M' );
		$search = addslashes ( $_POST ['search'] );
		
		$gid = $_POST ['response_groupid'] ? ( int ) $_POST ['response_groupid'] : 0; // 用户分组条件
		$tid = $_POST ['tid'] ? $_POST ['tid'] : ""; // 用户标签条件
		if (! empty ( $tid ) && $tid [0] != 0) {
			$tagstr = implode ( ",", $_POST ['tid'] );
			$tid = $tagstr . ",";
		} else {
			$tid = '';
		}
		
		$fan = $_POST ['fan'] ? $_POST ['fan'] : ""; // 设置反向选择条件
		
		$exclude_openids = $_POST ['exclude_openids'];
		if (empty ( $search )) {
			$members = $this->ap->conformfans ( $gid, "", $tid, $fan );
		} else {
			$members = $this->ap->according_nicknameAll ( $search );
		}
		$info ['active_openids'] = '';
		foreach ( $members as $key => &$member ) {
			if (strpos ( $exclude_openids, $member ['openid'] ) === false) {
				$info ['active_openids'] = ! empty ( $info ['active_openids'] ) ? ($info ['active_openids'] . ',' . $member ['openid']) : $member ['openid'];
			}
		}
		if (strpos ( $info ['active_openids'], ',' )) {
			$info ['active_openids'] = substr ( $info ['active_openids'], 0, strlen ( $info ['active_openids'] ) - 1 );
		}
		$info ['active_mode'] = ! empty ( $_POST ['active_mid'] ) && empty ( $_POST ['active_content'] ) ? 2 : 1;
		
		if (! empty ( $_POST ['active_content'] )) {
			// $active_content = substr_replace($_POST['active_content'],'',-4);
			
			$active_content = str_replace ( '<br>', "\r\n", $_POST ['active_content'] );
		}
		// $info['active_content'] = !empty($_POST['active_content']) ? strip_tags(addslashes($_POST['active_content'])) : '';
		$info ['active_content'] = ! empty ( $active_content ) ? strip_tags ( addslashes ( $active_content ) ) : '';
		$info ['active_mid'] = ! empty ( $_POST ['active_mid'] ) ? ( int ) $_POST ['active_mid'] : 0;
		$info ['active_type'] = ( int ) $_POST ['active_type'];
		$info ['active_duration_end'] = ! empty ( $_POST ['active_duration_end'] ) ? $_POST ['active_duration_end'] : '0000-00-00';
		$info ['active_crontab_time'] = ! empty ( $_POST ['active_crontab_time'] ) ? $_POST ['active_crontab_time'] : '0000-00-00 00:00:00';
		if ($info ['active_duration_end'] != '0000-00-00' && $info ['active_crontab_time'] == '0000-00-00 00:00:00') {
			$info ['active_duration_start'] = date ( 'Y-m-d' );
		} else {
			$info ['active_duration_start'] = '0000-00-00';
		}
		$info ['active_interval'] = ! empty ( $_POST ['active_interval'] ) ? $_POST ['active_interval'] : 0;
		$info ['active_state'] = ! empty ( $info ['active_crontab_time'] ) ? 1 : 2;
		
		$res = $this->ap->add_activepush ( $info );
		/**
		 * if没有定时发布，if高级群发，if48推送
		 */
		// $this->display('admin/message/weixin_msg_list.html');
		header ( 'Location:' . APP_PATH . 'index.php/activepush/weixin_msg_list' );
	}
	public function activepushCrontab() {
		$this->ap->activepushCrontab ();
	}
	/*
	 * public function interaction48(){
	 * $this->ap->interaction48();
	 * }
	 */
	public function advancedApi() {
		$this->ap->advancedApi ( 84 );
	}
	public function gaoji() {
		$this->ap->advancedApi_test ( 295 );
	}
	
	/**
	 * 获取实际送达人数
	 */
	public function getMemGotCount() {
		if (! $_GET ['id']) {
			exit ( '请在URL后输入ID，如http://intelweixin.buzzopt.com/index.php/activepush/getMemGotCount/id/629' );
		}
		$id = $_GET ['id'];
		$info = $this->ap->getActivePushInfo ( array (
				'id' => $id 
		), TRUE );
		$active_openids_arr = explode ( ',', $info ['active_openids'] );
		$active_finish_openids_arr = explode ( ',', $info ['active_finish_openids'] );
		
		p ( '目标发送人数为：' . count ( $active_openids_arr ) );
		p ( '实际送达人数为：' . count ( $active_finish_openids_arr ) );
	}
	public function demoSend() {
		if (! $_GET ['openid']) {
			exit ();
		}
		$openid = $_GET ['openid'];
		$wechat = new wechat ();
		$content = array (
				'content' => 'Hello~!' 
		);
		$return = $wechat->putMsg ( $openid, 'text', $content );
		p ( $return );
	}
}
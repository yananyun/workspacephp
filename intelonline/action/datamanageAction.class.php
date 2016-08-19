<?php

/**
 * 数据管理
 * @author Endy
 * 15-5-26 下午1:06
 */
class datamanageAction extends Action {
	public $M;
	public function __construct() {
		if (System::$action == 'exportOrderUser' || System::$action == 'exportEggUser') {
			$tmp_index = 0;
		} else {
			$tmp_index = 2;
		}
		parent::__construct ( $tmp_index );
		$this->M = new datamanage ();
	}
	public function index() {
		$tag = $this->M->getList ( 'sys_tag', 'id,name', 'status="1"', null, 'id asc' );
		$this->assign ( 'tag', $tag );
		$this->adminDisplay ();
	}
	
	// 用户信息导出：
	// 发送者openid 发送者昵称 订阅状态 性别 城市 标签 二维码
	public function exportUser() {
		$ctime = strtotime ( $_GET ['ctime'] );
		$etime = strtotime ( $_GET ['etime'] ) + 3600 * 24 - 1;
		$sql = "select openid,nickname,sex,city,province,subscribe,subscribe_time,sid from sys_member where ctime > {$ctime} and ctime < {$etime} order by id";
		$info = $this->M->bysql ( 'sys_member', $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		$subscribe = array (
				'1' => '关注',
				'0' => '取消关注' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['subscribe_time'] = date ( "Y-m-d H:i:s", $info [$k] ['subscribe_time'] );
			$info [$k] ['subscribe'] = $subscribe [$info [$k] ['subscribe']];
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			$info [$k] ['tag'] = $this->tag ( $v ['openid'] );
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			unset ( $info [$k] ['province'] );
		}
		$title = "openid,昵称,性别,城市,订阅状态,订阅时间,二维码,标签";
		$name = "粉丝信息" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 订单用户导出：
	public function exportOrderUser() {
		$sql = "SELECT b.openid,b.nickname,b.sex,b.city,b.province,b.subscribe,b.subscribe_time,b.sid,a.gname,a.gorder,a.gphone,a.ctime FROM sys_gameinfo_form1 as a LEFT JOIN sys_member as b ON a.openid=b.openid";
		// $sql = "select openid,nickname,sex,city,province,subscribe,subscribe_time,sid from sys_member where ctime > {$ctime} and ctime < {$etime} order by id";
		$info = $this->M->query ( $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		$subscribe = array (
				'1' => '关注',
				'0' => '取消关注' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['subscribe_time'] = date ( "Y-m-d H:i:s", $info [$k] ['subscribe_time'] );
			$info [$k] ['subscribe'] = $subscribe [$info [$k] ['subscribe']];
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			// $info[$k]['tag'] = $this->tag($v['openid']);
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			// $info[$k]['ctime'] = date("Y-m-d H:i:s",$v['ctime']);
			unset ( $info [$k] ['province'] );
		}
		$title = "openid,昵称,性别,城市,订阅状态,订阅时间,二维码,姓名,订单号,手机号,领奖时间";
		$name = "订单粉丝信息" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	// 砸蛋用户导出：
	public function exportEggUser() {
		$sql = "SELECT b.openid,b.nickname,b.sex,b.city,b.province,b.subscribe,b.subscribe_time,b.sid,a.gname,a.gorder,a.gphone,a.ctime,c.gid,c.address FROM sys_egg as c LEFT JOIN sys_gameinfo as a ON c.order=a.gorder LEFT JOIN sys_member as b ON c.openid=b.openid WHERE c.openid!=''";
		// $sql = "select openid,nickname,sex,city,province,subscribe,subscribe_time,sid from sys_member where ctime > {$ctime} and ctime < {$etime} order by id";
		$info = $this->M->query ( $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		$subscribe = array (
				'1' => '关注',
				'0' => '取消关注' 
		);
		$gift_arr = array (
				'智能水杯',
				'舒适抱枕',
				'野营背包' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['subscribe_time'] = date ( "Y-m-d H:i:s", $info [$k] ['subscribe_time'] );
			$info [$k] ['subscribe'] = $subscribe [$info [$k] ['subscribe']];
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			// $info[$k]['tag'] = $this->tag($v['openid']);
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			// $info[$k]['ctime'] = date("Y-m-d H:i:s",$v['ctime']);
			unset ( $info [$k] ['province'] );
			$info [$k] ['gid'] = $gift_arr [$v ['gid'] - 1];
		}
		$title = "openid,昵称,性别,城市,订阅状态,订阅时间,二维码,姓名,订单号,手机号,领奖时间,奖品,地址";
		$name = "砸蛋粉丝信息" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 粉丝取消关注数据：
	// 发送者openid 发送者昵称 性别 城市 取消关注时间
	public function exportUnsubscribe() {
		$ctime = strtotime ( $_GET ['ctime'] );
		$etime = strtotime ( $_GET ['etime'] ) + 3600 * 24 - 1;
		$sql = "select m.openid,m.nickname,m.sex,m.city,m.province,g.ctime from sys_get_msg g left join sys_member m on g.fromusername = m.openid where g.event='unsubscribe' and g.ctime > {$ctime} and g.ctime < {$etime} order by g.id";
		$info = $this->M->bysql ( 'sys_get_msg', $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		// $subscribe = array('1'=>'关注','0'=>'取消关注');
		// subscribe订阅，unsubscribe取消订阅，SCAN扫描二维码，LOCATION上报地理位置，CLICK点击自定义菜单，VIEW点击菜单跳转链接
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			$info [$k] ['ctime'] = date ( "Y-m-d H:i:s", $info [$k] ['ctime'] );
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			unset ( $info [$k] ['province'] );
		}
		$title = "openid,昵称,性别,城市,取消关注时间";
		$name = "粉丝取消关注数据" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 粉丝交互信息图片数据：（图片）
	// 发送者openid 发送者昵称 性别 城市 发送时间 图片链接
	public function exportImageMsg() {
		$ctime = strtotime ( $_GET ['ctime'] );
		$etime = strtotime ( $_GET ['etime'] ) + 3600 * 24 - 1;
		$sql = "select m.openid,m.nickname,m.sex,m.city,m.province,g.ctime,g.picurl from sys_get_msg g left join sys_member m on g.fromusername=m.openid where g.msgtype='image' and g.ctime < {$etime} and g.ctime > {$ctime} order by g.id";
		$info = $this->M->bysql ( 'sys_get_msg', $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			$info [$k] ['ctime'] = date ( "Y-m-d H:i:s", $info [$k] ['ctime'] );
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			unset ( $info [$k] ['province'] );
		}
		$title = "openid,昵称,性别,城市,发送时间,图片链接";
		$name = "粉丝交互信息图片数据" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 交互信息列表：（文本）
	// 发送者openid 发送者昵称 性别 城市 发送时间 内容
	public function exportTextMsg() {
		$ctime = strtotime ( $_GET ['ctime'] );
		$etime = strtotime ( $_GET ['etime'] ) + 3600 * 24 - 1;
		$sql = "select m.openid,m.nickname,m.sex,m.city,m.province,g.ctime,g.content from sys_get_msg g left join sys_member m on g.fromusername=m.openid where g.msgtype='text' and g.ctime < {$etime} and g.ctime > {$ctime} order by g.id";
		$info = $this->M->bysql ( 'sys_get_msg', $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			$info [$k] ['content'] = $this->nickname ( $info [$k] ['content'] );
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			$info [$k] ['ctime'] = date ( "Y-m-d H:i:s", $info [$k] ['ctime'] );
			unset ( $info [$k] ['province'] );
		}
		$title = "openid,昵称,性别,城市,发送时间,文本内容";
		$name = "粉丝交互信息文本数据" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 按标签导出：
	// 发送者openid 发送者昵称 订阅状态 性别 城市
	public function exportTag() {
		$tagid = $_GET ['tagid'];
		$tag = $this->M->getInfo ( 'sys_tag', "id={$tagid}" );
		$userssql = "select mopenid,ctime from sys_mem_tag_relation where tagid={$tagid}";
		$users = $this->M->bysql ( 'sys_mem_tag_relation', $userssql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		$subscribe = array (
				'1' => '关注',
				'0' => '取消关注' 
		);
		foreach ( $users as $k => $v ) {
			$user = $this->M->getInfo ( 'sys_member', "openid='{$v['mopenid']}'", 'openid,nickname,sex,province,city,subscribe' );
			$user = $user [0];
			$user ['nickname'] = $this->nickname ( $user ['nickname'] );
			$user ['sex'] = $sex [$user ['sex']];
			$user ['subscribe'] = $subscribe [$user ['subscribe']];
			$user ['city'] = $user ['province'] . ' ' . $user ['city'];
			unset ( $user ['province'] );
			if ($v ['ctime']) {
				$user ['ctime'] = date ( 'Y-m-d H:i:s', $v ['ctime'] );
			}
			$info [] = $user;
			unset ( $user );
		}
		unset ( $users );
		$title = "openid,昵称,性别,城市,订阅状态,加入时间";
		$name = $tag ['name'] . "标签用户信息" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	
	// 48小时交互
	// 发送者openid 发送者昵称 性别 城市 二维码 交互类型 交互时间
	public function exportNewMsg48() {
		$ctime = strtotime ( $_GET ['ctime'] );
		$etime = strtotime ( $_GET ['etime'] ) + 3600 * 24 - 1;
		$sql = "select m.openid,m.nickname,m.sex,m.city,m.province,m.sid,g.msgtype,g.`event`,g.ctime from sys_get_msg g left join sys_member m on g.fromusername=m.openid where g.ctime < {$etime} and g.ctime > {$ctime} order by g.id";
		$info = $this->M->bysql ( 'sys_get_msg', $sql );
		$sex = array (
				'1' => '男',
				'2' => '女',
				'0' => '未知' 
		);
		// $subscribe = array('1'=>'关注','0'=>'取消关注');
		// subscribe订阅，unsubscribe取消订阅，SCAN扫描二维码，LOCATION上报地理位置，CLICK点击自定义菜单，VIEW点击菜单跳转链接
		$event = array (
				'link' => '链接消息',
				'location' => '地理位置',
				'video' => '视频消息',
				'voice' => '音频消息',
				'image' => '图片消息',
				'text' => '文本消息',
				'subscribe' => '订阅',
				'unsubscribe' => '取消订阅',
				'SCAN' => '扫描二维码',
				'LOCATION' => '上报地理位置',
				'CLICK' => '点击自定义菜单',
				'VIEW' => '点击菜单跳转链接',
				'MASSSENDJOBFINISH' => '高级群发返回结果',
				'pic_photo_or_album' => '图片消息',
				'pic_sysphoto' => '图片信息',
				'pic_weixin' => '图片信息',
				'scancode_push' => '扫码',
				'location_select' => '地理位置' 
		);
		foreach ( $info as $k => $v ) {
			$info [$k] ['nickname'] = $this->nickname ( $info [$k] ['nickname'] );
			$info [$k] ['sex'] = $sex [$info [$k] ['sex']];
			if ($info [$k] ['msgtype'] == 'event') {
				$info [$k] ['event'] = $event [$info [$k] ['event']] ? $event [$info [$k] ['event']] : $info [$k] ['event'];
			} else {
				$info [$k] ['event'] = $event [$info [$k] ['msgtype']] ? $event [$info [$k] ['msgtype']] : $info [$k] ['msgtype'];
			}
			unset ( $info [$k] ['msgtype'] );
			$info [$k] ['city'] = $info [$k] ['province'] . ' ' . $info [$k] ['city'];
			unset ( $info [$k] ['province'] );
			$info [$k] ['ctime'] = date ( "Y-m-d H:i:s", $info [$k] ['ctime'] );
		}
		$title = "openid,昵称,性别,城市,二维码,交互类型,交互时间";
		$name = "48小时交互信息" . date ( "Y-m-d", time () );
		$this->export ( $info, $title, $name );
		exit ();
	}
	public function export($date, $title, $name) {
		if (count ( $date ) < 1) {
			$this->showmessage ( '没有数据', '/index.php/datamanage/index', 1 );
		}
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', "-1" );
		// error_reporting(0); //屏蔽提示信息
		// $name = date("Y-m-d", time());
		header ( "Content-type:application/vnd.ms-excel" );
		header ( "Content-Disposition:filename={$name}.csv" );
		
		$arr = explode ( ",", $title );
		$str = implode ( ",", $arr );
		echo iconv ( 'UTF-8', 'GBK', $str ) . "\r";
		foreach ( $date as $v ) {
			echo iconv ( "UTF-8", 'GBK', implode ( ",", $v ) ) . "\r";
		}
	}
	public function tag($openid) {
		// 根据openid获取粉丝标签
		$tag = $this->M->getTagByOpenid ( $openid );
		if ($tag !== false) {
			$sql = "select name from sys_tag where id in ({$tag})";
			$tags = $this->M->bysql ( 'sys_tag', $sql );
			$str = '';
			foreach ( $tags as $k => $v ) {
				$str .= $v ['name'] . '，';
			}
			return rtrim ( $str, '，' );
		}
		return '';
	}
	public function nickname($nickname) {
		$arr = array ();
		$pattern = '/([a-zA-Z0-9\x{4e00}-\x{9fa5}])+/u';
		preg_match_all ( $pattern, $nickname, $arr );
		$res = implode ( '', $arr [0] );
		return $res;
	}
}

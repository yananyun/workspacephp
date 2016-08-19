<?php
/**
 * loginAction.class.php
 *			用户管理
 * 
 * @author Harry
 * @link http://haoshengzhide.com 
 * @since 2014.7.11
 */
class GameInfoAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 1 );
		$this->M = new GameInfo ();
	}
	public function makeQrcode() {
		echo 1;
		$wechatApi = new WechatApi ( '1WAjXnt0j0y38bkHbBoJVmLv99MSmvtIBzBbzvuIcvSGwuTF6tb6QX64OnV1sU9SjJ74IdUGfQqvcJHQe0aCaEDtkLTUfA2IozneNxPTrIM' );
		// $wechatApi = new WechatApi('p9UPXrcZIhlOiNRZ-IpWzdLpGx8XbUULAvaX4Pt2KM2c_VCsAjzyMmPyjhd9g68O02D5m3WBbSrMGimTUH2fXVgJKUZljXclrDIjc4RL1W0');
		$rs = $wechatApi->makeQrTicket ( 100, 'QR_LIMIT_SCENE' );
		p ( $rs );
	}
	public function index() {
		$this->display ( 'egg/form.html' );
	}
	public function init() {
		$M = new user ();
		$sql = "truncate sys_egg";
		$M->query ( $sql );
		
		for($i = 1; $i <= 190; $i ++) {
			$sql = "insert into sys_egg(`gid`) value('1')";
			$M->query ( $sql );
		}
		
		for($i = 1; $i <= 1000; $i ++) {
			$sql = "insert into sys_egg(`gid`) value('2')";
			$M->query ( $sql );
		}
		
		for($i = 1; $i <= 1000; $i ++) {
			$sql = "insert into sys_egg(`gid`) value('3')";
			$M->query ( $sql );
		}
	}
	public function show() {
		$template = isset ( $_GET ['tl'] ) && $_GET ['tl'] ? $_GET ['tl'] : 'index';
		$template = explode ( '.', $template );
		$template = $template [0];
		if ($template == 'form1') {
			$gorder = date ( 'ndHis', time () ) . rand ( 1000, 9999 );
			$this->assign ( 'gorder', $gorder );
		}
		$this->display ( 'egg/' . $template . '.html' );
	}
	public function egg() {
		$gid = 0;
		$order = $_SESSION ['egg_order'];
		
		$openid = $this->memberId;
		// 如果重复进入，则将之前的中的奖品给出
		$sql = "select id,gid from sys_egg  where order = '$order'";
		$uinfo = $this->M->query ( $sql );
		if ($uinfo) {
			$gid = $uinfo [0] ['gid'];
		} else {
			$sql = "select id,gid from sys_egg  where openid = '' order by rand() limit 1";
			$tmp = $this->M->query ( $sql );
			if ($tmp) {
				$id = $tmp [0] ['id'];
				$gid = $tmp [0] ['gid'];
				
				$this->M->db->tableName = 'sys_egg';
				$row ['openid'] = $openid;
				$row ['uptime'] = time ();
				$row ['order'] = $order;
				$result = $this->M->update ( $row, "id = $id" );
			}
		}
		
		$this->assign ( 'gid', $gid );
		
		$this->display ( 'egg/egg.html' );
	}
	function info() {
		$order = $_SESSION ['egg_order'];
		$sql = "select id,gid from sys_egg  where `order` = '$order'";
		$uinfo = $this->M->query ( $sql );
		$this->assign ( 'info', $uinfo [0] );
		$this->display ( "egg/Infor.html" );
	}
	function form1() {
		$this->display ( 'egg/form1.html' );
	}
	function form1_save() {
		$gname = isset ( $_POST ['gname'] ) && ! empty ( $_POST ['gname'] ) ? trim ( safe_replace ( $_POST ['gname'] ) ) : '';
		$gphone = isset ( $_POST ['gphone'] ) && ! empty ( $_POST ['gphone'] ) ? trim ( safe_replace ( $_POST ['gphone'] ) ) : '';
		$gorder = isset ( $_POST ['gorder'] ) && ! empty ( $_POST ['gorder'] ) ? trim ( safe_replace ( $_POST ['gorder'] ) ) : '';
		$ctime = date ( 'Y-m-d H:i:s' );
		
		// check the order repeat
		if (empty ( $gorder )) {
			ajaxReturn ( "", "订单号不能为空", 0 );
		}
		
		if (empty ( $gname )) {
			ajaxReturn ( "", "姓名不能为空", 0 );
		}
		if (empty ( $gphone )) {
			ajaxReturn ( "", "手机号不能为空", 0 );
		}
		$sql = "select * from sys_gameinfo_form1 where gorder='$gorder'";
		$info = $this->M->query ( $sql );
		if ($info) {
			ajaxReturn ( "", "该账号已经领取过奖品,不能重复使用", 0 );
		}
		$sql = "select * from sys_gameinfo_form1 where gphone='$gphone'";
		$info = $this->M->query ( $sql );
		if ($info) {
			ajaxReturn ( "", "该手机号已经领取过奖品,不能重复使用", 0 );
		}
		
		$this->M->db->tableName = 'sys_gameinfo_form1';
		unset ( $row );
		$row ['gname'] = $gname;
		$row ['openid'] = $this->memberId;
		$row ['gphone'] = $gphone;
		$row ['gorder'] = $gorder;
		$row ['ctime'] = $ctime;
		
		$result = $this->M->insert ( $row );
		if ($result) {
			// 给用户推送消息
			$accountInfo = $this->M->getConf ( array (
					'id' => 1 
			) ); // 获取账号信息
			$wechat = new WechatApi ( $accountInfo ['access_token'] ); // 实例化微信接口
			$wechat->putMsg ( $this->memberId, 'text', array (
					'content' => '你的订单号是：' . $gorder 
			) );
			
			ajaxReturn ( "", "操作成功", 1 );
		}
		ajaxReturn ( "", "操作失败", 0 );
	}
	function save_address() {
		$id = intval ( $_POST ['id'] );
		$address = isset ( $_POST ['address'] ) && ! empty ( $_POST ['address'] ) ? trim ( safe_replace ( $_POST ['address'] ) ) : '';
		$this->M->db->tableName = 'sys_egg';
		$row ['address'] = $address;
		$row ['uptime'] = time ();
		
		$result = $this->M->update ( $row, "id = $id" );
		if ($result) {
			ajaxReturn ( "", "地址已提交", 1 );
		}
		ajaxReturn ( "", "操作失败", 0 );
	}
	
	// ajax 调用数据库,选取随机奖品id 传到egg.html
	
	// 执行填写信息操作
	public function addgame() {
		$where = array ();
		$order = $where ['gorder'] = filter_input ( INPUT_POST, 'userorder' );
		$where ['gname'] = filter_input ( INPUT_POST, 'gname' );
		$where ['gphone'] = filter_input ( INPUT_POST, 'gphone' );
		
		// if(!trim($where['gphone'] || )
		// {
		// echo json_encode(array('status' => 0, 'msg' => '订单号必须10位全数字'));exit;
		// }
		// 查询订单号是否存在
		$flag = $this->M->select ( NUll, array (
				'gorder' => $where ['gorder'] 
		) );
		if (! empty ( $flag )) {
			echo json_encode ( array (
					'status' => 0,
					'msg' => '订单号已存在' 
			) );
			exit ();
		}
		
		// 获取系统时间
		$time = time ();
		$where ['ctime'] = date ( 'Y-m-d H:i:s', $time );
		
		// $this->M->tableName = 'gameinfo';
		// $return = $this->M->add($where);
		$return = $this->M->addInfo ( $where );
		
		if ($return) {
			$_SESSION ['egg_order'] = $order;
			echo json_encode ( array (
					'status' => 1,
					'msg' => '成功' 
			) );
			exit ();
		} else {
			echo json_encode ( array (
					'status' => 0,
					'msg' => '失败' 
			) );
			exit ();
		}
	}
}	
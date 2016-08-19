<?php
/**
 * userAction.class.php
 *		成员管理
 * 
 * @author Harry
 * @since 20140711
 * @link http://haoshengzhide.com/
 */
class userAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new user ();
	}
	public function list_manage() {
		$this->display ( 'admin/user/list_manage.html' );
	}
	public function index() {
		$where = array ();
		$where ['p'] = $_GET ['p'] ? ( int ) $_GET ['p'] : 1;
		$where ['pagesize'] = 10;
		
		$result = $this->M->getList ( $where );
		$pages = $this->pages ( $result ['total'], $where ['p'], $where ['pagesize'], 10 );
		
		$this->assign ( 'pages', $pages );
		$this->assign ( 'list', $result ['list'] );
		$this->display ( 'admin/user/index.html' );
	}
	public function add() {
		$this->adminDisplay ();
	}
	public function doadd() {
		$info = array ();
		$info ['nickname'] = filter_input ( INPUT_POST, 'nickname' );
		$info ['username'] = filter_input ( INPUT_POST, 'username' );
		$info ['password'] = $this->M->mymd5 ( filter_input ( INPUT_POST, 'password' ) );
		$info ['password2'] = $this->M->mymd5 ( filter_input ( INPUT_POST, 'password2' ) );
		$info ['ctime'] = time ();
		
		if ($info ['password'] != $info ['password2']) {
			redirect ( '/index.php/user/add', 1, '密码不一致，请重新输入' );
		}
		unset ( $info ['password2'] );
		
		$chkData = $this->M->chkAdd ( $info ['username'] );
		if ($chkData) {
			$return = $this->M->add ( $info );
			if ($return) {
				redirect ( '/index.php/user/index' );
			} else {
				redirect ( '/index.php/user/index', 1, '写入失败，请重试' );
			}
		} else {
			redirect ( '/index.php/user/index', 1, '用户名重复' );
		}
	}
	
	// 权限列表
	public function ranklist() {
		return array (
				'运营概况' => 'statistical',
				'自动回复' => 'reply',
				'自定义菜单' => 'menu',
				'消息发布' => 'groupMessage',
				'消息管理' => 'message',
				'用户组管理' => 'group',
				'素材管理' => 'material',
				'订单管理' => 'orderManage',
				
				'会员管理' => 'member',
				'成员管理' => 'user' 
		)
		// '个人中心' => 'me',
		;
	}
	public function mkRank() {
		$userInfo = $this->M->getInfoByWhere ( array (
				'id' => ( int ) $_GET ['id'] 
		) );
		$rankArr = explode ( ',', $userInfo [0] ['rankstr'] );
		$rankData = array_flip ( $this->ranklist () );
		$result = array ();
		foreach ( $rankData as $k => $d ) {
			$result [$k] ['name'] = $d;
			if (in_array ( $k, $rankArr )) {
				$result [$k] ['is'] = 1;
			} else {
				$result [$k] ['is'] = 4;
			}
		}
		
		$this->assign ( 'rankList', $result );
		$this->assign ( 'info', $userInfo [0] );
		$this->display ( 'admin/user/mkRank.html' );
	}
	
	// 设置权限
	public function setRank() {
		if ($_POST ['id'] === '1') {
			redirect ( '/index.php/user/index', 1, 'admin用户不可修改' );
		}
		$rankArr = array ();
		foreach ( $_POST ['rank'] as $key => $r ) {
			$rankArr [] = $key;
		}
		$info = array ();
		$info ['rankstr'] = implode ( ',', $rankArr );
		$return = $this->M->updateInfo ( $info, ( int ) $_POST ['id'] );
		if ($return) {
			redirect ( '/index.php/user/index' );
		} else {
			redirect ( '/index.php/user/index', 1, '修改失败' );
		}
	}
	public function updateUser() {
		$userInfo = $this->M->getInfoByWhere ( array (
				'id' => ( int ) $_GET ['id'] 
		) );
		$userInfo = $userInfo [0];
		$rankArr = $this->ranklist ();
		$userRankArr = explode ( ',', $userInfo ['rankstr'] );
		$temp = array ();
		foreach ( $rankArr as $k => $r ) {
			$temp [$k] ['val'] = $r;
			if (in_array ( $r, $userRankArr )) {
				$temp [$k] ['status'] = 1;
			} else {
				$temp [$k] ['status'] = 0;
			}
		}
		
		$this->assign ( 'info', $userInfo );
		$this->assign ( 'rankArr', $temp );
		$this->display ( 'admin/user/updateUser.html' );
	}
	
	// 更新用户信息
	public function doUpdateUser() {
		$info = array ();
		$info ['nickname'] = filter_input ( INPUT_POST, 'nickname' );
		// $info['username'] = filter_input(INPUT_POST, 'username');
		$info ['status'] = $_POST ['status'];
		
		$return = $this->M->updateInfo ( $info, array (
				'id' => ( int ) $_POST ['id'] 
		) );
		if ($return) {
			redirect ( '/index.php/user/index' );
		} else {
			redirect ( '/index.php/user/index', 1, '修改失败' );
		}
	}
	
	// 删除用户信息
	public function deleteUser() {
		if ($_POST ['id'] === '1') {
			redirect ( '/index.php/user/index', 1, 'admin用户不可修改' );
		}
		$info = array ();
		$info ['status'] = '4';
		$return = $this->M->updateInfo ( $info, array (
				'id' => ( int ) $_GET ['id'] 
		) );
		if ($return) {
			redirect ( '/index.php/user/index' );
		} else {
			redirect ( '/index.php/user/index', 1, '修改失败' );
		}
	}
	
	// 重置密码
	public function resetPassword() {
		if ($_GET ['id'] === '1') {
			redirect ( '/index.php/user/index', 1, 'admin用户不可修改' );
		}
		$info = array ();
		$info ['password'] = $this->M->mymd5 ( DEFAULT_PASSWORD );
		
		$return = $this->M->updateInfo ( $info, array (
				'id' => ( int ) $_GET ['id'] 
		) );
		
		redirect ( '/index.php/user/index', 1, '已将密码重置为预设密码（默认为666666）' );
	}
	public function demo() {
		p ( DEFAULT_PASSWORD );
	}
}

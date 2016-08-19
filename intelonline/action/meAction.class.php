<?php
class meAction extends userAction {
	public function __construct() {
		parent::__construct ( 2 );
	}
	
	// 个人中心
	public function index() {
		$userInfo = $_SESSION ['userinfo'];
		
		$this->assign ( 'info', $userInfo );
		$this->display ( 'admin/user/me.html' );
	}
	
	// 修改个人密码
	public function updatePassword() {
		if ($this->M->mymd5 ( filter_input ( INPUT_POST, 'oldPass' ) ) == $_SESSION ['userinfo'] ['password']) {
			if ($_POST ['newPass'] == $_POST ['newPass2']) {
				if ($_POST ['newPass'] == '') {
					redirect ( '/index.php/me/index', 1, '密码不能为空' );
				}
				$info = array ();
				$info ['password'] = $this->M->mymd5 ( filter_input ( INPUT_POST, 'newPass' ) );
				$return = $this->M->updateInfo ( $info, array (
						'id' => $_SESSION ['userinfo'] ['id'] 
				) );
				if ($return) {
					$_SESSION ['userinfo'] ['password'] = $this->M->mymd5 ( filter_input ( INPUT_POST, 'newPass' ) );
					redirect ( '/index.php/me/index', 1, '修改成功' );
				} else {
					redirect ( '/index.php/me/index', 1, '修改失败' );
				}
			} else {
				redirect ( '/index.php/me/index', 1, '两次密码输入不一致' );
			}
		} else {
			redirect ( '/index.php/me/index', 1, '旧密码错误' );
		}
	}
}
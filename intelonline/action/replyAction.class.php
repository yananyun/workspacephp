<?php
/**
 * replyAction.class.php
 *		自动回复
 * 
 * @author Harry
 * @since 20140711
 * @link http://haoshengzhide.com/
 */
class replyAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new reply ();
	}
	public function index() {
		$this->list_manage ();
	}
	public function list_manage() {
		$this->display ( 'admin/reply/list_manage.html' );
	}
	
	// 关键词应答列表
	public function keywordList() {
		$where = array ();
		$where ['p'] = $_GET ['p'] ? ( int ) $_GET ['p'] : 1;
		$where ['pagesize'] = 15;
		$where ['type'] = 1;
		
		$result = $this->M->getKeywordList ( $where );
		$page = $this->pages ( $result ['total'], $where ['p'], $where ['pagesize'], 10, 'intelListWrap' );
		
		$this->assign ( 'list', $result ['list'] );
		$this->assign ( 'pages', $page );
		
		$this->display ( 'admin/reply/keywordList.html' );
	}
	
	// 添加关键词应答
	public function addKeywordReply() {
		$this->assign ( 'isEditor', 0 );
		$this->display ( 'admin/reply/addKeywordReply.html' );
	}
	
	// 编辑关键词应答
	public function editKeywordReply() {
		$id = ( int ) $_GET ['id'];
		$info = $this->M->getInfo ( array (
				'id' => $id 
		), TRUE );
		
		$l = 1;
		if ($info ['materialid'] != 0) {
			$l = 2; // 图文信息
			$Material = new material ();
			$materialInfo = $Material->getMaterialInfo ( $info ['materialid'] );
			$single = count ( $materialInfo ['content'] ['articles'] );
			
			$this->assign ( 'single', $single );
			$this->assign ( 'material', $materialInfo );
		}
		
		$this->assign ( 'info', $info );
		$this->assign ( 'isEditor', 1 );
		$this->assign ( 'l', $l );
		$this->display ( 'admin/reply/addKeywordReply.html' );
	}
	public function doaddKeywordReply() {
		$info = array ();
		$info ['uid'] = $_SESSION ['userinfo'] ['id'];
		$info ['type'] = 1;
		$info ['name'] = filter_input ( 0, 'name' );
		$info ['keyword'] = filter_input ( 0, 'keyword' );
		$info ['materialid'] = $_POST ['response_mid'] ? filter_input ( 0, 'response_mid' ) : 0;
		$info ['content'] = $_POST ['response_content'] ? filter_input ( 0, 'response_content' ) : '';
		$info ['mode'] = $_POST ['mode'] ? $_POST ['mode'] : 1;
		$info ['status'] = $_POST ['status'] ? $_POST ['status'] : 1;
		$info ['uptime'] = time ();
		
		if ($_POST ['id']) // update的时候
{
			$return = $this->M->updateInfo ( $info, array (
					'id' => ( int ) $_POST ['id'] 
			) );
		} else { // insert的时候
			$info ['ctime'] = $info ['uptime'];
			$return = $this->M->add ( $info );
		}
		if ($return) {
			redirect ( 'list_manage' );
		}
	}
	public function delReply() {
		$id = ( int ) $_GET ['kid'];
		$this->M->updateInfo ( array (
				'status' => 4 
		), array (
				'id' => $id 
		) );
		echo 1;
	}
	public function chkData() {
		$type = ( int ) $_GET ['type'];
		$isEditor = $_POST ['isEditor'];
		$mode = $_POST ['mode'] ? $_POST ['mode'] : 1;
		if ($type == 1) {
			$nameArr = $this->M->getInfo ( array (
					'name' => filter_input ( INPUT_POST, 'name' ),
					'type' => '1',
					'mode' => "'$mode'",
					'status' => '1' 
			) );
			if (count ( $nameArr ) > 0) {
				if (($isEditor == 1) && (count ( $nameArr ) == 1) && ($nameArr [0] ['id'] == $_POST ['id'])) {
				} else {
					echo 41;
					exit ();
				}
			}
			$keywordArr = $this->M->getInfo ( array (
					'keyword' => filter_input ( INPUT_POST, 'keyword' ),
					'type' => '1',
					'mode' => "'$mode'",
					'status' => '1' 
			) );
			if (count ( $keywordArr ) > 0) {
				if (($isEditor == 1) && (count ( $keywordArr ) == 1) && ($keywordArr [0] ['id'] == $_POST ['id'])) {
				} else {
					echo 42;
					exit ();
				}
			}
			echo 1;
		}
	}
	
	// 添加关注应答
	public function addSubscribeReply() {
		$where = array ();
		$where ['type'] = 2;
		$info = $this->M->getInfo ( $where, 1 );
		
		// 已设图文素材展示~!!!----------------------------------------------------------->start<-----------------------------------------------------------
		$l = 0; // 没有信息
		if (! empty ( $info )) {
			$l = 1; // 文本信息
		}
		if ($info ['materialid'] != 0) {
			$l = 2; // 图文信息
			$Material = new material ();
			$materialInfo = $Material->getMaterialInfo ( $info ['materialid'] );
			$single = count ( $materialInfo ['content'] ['articles'] );
			
			$this->assign ( 'single', $single );
			$this->assign ( 'material', $materialInfo );
		}
		$this->assign ( 'l', $l );
		// 已设图文素材展示~!!!------------------------------------------------------------>end<------------------------------------------------------------
		
		$this->assign ( 'info', $info );
		$this->display ( 'admin/reply/addSubscribeReply.html' );
	}
	public function doaddSubscribeReply() {
		$info = array ();
		$info ['uid'] = $_SESSION ['userinfo'] ['id'];
		$info ['type'] = 2;
		$info ['materialid'] = $_POST ['response_mid'] ? filter_input ( 0, 'response_mid' ) : 0;
		$info ['content'] = $_POST ['response_content'] ? filter_input ( 0, 'response_content' ) : '';
		$info ['status'] = $_POST ['status'] ? $_POST ['status'] : 1;
		$info ['uptime'] = time ();
		if ($_POST ['id']) {
			$return = $this->M->updateInfo ( $info, array (
					'id' => ( int ) $_POST ['id'] 
			) );
		} else {
			$info ['ctime'] = $info ['uptime'];
			$return = $this->M->add ( $info );
		}
		if ($return) {
			redirect ( 'list_manage' );
		}
	}
	
	// 添加自动回复
	public function addAutoReply() {
		$where = array ();
		$where ['type'] = 3;
		$info = $this->M->getInfo ( $where, 1 );
		
		// 已设图文素材展示~!!!----------------------------------------------------------->start<-----------------------------------------------------------
		$l = 0; // 没有信息
		if (! empty ( $info )) {
			$l = 1; // 文本信息
		}
		if ($info ['materialid'] != 0) {
			$l = 2; // 图文信息
			$Material = new material ();
			$materialInfo = $Material->getMaterialInfo ( $info ['materialid'] );
			$single = count ( $materialInfo ['content'] ['articles'] );
			
			$this->assign ( 'single', $single );
			$this->assign ( 'material', $materialInfo );
		}
		$this->assign ( 'l', $l );
		// 已设图文素材展示~!!!------------------------------------------------------------>end<------------------------------------------------------------
		
		$this->assign ( 'info', $info );
		$this->display ( 'admin/reply/addAutoReply.html' );
	}
	public function doaddAutoReply() {
		$info = array ();
		$info ['uid'] = $_SESSION ['userinfo'] ['id'];
		$info ['type'] = 3;
		$info ['materialid'] = $_POST ['response_mid'] ? filter_input ( 0, 'response_mid' ) : 0;
		$info ['content'] = $_POST ['response_content'] ? filter_input ( 0, 'response_content' ) : '';
		$info ['status'] = $_POST ['status'] ? $_POST ['status'] : 1;
		$info ['uptime'] = time ();
		if ($_POST ['id']) {
			$return = $this->M->updateInfo ( $info, array (
					'id' => ( int ) $_POST ['id'] 
			) );
		} else {
			$info ['ctime'] = $info ['uptime'];
			$return = $this->M->add ( $info );
		}
		if ($return) {
			redirect ( 'list_manage' );
		}
	}
}
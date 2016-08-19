<?php
/**
 * groupAction.class.php
 *		用户组管理
 * 
 * @author:Harry
 * @since:2014.7.4 
 */
class groupAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new group ();
	}
	public function list_manage() {
		$this->display ( 'admin/group/list_manage.html' );
	}
	
	// 获取分组list
	public function groupList() {
		$where = array ();
		$where ['p'] = $_GET ['p'] ? ( int ) $_GET ['p'] : 1;
		$where ['pagesize'] = 15;
		
		$result = $this->M->getList ( $where );
		$pages = $this->pages ( $result ['total'], $where ['p'], $where ['pagesize'], 10, 'intelListWrap' );
		
		$this->assign ( 'list', $result ['list'] );
		$this->assign ( 'pages', $pages );
		
		$this->display ( 'admin/group/groupList.html' );
	}
	// 获取分组list
	public function ajaxGroupList() {
		if ($_GET ["openid"]) {
			$openid = $_GET ["openid"];
			
			$data = $this->M->getMemGroupRelationByOpenid ( array (
					'mopenid' => $openid 
			) );
			
			if ($data) {
				$groupid = $data [0] ['groupid'];
			}
		}
		
		$result = $this->M->getGroupByWhere ( array (
				'status' => '1' 
		) );
		$html = '';
		foreach ( $result as $v ) {
			if ($groupid) {
				if ($groupid == $v ['id']) {
					$html .= '<input type="radio" checked="checked" value="' . $v ['id'] . '" name="tag">' . $v ['name'];
				} else {
					$html .= '<input type="radio" value="' . $v ['id'] . '" name="tag">' . $v ['name'];
				}
			} else {
				$html .= '<input type="radio" value="' . $v ['id'] . '" name="tag">' . $v ['name'];
			}
		}
		echo $html;
	}
	
	// add a new user group
	public function add() {
		$this->display ( 'admin/group/add.html' );
	}
	
	// to do add
	public function doadd() {
		$info = array ();
		$info ['name'] = htmlspecialchars ( addslashes ( $_POST ['groupName'] ) );
		$info ['remark'] = htmlspecialchars ( addslashes ( $_POST ['groupDes'] ) );
		$info ['uid'] = $_SESSION ['userinfo'] ['id'];
		$info ['status'] = 1;
		$info ['ctime'] = time ();
		
		$gid = $this->M->addUserGroup ( $info );
		redirect ( 'list_manage' );
	}
	
	// 删除用户分组
	public function deleteUserGroup() {
		$mopenid = isset ( $_GET ['openid'] ) && ! empty ( $_GET ['openid'] ) ? trim ( $_GET ['openid'] ) : '';
		if ($mopenid) {
			$this->M->tableName = 'mem_group_relation';
			$this->M->delete ( "mopenid='$mopenid'" );
		} else {
			$info = array (
					'status' => 4,
					'uptime' => time () 
			);
			$where = array (
					'id' => ( int ) $_GET ['id'] 
			);
			
			$this->M->updateUserGroup ( $info, $where );
			$this->M->deleteMemGroupRelation ( ( int ) $_GET ['id'] );
		}
	}
	
	// 编辑操作
	public function edit() {
		$info = $this->M->getGroupInfo ( array (
				'id' => ( int ) $_GET ['id'] 
		) );
		$condition = json_decode ( $info [0] ['condition'], true );
		
		$this->assign ( 'info', $info [0] );
		$this->assign ( 'condition', $condition );
		$this->display ( 'admin/group/edit.html' );
	}
	
	// do edit
	public function doedit() {
		$id = ( int ) $_POST ['id'];
		$where = array (
				'id' => $id 
		);
		
		$info = array ();
		$info ['name'] = htmlspecialchars ( addslashes ( $_POST ['groupName'] ) );
		$info ['remark'] = htmlspecialchars ( addslashes ( $_POST ['groupDes'] ) );
		$info ['uid'] = $_SESSION ['userinfo'] ['id'];
		$info ['uptime'] = time ();
		$gid = $this->M->updateUserGroup ( $info, $where );
		// $this->M->deleteMemGroupRelation($id);
		redirect ( 'list_manage' );
	}
	
	/**
	 * 将用户添加到分组
	 */
	public function addMemGroup() {
		$groupId = $_POST ['groupId'];
		$openid = $_POST ['openid'];
		// $openid_arr = array();
		// foreach($openid as $v)
		// {
		// $openid_arr[] = array('openid' => $v);
		// }
		$this->M->addUserGroupRelation ( $groupId, array (
				'openid' => $openid 
		) );
	}
	public function groupMember() {
		$groupId = $_GET ['groupId'];
		$this->assign ( 'groupId', $groupId );
		$this->display ( 'admin/group/groupMember.html' );
	}
	public function groupMemberList() {
		$groupId = $_GET ['groupId'];
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 12;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		$param ['groupId'] = $groupId;
		$param ['nickname'] = isset ( $_GET ['nickname'] ) && ! empty ( $_GET ['nickname'] ) ? urldecode ( trim ( safe_replace ( $_GET ['nickname'] ) ) ) : '';
		$groupId = $_GET ['groupId'];
		$list = $this->M->getMemberByGroupid ( $param );
		
		$total = $list ['total'];
		$pages = $this->newPages ( $total, $p, $pagesize, 2, 'intelListWrap' );
		$this->assign ( array (
				'sexArr' => array (
						1 => '男',
						2 => '女' 
				),
				'groupId' => $groupId,
				'list' => $list ['data'],
				'pages' => $pages 
		) );
		$this->display ( 'admin/group/groupMemberList.html' );
	}
} 
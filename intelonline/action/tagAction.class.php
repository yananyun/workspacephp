<?php
/**
 * tagAction.class.php
 *		用户组管理
 * 
 * @author:Harry
 * @since:2014.7.4 
 */
class tagAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new tag ();
	}
	function test() {
		echo '<pre>';
		var_dump ( json_decode ( '[{"fromusername":"oil0zt1ouPClviV6PAFluob-4rXs","num":"70"},{"fromusername":"oil0zt5hDYTPu3szvw7v3hI9Upyo","num":"40"},{"fromusername":"oil0zt5WXIaVs_ST-avhNj2VNN-M","num":"71"},{"fromusername":"oil0zt8T8ktyeufuSDKtGZs9XChU","num":"102"},{"fromusername":"oil0ztzvTvcOdCXReVJYmXjUbzPQ","num":"51"},{"fromusername":"oil0zt_Oxnm7AcQLXbByQCH93CHw","num":"49"}]', true ) );
	}
	public function list_manage() {
		$this->display ( 'admin/tag/list_manage.html' );
	}
	
	// 获取标签list
	public function tagList() {
		$where = array ();
		$where ['p'] = $_GET ['p'] ? ( int ) $_GET ['p'] : 1;
		$where ['pagesize'] = 15;
		
		$result = $this->M->getList ( $where );
		$pages = $this->pages ( $result ['total'], $where ['p'], $where ['pagesize'], 10, 'intelListWrap' );
		foreach ( $result ['list'] as $k => $v ) {
			$result ['list'] [$k] ['num'] = $this->M->getTagNumByWhere ( array (
					'tagid' => $v ['id'] 
			) );
		}
		
		$this->assign ( 'list', $result ['list'] );
		$this->assign ( 'pages', $pages );
		
		$this->display ( 'admin/tag/tagList.html' );
	}
	// 获取分组list
	public function ajaxTagList() {
		if ($_GET ["openid"]) {
			$openid = $_GET ["openid"];
		}
		
		$result = $this->M->getTagByWhere ( array (
				'status' => '1' 
		) );
		$html = '';
		foreach ( $result as $v ) {
			$tagid = $v ['id'];
			if ($openid) {
				$data = $this->M->getMemTagsByOpenid ( array (
						'mopenid' => $openid,
						"tagid" => $tagid 
				) );
				if (! empty ( $data )) {
					$html .= '<input type="checkbox" checked="checked" value="' . $v ['id'] . '">' . $v ['name'];
				} else {
					$html .= '<input type="checkbox" value="' . $v ['id'] . '">' . $v ['name'];
				}
			} else {
				$html .= '<input type="checkbox" value="' . $v ['id'] . '">' . $v ['name'];
			}
		}
		echo $html;
	}
	
	// add a new user tag
	public function add() {
		$this->display ( 'admin/tag/add.html' );
	}
	
	// to do add
	public function doadd() {
		$info = array ();
		$info ['name'] = htmlspecialchars ( addslashes ( $_POST ['tagName'] ) );
		$info ['desc'] = htmlspecialchars ( addslashes ( $_POST ['tagDes'] ) );
		foreach ( $_POST ['qrcode'] as $k => $v ) {
			$_POST ['qrcode'] [$k] = '--' . $v . '--';
		}
		$info ['qrcode'] = implode ( ',', $_POST ['qrcode'] );
		foreach ( $_POST ['active_url'] as $k => $v ) {
			$_POST ['active_url'] [$k] = '--' . $v . '--';
		}
		$info ['active_url'] = implode ( ',', $_POST ['active_url'] );
		foreach ( $_POST ['keywords'] as $k => $v ) {
			$_POST ['keywords'] [$k] = '--' . $v . '--';
		}
		$info ['keywords'] = implode ( ',', $_POST ['keywords'] );
		// $info['uid'] = $_SESSION['userinfo']['id'];
		$info ['ctime'] = time ();
		
		$gid = $this->M->addUserTag ( $info );
		redirect ( 'list_manage' );
	}
	
	// //删除用户标签
	// public function deleteUserTag()
	// {
	// $info = array('status'=>4,'uptime'=>time());
	// $where = array('id' => (int)$_GET['id']);
	//
	// $this->M->updateUserTag($info,$where);
	// }
	
	// 编辑操作
	public function edit() {
		$info = $this->M->getTagInfo ( array (
				'id' => ( int ) $_GET ['id'] 
		) );
		// $condition = json_decode($info[0]['condition'], true);
		$qrcodes = explode ( ',', $info [0] ['qrcode'] );
		$active_url = explode ( ',', $info [0] ['active_url'] );
		$keywords = explode ( ',', $info [0] ['keywords'] );
		$this->assign ( 'info', $info [0] );
		$this->assign ( 'qrcodes', $qrcodes );
		$this->assign ( 'active_url', $active_url );
		$this->assign ( 'keywords', $keywords );
		// $this->assign('condition',$condition);
		$this->display ( 'admin/tag/edit.html' );
	}
	
	// do edit
	public function doedit() {
		$id = ( int ) $_POST ['id'];
		$where = array (
				'id' => $id 
		);
		
		$info = array ();
		$info ['name'] = htmlspecialchars ( addslashes ( $_POST ['tagName'] ) );
		$info ['desc'] = htmlspecialchars ( addslashes ( $_POST ['tagDes'] ) );
		foreach ( $_POST ['qrcode'] as $k => $v ) {
			$_POST ['qrcode'] [$k] = '--' . $v . '--';
		}
		$info ['qrcode'] = implode ( ',', $_POST ['qrcode'] );
		foreach ( $_POST ['active_url'] as $k => $v ) {
			$_POST ['active_url'] [$k] = '--' . $v . '--';
		}
		$info ['active_url'] = implode ( ',', $_POST ['active_url'] );
		foreach ( $_POST ['keywords'] as $k => $v ) {
			$_POST ['keywords'] [$k] = '--' . $v . '--';
		}
		$info ['keywords'] = implode ( ',', $_POST ['keywords'] );
		$info ['uptime'] = time ();
		$gid = $this->M->updateUserTag ( $info, $where );
		redirect ( 'list_manage' );
	}
	
	/**
	 * 将用户添加到标签
	 */
	public function addMemTag() {
		$tagId = $_POST ['tagId'];
		$openid = $_POST ['openid'];
		
		if (is_string ( $openid )) {
			$trans = array (
					"，" => "," 
			);
			$openid = strtr ( $openid, $trans );
			$openid = explode ( ',', $openid );
		}
		// $openid_arr = array();
		// foreach($openid as $v)
		// {
		// $openid_arr[] = array('openid' => $v);
		// }
		foreach ( $tagId as $v ) {
			$this->M->addUserTagRelation ( $v, array (
					'openid' => $openid 
			) );
		}
	}
	public function tagMember() {
		$tagId = $_GET ['tagId'];
		$this->assign ( 'tagId', $tagId );
		$this->display ( 'admin/tag/tagMember.html' );
	}
	public function tagMemberList() {
		$tagId = $_GET ['tagId'];
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 12;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		$param ['tagId'] = $tagId;
		$param ['nickname'] = isset ( $_GET ['nickname'] ) && ! empty ( $_GET ['nickname'] ) ? urldecode ( trim ( safe_replace ( $_GET ['nickname'] ) ) ) : '';
		$tagId = $_GET ['tagId'];
		$list = $this->M->getMemberByTagid ( $param );
		$total = $list ['total'];
		$pages = $this->newPages ( $total, $p, $pagesize, 2, 'intelListWrap' );
		$this->assign ( array (
				'sexArr' => array (
						1 => '男',
						2 => '女' 
				),
				'tagId' => $tagId,
				'list' => $list ['data'],
				'pages' => $pages 
		) );
		$this->display ( 'admin/tag/tagMemberList.html' );
	}
	
	// 删除用户标签
	public function deleteUserTag() {
		$id = ( int ) $_GET ['id'];
		$this->M->query ( "DELETE FROM `sys_mem_tag_relation`  WHERE id=$id " );
	}
} 
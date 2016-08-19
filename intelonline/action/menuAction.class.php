<?php
/**
 * menuAction.class.php	自定义菜单
 * 
 * @author:Harry
 * @link:http://haoshengzhide.com/
 * @since:2013.12.04
 */
class menuAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new menu ();
	}
	
	/**
	 * 菜单管理
	 */
	public function menu_manage() {
		$this->assign ( 'title', '自定义菜单栏' );
		$this->display ( 'admin/menu/menu_manage.html' );
	}
	
	/**
	 * 菜单list
	 */
	public function menuList() {
		$getMenuList = $this->M->getAllMenu ();
		
		$this->assign ( 'getMenuList', $getMenuList );
		$this->display ( 'admin/menu/menuList.html' );
	}
	
	/**
	 * 更新菜单名称
	 */
	public function UpdateNameMenu() {
		$id = trim ( $_POST ['id'] ) ? trim ( $_POST ['id'] ) : '';
		$value = trim ( $_POST ['value'] ) ? trim ( $_POST ['value'] ) : '';
		// 曹洪猛新增,验证菜单名称长度
		// 20131025jiatan
		if (strlen ( $value ) <= 40) {
			if (! empty ( $id ) && is_numeric ( $id )) {
				$result = $this->M->UpdateNameMenu ( $id, $value );
				if (is_numeric ( $result )) {
					ajaxReturn ( $result, '菜单名称更新成功', true );
				} else {
					ajaxReturn ( $result, '菜单名称更新失败', false );
				}
			} else {
				ajaxReturn ( '1000', '非法参数', false );
			}
		} else {
			ajaxReturn ( '1000', '菜单名称超过5个汉字或者16个字符的限制!', false );
		}
	}
	
	/**
	 * 取消一级菜单设置
	 */
	public function cancelMenuSet() {
		$id = trim ( $_POST ['id'] ) ? trim ( $_POST ['id'] ) : '';
		if (! empty ( $id )) {
			$data = array (
					'id' => $id 
			);
			$result = $this->M->cancelMenuSet ( $data );
			ajaxReturn ( $result );
		}
	}
	
	/**
	 * 添加菜单前台
	 */
	function AddMenuChildMenu() {
		$this->display ( 'admin/menu/AddMenuChildMenu.html' );
	}
	public function AddFristMenu() {
		$this->display ( 'admin/menu/AddFristMenu.html' );
	}
	
	/**
	 * 保存添加菜单
	 */
	public function SaveFristMenu() {
		$menuName = trim ( $_POST ['name'] ) ? trim ( $_POST ['name'] ) : '';
		if (! empty ( $menuName )) {
			if (strlen ( $menuName ) <= 16) {
				$id = $this->M->saveMenu ( $menuName );
				if (is_int ( $id ) and $id > 0) {
					ajaxReturn ( $id, '菜单添加成功', true );
				} else {
					ajaxReturn ( $id, '菜单添加失败', false );
				}
			} else {
				ajaxReturn ( 0, '菜单名称超过5个汉字或者16个字符的限制!' . mb_strlen ( $menuName, 'UTF-8' ), false );
			}
		} else {
			ajaxReturn ( 0, '请填写菜单名称选项!', false );
		}
	}
	
	/**
	 * 保存子菜单
	 */
	public function SaveChildMenu() {
		$menuName = trim ( $_POST ['name'] ) ? trim ( $_POST ['name'] ) : '';
		$pid = trim ( $_POST ['pid'] ) ? trim ( $_POST ['pid'] ) : '';
		if (! empty ( $menuName )) {
			// 20131025字节长度限制 不超过40个字节
			if (strlen ( $menuName ) <= 40) {
				$id = $this->M->saveChildMenu ( $menuName, $pid );
				if (is_int ( $id ) and $id > 0) {
					ajaxReturn ( $id, '子菜单添加成功', true );
				} else {
					ajaxReturn ( $id, '子菜单添加失败', false );
				}
			} else {
				ajaxReturn ( 0, '子菜单名称超过13个汉字或者40个字符的限制!', false );
			}
		} else {
			ajaxReturn ( 0, '请填写子菜单名称选项!', false );
		}
	}
	
	/**
	 * 子菜单统计
	 */
	public function CountChildMenu() {
		$id = trim ( $_POST ['id'] ) ? trim ( $_POST ['id'] ) : '';
		if (! empty ( $id )) {
			$result = $this->M->CountChildMenu ( $id );
			ajaxReturn ( $result [0] ['num'] );
		}
	}
	
	/**
	 * 删除菜单
	 */
	public function DeleteMenu() {
		$id = trim ( $_POST ['id'] ) ? trim ( $_POST ['id'] ) : '';
		if (! empty ( $id )) {
			$result = $this->M->DeleteMenu ( $id );
			ajaxReturn ( $result );
		}
	}
	
	/**
	 * 返回资源列表，支持URL和素材
	 */
	public function ResourcesList() {
		$id = $_GET ['id'];
		$menuInfo = $this->M->GetMeunInfo ( $id );
		if ($menuInfo) {
			$this->assign ( 'menuInfo', $menuInfo [0] );
		}
		$this->assign ( 'id', $id );
		$this->display ( 'admin/menu/ResourcesList.html' );
	}
	public function getMaterialList() {
		$MaterialModel = new material ();
		$type = isset ( $_GET ['type'] ) && ! empty ( $_GET ['type'] ) ? trim ( $_GET ['type'] ) : 'news';
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		
		// 总页数
		$total = 0;
		$param ['type'] = $type;
		
		$list = $MaterialModel->getList ( $param );
		
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'materialPicTxtWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'type' => $type 
		) );
		$this->display ( 'admin/menu/getMaterialList.html' );
	}
	
	/**
	 * 更新菜单内容
	 */
	public function updateMenuMsg() {
		$id = addslashes ( $_POST ['id'] );
		$type = addslashes ( $_POST ['type'] );
		
		$urlContent = addslashes ( $_POST ['urlContent'] );
		$textContent = addslashes ( $_POST ['textContent'] );
		$Sucai = addslashes ( $_POST ['Sucai'] );
		
		switch ($type) {
			case 1 :
				$content = $urlContent;
				break;
			case 2 :
				$content = $textContent;
				break;
			case 3 :
				$content = $Sucai;
				break;
		}
		
		if (empty ( $content )) {
			echo 4;
			exit ();
		}
		
		$return = $this->M->updateMenuMsg ( $id, $content, $type );
		if (is_int ( $return ) and $return > 0) {
			echo 1;
		} else {
			echo 4;
		}
	}
	
	/**
	 * 发布自定义菜单
	 */
	public function releaseMenu() {
		// 根据公众账号获取自定义菜单内容
		$menu = $this->M->getAllMenu ();
		if (count ( $menu ) > 3) {
			ajaxReturn ( $err_menu, '自定义菜单总量大于限制!', false );
		}
		// 检测自定义菜单内容,如果发生错误,返回错误信息
		$status = true;
		$release_menu = array ();
		$i = 0;
		foreach ( $menu as $first_menu ) {
			if ($first_menu ['child']) {
				if (count ( $first_menu ['chile'] ) > 5) {
					ajaxReturn ( $err_menu, $first_menu ['name'] . '的子菜单数量大于限制!', false );
				} else {
					// $release_menu['button'][$i]['name'] = urlencode($first_menu['name']);
					$release_menu ['button'] [$i] ['name'] = ($first_menu ['name']);
					$j = 0;
					foreach ( $first_menu ['child'] as $second_menu ) {
						if (! $second_menu ['content']) {
							$status = false;
							$err_menu [] = $second_menu ['id'];
						} else {
							$release_menu ['button'] [$i] ['sub_button'] [$j] ['name'] = ($second_menu ['name']);
							// $release_menu['button'][$i]['sub_button'][$j]['name'] = urlencode($second_menu['name']);
							if ($second_menu ['type'] == 1) {
								$release_menu ['button'] [$i] ['sub_button'] [$j] ['type'] = 'view';
								$release_menu ['button'] [$i] ['sub_button'] [$j] ['url'] = $second_menu ['content'];
							} else {
								$release_menu ['button'] [$i] ['sub_button'] [$j] ['type'] = 'click';
								$release_menu ['button'] [$i] ['sub_button'] [$j] ['key'] = $second_menu ['key'];
							}
						}
						$j ++;
					}
				}
			} else {
				if (! $first_menu ['content']) {
					$status = false;
					$err_menu [] = $first_menu ['id'];
				} else {
					$release_menu ['button'] [$i] ['name'] = ($first_menu ['name']);
					// $release_menu['button'][$i]['name'] = urlencode($first_menu['name']);
					if ($first_menu ['type'] == 1) {
						$release_menu ['button'] [$i] ['type'] = 'view';
						$release_menu ['button'] [$i] ['url'] = $first_menu ['content'];
					} else {
						$release_menu ['button'] [$i] ['type'] = 'click';
						// $release_menu['button'][$i]['key'] = substr($first_menu['key'], 0, 16);
						$release_menu ['button'] [$i] ['key'] = $first_menu ['key'];
					}
				}
			}
			$i ++;
		}
		
		// 如果检测到错误则返回相应错误信息
		if (! $status) {
			ajaxReturn ( $err_menu, '自定义菜单数据错误,部分自定义菜单内容未填写!', false );
		}
		
		// 调用API进行发布
		$api = new wechatAction ();
		$return = $api->releaseMenu ( $release_menu );
		if ($return ['errcode'] == '0') {
			ajaxReturn ( $return ['errcode'], $return ['errmsg'], 1 );
		} else {
			ajaxReturn ( $return ['errcode'], $return ['errmsg'], 0 );
		}
	}
}
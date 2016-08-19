<?php
/**
 * 素材控制器类
 * @author Administrator
 *
 */
class materialAction extends Action {
	public $model = null;
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct ( 2 );
		$this->model = new material ();
	}
	
	/**
	 * 图文素材管理
	 */
	public function pic_txt_msg() {
		$this->display ( 'admin/material/pic_txt_msg.html' );
	}
	
	/**
	 * 文本素材管理
	 */
	public function txt_msg() {
		$this->display ( 'admin/material/txt_msg.html' );
	}
	
	/**
	 * 素材列表
	 */
	public function list_material() {
		$type = isset ( $_GET ['type'] ) && ! empty ( $_GET ['type'] ) ? trim ( $_GET ['type'] ) : 'news';
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		$title = isset ( $_GET ['title'] ) && ! empty ( $_GET ['title'] ) ? trim ( safe_replace ( $_GET ['title'] ) ) : '';
		$title = urldecode ( $title );
		$param ['title'] = $title;
		// 总页数
		$total = 0;
		$param ['type'] = $type;
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		$list = $this->model->getList ( $param );
		if ($type == 'news') {
			foreach ( $list ['data'] as &$vo ) {
				$vo ['ticket'] = $this->getQrcode ( $vo ['articles'] [0] ['mid'] );
			}
		}
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'materialPicTxtWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'type' => $type,
				'baseNum' => $baseNum 
		) );
		$this->display ( 'admin/material/list_material.html' );
	}
	
	/**
	 * 单图文消息创建
	 */
	public function creat_pic_txt() {
		$this->display ( 'admin/material/creat_pic_txt.html' );
	}
	
	/**
	 * 文本素材创建
	 */
	public function creat_txt() {
		$this->display ( 'admin/material/creat_txt.html' );
	}
	
	/**
	 * 多图文消息创建
	 */
	public function creat_pic_txt_multi() {
		$this->display ( 'admin/material/creat_pic_txt_multi.html' );
	}
	
	/**
	 * 编辑文本素材
	 */
	function edit_txt() {
		$mid = isset ( $_GET ['mid'] ) && ! empty ( $_GET ['mid'] ) ? intval ( $_GET ['mid'] ) : 0;
		if (! $mid) {
			exit ( '参数错误' );
		}
		$text = $this->model->getArticleByMid ( $mid );
		$this->assign ( array (
				'material' => $text [0],
				'mid' => $mid 
		) );
		$this->display ( 'admin/material/edit_txt.html' );
	}
	/**
	 * 单图文消息创建
	 */
	public function edit_pic_txt() {
		$mid = isset ( $_GET ['mid'] ) && ! empty ( $_GET ['mid'] ) ? intval ( $_GET ['mid'] ) : 0;
		if (! $mid) {
			exit ( '参数错误' );
		}
		$news = $this->model->getArticleByMid ( $mid );
		$this->assign ( array (
				'material' => $news [0],
				'mid' => $mid 
		) );
		$this->display ( 'admin/material/edit_pic_txt.html' );
	}
	
	/**
	 * 多图文消息创建
	 */
	public function edit_pic_txt_multi() {
		$mid = isset ( $_GET ['mid'] ) && ! empty ( $_GET ['mid'] ) ? intval ( $_GET ['mid'] ) : 0;
		if (! $mid) {
			exit ( '参数错误' );
		}
		$news = $this->model->getArticleByMid ( $mid );
		$this->assign ( array (
				'material' => $news [0],
				'news' => $news,
				'mid' => $mid 
		) );
		$this->display ( 'admin/material/edit_pic_txt_multi.html' );
	}
	
	/**
	 * 图文消息保存
	 */
	public function creat_pic_txt_save() {
		$mid = isset ( $_POST ['mid'] ) && ! empty ( $_POST ['mid'] ) ? intval ( $_POST ['mid'] ) : '';
		/*
		 * $title = isset($_POST['title']) && !empty($_POST['title']) ? safe_replace($_POST['title']) : '';
		 * $author = isset($_POST['author']) && !empty($_POST['author']) ? safe_replace($_POST['author']) : '';
		 * $thumb = isset($_POST['thumb']) && !empty($_POST['thumb']) ? trim($_POST['thumb']) : '';
		 * $indetail = isset($_POST['indetail']) && !empty($_POST['indetail']) ? intval($_POST['indetail']) : 0;
		 * $content = isset($_POST['content']) && !empty($_POST['content']) ? $_POST['content'] : '';
		 * $url = isset($_POST['url']) && !empty($_POST['url']) ? safe_replace($_POST['url']) : '';
		 * $original_url = isset($_POST['original_url']) && !empty($_POST['original_url']) ? safe_replace($_POST['original_url']) : '';
		 */
		$news = isset ( $_POST ['data'] ) && ! empty ( $_POST ['data'] ) ? trim ( $_POST ['data'] ) : '';
		$news = json_decode ( $news, true );
		
		$time = time ();
		
		// 基表信息
		$baseinfo = array ();
		$baseinfo ['type'] = 'news';
		$baseinfo ['uptime'] = $time;
		$baseinfo ['single'] = 1;
		if (count ( $news ) > 1) {
			$baseinfo ['single'] = 2;
		}
		if (empty ( $mid )) {
			$baseinfo ['ctime'] = $time;
			$mid = $this->model->addMaterial ( $baseinfo );
		}
		if ($news) {
			foreach ( $news as $k => $v ) {
				$title = trim ( $v ['title'] );
				if (empty ( $title )) {
					ajaxReturn ( '', '标题不能为空', 0 );
				}
				
				$thumb = trim ( $v ['thumb'] );
				if (empty ( $title )) {
					ajaxReturn ( '', '缩略图不能为空', 0 );
				}
				
				$news [$k] ['content'] = urldecode ( $v ['content'] );
				$news [$k] ['mid'] = $mid;
				$news [$k] ['description'] = urldecode ( $v ['description'] );
				$news [$k] ['isdel'] = 0;
			}
			$result = $this->model->addNews ( $news, $mid );
		}
		
		if ($result && $result != 'test') {
			ajaxReturn ( '', '操作成功', 1 );
		}
		ajaxReturn ( '', '操作失败', 0 );
	}
	
	/**
	 * 移除素材
	 * 
	 * @param number $mid        	
	 */
	public function deleteMaterial() {
		$mid = isset ( $_POST ['mid'] ) && ! empty ( $_POST ['mid'] ) ? intval ( $_POST ['mid'] ) : '';
		$result = $this->model->deleteMaterial ( $mid );
		
		if ($result && $result != 'test') {
			ajaxReturn ( '', '操作成功', 1 );
		}
		ajaxReturn ( '', '操作失败', 0 );
	}
	public function pic_msg() {
		$this->display ( 'admin/material/pic_msg.html' );
	}
	
	/**
	 * 图片素材上传
	 */
	function create_pic_msg() {
		$width = isset ( $_GET ['w'] ) ? intval ( $_GET ['w'] ) : 200;
		$height = isset ( $_GET ['h'] ) ? intval ( $_GET ['h'] ) : 150;
		$upload = new UploadFile ();
		$upload->savePath = ROOT_PATH . '/uploads/orginal/';
		$upload->upload ();
		$result = $upload->getUploadFileInfo ();
		
		if ($result [0]) {
			$filepath = '/uploads/orginal/' . $result [0] ['savename'];
			$filename = $result [0] ['savename'];
			$info = array ();
			$time = time ();
			$info ['type'] = 'image';
			$info ['ctime'] = $time;
			$info ['uptime'] = $time;
			$info ['filepath'] = $filepath;
			$info ['filename'] = $filename;
			$result = $this->model->addMaterial ( $info );
			if ($result && $result != 'test') {
				ajaxReturn ( '', '操作成功', 1 );
			}
		}
		ajaxReturn ( '', '操作失败', 0 );
	}
	
	/**
	 * 添加文本素材
	 */
	function creat_txt_save() {
		$id = isset ( $_POST ['id'] ) && ! empty ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : 0;
		$title = isset ( $_POST ['title'] ) && ! empty ( $_POST ['title'] ) ? trim ( $_POST ['title'] ) : '';
		$content = isset ( $_POST ['content'] ) && ! empty ( $_POST ['content'] ) ? safe_replace ( trim ( $_POST ['content'] ) ) : '';
		if (empty ( $title )) {
			ajaxReturn ( '', '标题不能为空', 0 );
		}
		if (empty ( $content )) {
			ajaxReturn ( '', '内容不能为空', 0 );
		}
		if ($id) {
			$info = array ();
			$info ['title'] = $title;
			$info ['content'] = $content;
			$this->model->tableName = 'material_article';
			$result = $this->model->update ( $info, "id=$id" );
		} else {
			$type = 'text';
			$material = array ();
			$material ['type'] = $type;
			$material ['ctime'] = time ();
			$material ['uptime'] = time ();
			if ($title && $content) {
				$id = $this->model->insert ( $material );
			}
			if ($id) {
				$info = array ();
				$info ['mid'] = $id;
				$info ['title'] = $title;
				$info ['content'] = $content;
				$this->model->tableName = 'material_article';
				$result = $this->model->insert ( $info );
			}
		}
		if ($result) {
			ajaxReturn ( '', '操作成功', 1 );
		}
		ajaxReturn ( '', '操作失败', 0 );
	}
	/**
	 * 编辑图片消息
	 */
	function edit_pic_msg() {
		$mid = isset ( $_POST ['mid'] ) && ! empty ( $_POST ['mid'] ) ? intval ( $_POST ['mid'] ) : '';
		$name = isset ( $_POST ['name'] ) && ! empty ( $_POST ['name'] ) ? safe_replace ( $_POST ['name'] ) : '';
		if (empty ( $mid ) || empty ( $name )) {
			ajaxReturn ( '', '操作失败', 0 );
		}
		$info = array ();
		$info ['filename'] = $name;
		$result = $this->model->updateMaterial ( $info, "id = $mid" );
		if ($result && $result != 'test') {
			ajaxReturn ( '', '操作成功', 1 );
		}
		ajaxReturn ( '', '操作失败', 0 );
	}
	public function voice_msg() {
		$this->display ( 'admin/material/pic_msg.html' );
	}
	public function video_msg() {
		$this->display ( 'admin/material/pic_msg.html' );
	}
	
	/**
	 * 默认操作
	 */
	public function index() {
	}
	/**
	 * 素材添加
	 */
	public function add() {
	}
	/**
	 * 素材添加的执行
	 */
	public function add_do() {
	}
	/**
	 * 素材修改
	 */
	public function update() {
	}
	/**
	 * 素材修改的执行
	 */
	public function update_do() {
	}
	/**
	 * 素材移动的执行
	 */
	public function delete_do() {
	}
	public function material_popup() {
		$type = isset ( $_GET ['type'] ) && ! empty ( $_GET ['type'] ) ? trim ( $_GET ['type'] ) : 'news';
		$this->assign ( array (
				'type' => $type 
		) );
		$this->display ( 'admin/material/material_popup.html' );
	}
	
	/**
	 * 素材列表
	 */
	public function list_material_popup() {
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
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		$list = $this->model->getList ( $param );
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'mWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'type' => $type,
				'bNum' => $baseNum 
		) );
		$this->display ( 'admin/material/list_material_popup.html' );
	}
	
	/**
	 * 蔬菜二维码
	 * 
	 * @param number $mid
	 *        	素材id
	 * @return string
	 */
	public function getQrcode($mid = 0) {
		$qrcode = new qrcode ();
		$result = $qrcode->getInfoByMid ( $mid );
		$ticket = '';
		$scene_id = 800000000 + $mid;
		if ($result) {
			if (time () < $result ['expiretime']) {
				// file_put_contents('./uploads/a.html',$result);
				$ticket = $result ['imgurl'];
			} else {
				$wechat = new WechatApi (); // 实例化微信接口
				$make = $wechat->makeQrTicket ( $scene_id, 'QR_SCENE', 600000 );
				$info = array ();
				$info ['imgurl'] = $make ['ticket'];
				$info ['uptime'] = time ();
				$info ['expiretime'] = time () + 600000;
				if ($qrcode->update ( $info, "id=" . $result ['id'] )) {
					$ticket = $make ['ticket'];
				}
			}
		} else {
			$wechat = new WechatApi (); // 实例化微信接口
			$make = $wechat->makeQrTicket ( $scene_id, 'QR_SCENE', 600000 );
			// file_put_contents('./uploads/a.html',$make['ticket'].' '. $wechat->accesstoken);
			$info = array ();
			$info ['sceneid'] = $scene_id;
			$info ['materialid'] = $mid;
			$info ['imgurl'] = $make ['ticket'];
			$info ['status'] = '1';
			$info ['type'] = '2';
			$info ['expiretime'] = time () + 600000;
			if ($qrcode->addQrcode ( $info )) {
				$ticket = $make ['ticket'];
			}
		}
		// file_put_contents('./uploads/a.html',$make);
		return $ticket;
	}
}
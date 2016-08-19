<?php
/**
 * 最新资讯
 * @author Administrator
 *
 */
class informationAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new information ();
	}
	public function index() {
		// $this->display('admin/tag/list_manage.html');
		$this->adminDisplay ();
	}
	
	// 获取list
	public function lists() {
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		$title = isset ( $_GET ['title'] ) && ! empty ( $_GET ['title'] ) ? safe_replace ( trim ( $_GET ['title'] ) ) : '';
		$param ['title'] = urldecode ( $title );
		// 总页数
		$total = 0;
		$list = $this->M->getListAmin ( $param );
		foreach ( $list ['list'] as &$vo ) {
			$vo ['descption'] = mb_strimwidth ( strip_tags ( $vo ['descption'] ), 0, 100, '...' );
		}
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'WrapLists' );
		$this->assign ( array (
				'list' => $list ['list'],
				'pages' => $pages,
				'baseNum' => $baseNum 
		) );
		$this->adminDisplay ();
	}
	
	/**
	 * 跳转添加页
	 */
	public function add() {
		$this->adminDisplay ();
	}
	/**
	 * 添加或者修改资讯
	 */
	public function save_information() {
		$id = isset ( $_POST ['id'] ) && ! empty ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : 0;
		$title = isset ( $_POST ['title'] ) && ! empty ( $_POST ['title'] ) ? trim ( $_POST ['title'] ) : '';
		$weight = isset ( $_POST ['weight'] ) && ! empty ( $_POST ['weight'] ) ? intval ( $_POST ['weight'] ) : 0;
		$source = isset ( $_POST ['source'] ) && ! empty ( $_POST ['source'] ) ? trim ( $_POST ['source'] ) : '';
		$descption = isset ( $_POST ['descption'] ) && ! empty ( $_POST ['descption'] ) ? trim ( $_POST ['descption'] ) : '';
		$picurl = isset ( $_POST ['picurl'] ) && ! empty ( $_POST ['picurl'] ) ? trim ( $_POST ['picurl'] ) : '';
		$content = trim ( $_POST ['content'] );
		$content = formatUEditor ( $content );
		if (empty ( $title )) {
			ajaxReturn ( null, '标题不能为空', 0 );
		}
		if ($weight > 100) {
			ajaxReturn ( null, '权重最大值为100', 0 );
		}
		/*
		 * if(empty($source)){
		 * ajaxReturn(null,'来源不能为空',0);
		 * }
		 */
		if (empty ( $descption )) {
			ajaxReturn ( null, '描述不能为空', 0 );
		}
		if (empty ( $picurl )) {
			ajaxReturn ( null, '请选择上传图片', 0 );
		}
		if (empty ( $content )) {
			ajaxReturn ( null, '内容不能为空', 0 );
		}
		$info = array ();
		$info ['title'] = $title;
		$info ['weight'] = $weight;
		$info ['source'] = $source;
		$info ['descption'] = $descption;
		$info ['picurl'] = $picurl;
		$info ['content'] = $content;
		$info ['uptime'] = time ();
		if (! $id) {
			$info ['ctime'] = time ();
			$result = $this->M->insert ( $info );
		} else {
			$result = $this->M->update ( $info, "id=$id" );
		}
		if ($result) {
			ajaxReturn ( null, '操作成功', 1 );
		} else {
			ajaxReturn ( null, '操作失败', 1 );
		}
	}
	
	/**
	 * 跳转修改页面
	 */
	public function edit() {
		$id = intval ( $_GET ['id'] );
		$info = $this->M->findById ( $id );
		$this->assign ( array (
				'info' => $info 
		) );
		$this->adminDisplay ();
	}
	
	/**
	 * 发布资讯
	 */
	public function check_status() {
		$id = intval ( $_POST ['id'] );
		$status = trim ( $_POST ['status'] );
		$result = $this->M->update ( array (
				'status' => $status,
				'uptime' => time () 
		), "id=$id" );
		if ($result) {
			if ($status) {
				ajaxReturn ( null, '发布成功', 1 );
			} else {
				ajaxReturn ( null, '取消成功', 1 );
			}
		} else {
			if ($status) {
				ajaxReturn ( null, '发布失败', 0 );
			} else {
				ajaxReturn ( null, '取消失败', 0 );
			}
		}
	}
	/**
	 * 删除资讯
	 */
	public function del() {
		$id = intval ( $_POST ['id'] );
		$result = $this->M->update ( array (
				'isdel' => '1',
				'uptime' => time () 
		), "id=$id" );
		if ($result) {
			ajaxReturn ( null, '删除成功', 1 );
		}
		ajaxReturn ( null, '删除失败', 0 );
	}
	
	/**
	 * 修改权重
	 */
	public function check_weight() {
		$id = intval ( $_POST ['id'] );
		$weight = intval ( $_POST ['weight'] );
		if ($weight > 100) {
			ajaxReturn ( null, '权重最大值为100', 0 );
		}
		$result = $this->M->update ( array (
				'weight' => $weight,
				'uptime' => time () 
		), "id=$id" );
		if ($result) {
			ajaxReturn ( null, '修改成功', 1 );
		} else {
			ajaxReturn ( null, '修改失败', 0 );
		}
	}
	
	/**
	 * 预览
	 */
	public function preview() {
		$id = intval ( $_GET ['id'] );
		$info = $this->M->findById ( $id );
		$this->assign ( array (
				'info' => $info 
		) );
		$this->adminDisplay ();
	}
} 
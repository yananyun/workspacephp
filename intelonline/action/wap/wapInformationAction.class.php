<?php
/**
 * 最新资讯
 * @author Administrator
 *
 */
class wapInformationAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 1 );
		$this->M = new information ();
	}
	public function index() {
		$this->display ( 'wap/information/index.html' );
	}
	
	// 获取list
	public function lists() {
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 6;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		/*
		 * $title = isset($_GET['title']) && !empty($_GET['title']) ? safe_replace(trim($_GET['title'])):'';
		 * $param['title'] = urldecode($title);
		 */
		// 总页数
		$total = 0;
		$list = $this->M->getList ( $param );
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
		$this->display ( 'wap/information/lists.html' );
	}
	public function detail() {
		$id = intval ( $_GET ['id'] );
		$information = $this->M->findById ( $id );
		$this->assign ( 'info', $information );
		$this->display ( 'wap/information/detail.html' );
	}
} 
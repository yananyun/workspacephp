<?php

/*
 * 抓娃娃数据导出
 */
class AenAction extends Action {
	public $model;
	public function __construct() {
		parent::__construct ( false );
		$this->model = new AenModel ();
	}
	public function init() {
	}
	/**
	 * `id` int(11) NOT NULL AUTO_INCREMENT,
	 * `openid` varchar(50) NOT NULL,
	 * `companyname` varchar(100) NOT NULL,
	 * `website` varchar(150) NOT NULL,
	 * `companysize` varchar(80) NOT NULL COMMENT '公司规模',
	 * `sale` varchar(80) NOT NULL COMMENT '公司年营业额',
	 * `identity` enum('1','0') DEFAULT '0',
	 * `industry` varchar(80) NOT NULL COMMENT '公司行业',
	 * `rdnum` varchar(80) NOT NULL COMMENT '研发人数',
	 * `salesnum` varchar(80) NOT NULL COMMENT '销售人数',
	 * `workernum` varchar(80) NOT NULL COMMENT '生产人数',
	 * `productdescription` varchar(500) NOT NULL COMMENT '产品描述',
	 * `productimg` varchar(200) NOT NULL COMMENT '产品图片',
	 * `expectedsales` varchar(80) NOT NULL COMMENT '预计年销售量',
	 * `name` varchar(80) NOT NULL COMMENT '姓名',
	 * `mobile` varchar(20) NOT NULL,
	 * `title` varchar(80) NOT NULL COMMENT '职位',
	 * `email` varchar(100) NOT NULL COMMENT '工作邮箱',
	 * `address` varchar(150) NOT NULL COMMENT '邮寄地址',
	 * `type` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1,待审核,2审核通过,3审核失败',
	 * `reason` varchar(255) DEFAULT NULL COMMENT '审核备注',
	 * `ctime` int(11) NOT NULL,
	 * `is_play` enum('1','2') DEFAULT '1' COMMENT '1,未玩过，2，已经玩过了',
	 */
	
	// si填报信息的数据的导出
	public function exportsignup() {
		$sql = "select * from si_signup order by id";
		$list = $this->model->byquery ( 'si_signup', $sql );
		$type = array (
				'1' => '待审核',
				'2' => '审核成功',
				'3' => '审核失败' 
		);
		$identity = array (
				'1' => '是',
				'0' => '否' 
		);
		$is_play = array (
				'1' => '未玩过',
				"2" => '已经玩过' 
		);
		foreach ( $list as $k => $v ) {
			$list [$k] ['type'] = $type [$list [$k] ['type']];
			$list [$k] ['identity'] = $identity [$list [$k] ['identity']];
			// $list[$k]['nickname'] = $this->filterNickname($list[$k]['nickname']);
			$list [$k] ['ctime'] = date ( "Y-m-d H:i:s", $list [$k] ['ctime'] );
			$list [$k] ['is_play'] = $is_play [$list [$k] ['is_play']];
			unset ( $list [$k] ['id'] );
		}
		$title = "openid,公司名,网址,公司规模,是否已有基于Intel平台的产品,公司年营业额,公司行业,研发人数,销售人数,生产人数,产品描述,产品图片,预计年销售量,姓名,手机,职位,工作邮箱,邮寄地址,审核状态,审核备注,时间,是否玩过娃娃机";
		// $title = "openid,昵称,中奖类型,时间";
		$this->daochu ( $title, $list );
	}
	
	// 导出抓娃娃活动日志
	public function exportdolllog() {
		$sql = "select l.id,l.openid,u.nickname,l.type,l.ctime from newdoll_lottery_log l left join newdoll_user u on l.openid=u.openid order by l.id";
		$list = $this->model->byquery ( 'newdoll_lottery_log', $sql );
		$jiang = array (
				'0' => '未中奖',
				'1' => '50元携程券',
				'2' => '100元携程券',
				'3' => '时光网电影券',
				'4' => '物美超市电影券' 
		);
		foreach ( $list as $k => $v ) {
			$list [$k] ['type'] = $jiang [$list [$k] ['type']];
			$list [$k] ['ctime'] = date ( "Y-m-d H:i:s", $list [$k] ['ctime'] );
		}
		$title = "id,openid,昵称,中奖类型,时间";
		$this->daochu ( $title, $list );
	}
	
	// 导出抓娃娃中奖数据
	public function exportdollinfo() {
		$sql = "select l.openid,u.nickname,l.type,g.number,g.code,l.ctime from newdoll_lottery_log l left join newdoll_gift g on l.ctime=g.ctime  LEFT JOIN  newdoll_user u on g.openid=u.openid where l.type !=0 order by l.ctime";
		$list = $this->model->byquery ( 'newdoll_lottery_log', $sql );
		
		// $type = array('1' => '50元携程旅游券', '2' => '100元携程旅游券');
		$jiang = array (
				'1' => '50元携程券',
				'2' => '100元携程券',
				'3' => '时光网电影券',
				'4' => '物美超市电影券' 
		);
		foreach ( $list as $k => $v ) {
			$list [$k] ['type'] = $jiang [$list [$k] ['type']];
			$list [$k] ['nickname'] = $this->filterNickname ( $list [$k] ['nickname'] );
			$list [$k] ['number'] = "\t" . $list [$k] ['number'];
			$list [$k] ['code'] = "\t" . $list [$k] ['code'];
			$list [$k] ['ctime'] = date ( "Y-m-d H:i:s", $list [$k] ['ctime'] );
		}
		$title = "openid,昵称,中奖类型,卡号,密码,时间";
		$this->daochu ( $title, $list );
	}
	// 导出未用的奖券
	public function exportdollSurplus() {
		$sql = "select number,code,type,status from newdoll_gift where status=1";
		$list = $this->model->byquery ( 'newdoll_gift', $sql );
		$arr = array (
				"1" => '50元携程券',
				'2' => '100元携程券',
				'3' => '电影券',
				'4' => '物美超市购物券' 
		);
		$status = array (
				'1' => '未使用',
				'2' => '已使用' 
		);
		foreach ( $list as $k => $v ) {
			$list [$k] ['number'] = "\t" . $list [$k] ['number'];
			$list [$k] ['code'] = "\t" . $list [$k] ['code'];
			$list [$k] ['type'] = $arr [$list [$k] ['type']];
			$list [$k] ['status'] = $status [$list [$k] ['status']];
		}
		$title = "号码,密码,类型,状态";
		$this->daochu ( $title, $list );
	}
	
	// 导出si大转盘中实体券信息
	public function exportsiinfo() {
		$sql = "select l.openid,l.type,l.name,l.address,l.mobile,l.ctime,u.nickname,g.number,g.`code` from si_lottery_log l LEFT JOIN si_user u on l.openid=u.openid LEFT JOIN si_gift g on u.openid=g.openid where l.type != 0 group by l.openid ORDER BY l.ctime asc,l.type asc";
		$list = $this->model->byquery ( 'si_lottery_log', $sql );
		// 1时光网电影票 2麦当劳代金券 3物美电子券 4星巴克咖啡券 5好利来蛋糕券
		$type = array (
				'1' => '时光网电影票',
				'2' => '麦当劳代金券',
				'3' => '物美电子券',
				'4' => '星巴克咖啡券',
				'5' => '好利来蛋糕券' 
		);
		foreach ( $list as $k => $v ) {
			$list [$k] ['type'] = $type [$list [$k] ['type']];
			$list [$k] ['number'] = "\t" . $list [$k] ['number'];
			$list [$k] ['nickname'] = $this->filterNickname ( $list [$k] ['nickname'] );
			$list [$k] ['ctime'] = date ( "Y-m-d H:i:s", $list [$k] ['ctime'] );
		}
		$title = "openid,类型,姓名,地址,手机,时间,昵称,卡号,密码";
		$this->daochu ( $title, $list );
	}
	public function export() {
		$type = array (
				'1' => '咖啡券',
				'2' => '电影券',
				'3' => '京东券',
				'4' => '旅游券',
				'5' => '只能手环' 
		);
		$day = strtotime ( date ( 'Y-m-d' ), time () );
		$atime = $day;
		$etime = $day + 3600 * 24 - 1;
		$sql = "select l.openid,l.type,l.name,l.address,l.mobile,u.nickname,g.number,g.`code` from edison_lottery_log l LEFT JOIN edison_user u on l.openid=u.openid LEFT JOIN edison_gift g on u.openid=g.openid where l.type != 0 and l.ctime > {$atime} and l.ctime < {$etime}  ORDER BY l.type";
		$info = $this->model->byquery ( 'edison_lottery_log', $sql );
		
		foreach ( $info as $k => $v ) {
			$info [$k] ['type'] = $type [$info [$k] ['type']];
			$info [$k] ['nickname'] = $this->filterNickname ( $info [$k] ['nickname'] );
			$info [$k] ['number'] = "\t" . $info [$k] ['number'];
		}
		$title = "openid,类型,姓名,地址,手机,昵称,卡号,密码";
		$this->daochu ( $title, $info );
	}
	public function daochu($title, $info) {
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', "-1" );
		// error_reporting(0); //屏蔽提示信息
		$name = date ( "Y-m-d", time () );
		header ( "Content-type:application/vnd.ms-excel" );
		header ( "Content-Disposition:filename={$name}.csv" );
		
		$arr = explode ( ",", $title );
		$str = implode ( ",", $arr );
		echo iconv ( 'UTF-8', 'GBK', $str ) . "\r";
		foreach ( $info as $v ) {
			echo iconv ( "UTF-8", 'GBK', implode ( ",", $v ) ) . "\r";
		}
	}
	public function filterNickname($str) {
		$arr = array ();
		$pattern = '/([a-zA-Z0-9\x{4e00}-\x{9fa5}])+/u';
		preg_match_all ( $pattern, $str, $arr );
		$res = implode ( '-', $arr [0] );
		return $res;
	}
}

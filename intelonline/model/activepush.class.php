<?php
/**
 * 自动应答model
 * 
 * @author wangying
 * @copyright zevertech
 * 2014/06/12
 */
class activepush extends Module {
	private $M;
	public function __construct($platform = 'default') {
		// 设定平台调用的数据库配置项
		$this->platform = $platform;
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		// 连接数据库
		$this->connect ();
		$this->M = new wechat ();
	}
	/**
	 * 查询用户分组
	 * 
	 * @return Ambigous <multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	public function groupmsg() {
		$sql = "select id,name from sys_membergroup where status = '1'";
		return $this->query ( $sql );
	}
	/**
	 * 找出所有符合此分组的粉丝
	 * 
	 * @param unknown $group_id        	
	 * @return unknown
	 */
	public function conformfans($group_id, $param = '', $tid = '', $fan = '') {
		$fans_where = " m.subscribe=1 ";
		$join = '';
		// 用户分组条件
		if ($group_id) {
			$join .= " RIGHT JOIN sys_mem_group_relation as g ON m.openid = g.mopenid ";
			$fans_where .= " AND g.groupid = {$group_id} ";
		}
		// 用户标签条件
		if ($tid) {
			$tidStr = substr ( $tid, 0, - 1 );
			$join .= " RIGHT JOIN sys_mem_tag_relation as t ON m.openid = t.mopenid ";
			$fans_where .= " AND t.tagid IN ({$tidStr}) ";
		}
		
		if (is_array ( $param )) {
			$p = $param ['p'];
			$pagesize = $param ['pagesize'];
			$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
			if ($param ['keyword']) {
				$fans_where .= " and m.nickname like '%" . $param ['keyword'] . "%' ";
			}
		} else {
			$limit = '';
		}
		$total_sql = "select count(distinct(m.openid)) as count from sys_member as m {$join} where {$fans_where}";
		$res_total = $this->db->query ( $total_sql );
		
		$fans_sql = "select m.* from sys_member as m {$join} where {$fans_where} group by m.openid order by id asc {$limit}";
		// dump($fans_sql);die;
		$res_fans = $this->query ( $fans_sql );
		foreach ( $res_fans as &$r ) {
			$sql = "select count(*) as num from sys_get_msg where fromusername ='" . $r ['openid'] . "'";
			$jh = $this->query ( $sql );
			$r ['jh'] = $jh [0] ['num'];
		}
		$res_fans ['total'] = ( int ) $res_total [0] ['count'];
		return $res_fans;
	}
	/**
	 * 找出所有符合此分组的粉丝
	 * 
	 * @param unknown $group_id        	
	 * @return unknown
	 */
	// public function conformfans($group_id,$param='',$tid='',$fan=''){
	// $fans_where = ' ((1';
	// $fans_where .= " and subscribe=1";
	// //用户分组条件
	// if($group_id !=0){
	// $sql_group = "select * from sys_member_group where id={$group_id} limit 1";
	// $res_group = $this->query($sql_group);
	// $group = $res_group[0];
	//
	// //$fan == "fan" 设置 用户分组和用户标签的反向选择条件
	// if($fan=="fan"){
	// //性别,来源,类型,地域,等级,最小交互,最大交互,排除openid,增加openid
	// !empty($group['condition_sex']) && $fans_where .= " and sex!={$group['condition_sex']}";
	// !empty($group['condition_source']) && $fans_where .= " and sid not in({$group['condition_source']})";
	// !empty($group['condition_type']) && $fans_where .= " and type not in({$group['condition_type']})";
	// !empty($group['condition_city']) && $fans_where .= " and city!='{$group['condition_city']}'";
	// !empty($group['condition_province']) && $fans_where .= " and province!='{$group['condition_province']}'";
	// //!empty($group['condition_level']) && $fans_where .= " and level={$group['condition_level']}";
	// !empty($group['condition_cross_min']) && $fans_where .= " and ( interactive<{$group['condition_cross_min']}";
	// !empty($group['condition_cross_max']) && $fans_where .= " or interactive>{$group['condition_cross_max']} )";
	// if(!empty($group['condition_added']) && $group['condition_added']){
	// $add = explode(',',$group['condition_added']);
	// foreach($add as &$val){
	// $val = "'{$val}'";
	// }
	// $add = implode(',', $add);
	// $fans_where .= " and openid not in({$add})";
	// }
	// if(!empty($group['condition_tags']) && $group['condition_tags']){
	// $tag = explode(',',$group['condition_tags']);
	// foreach($tag as $val){
	// $sql = "select openid from `sys_tags_member` WHERE tag_id=".$val;
	// $tmp = $this->db->query($sql);
	// foreach($tmp as $k=>$v){
	// $tagFansArr[] = $v['openid'];
	// }
	// }
	//
	// $FansArr = array_flip(array_flip($tagFansArr));
	// foreach($FansArr as $key=>$val){
	// $FansArr[$key] = "'".$val."'";
	// }
	// $FansStr = implode(",", $FansArr);
	// $fans_where .= " and openid not in(".$FansStr.")";
	// }
	// $fans_where .= ")";
	// if(!empty($group['condition_exclude']) && $group['condition_exclude']){
	// $exclude = explode(',',$group['condition_exclude']);
	// foreach($exclude as &$val){
	// $val = "'{$val}'";
	// }
	// $exclude = implode(',', $exclude);
	// $fans_where .= " or openid in({$exclude})";
	// }
	// $fans_where .= ")";
	// }else{
	// //性别,来源,类型,地域,等级,最小交互,最大交互,排除openid,增加openid
	// !empty($group['condition_sex']) && $fans_where .= " and sex={$group['condition_sex']}";
	// !empty($group['condition_source']) && $fans_where .= " and sid in({$group['condition_source']})";
	// !empty($group['condition_type']) && $fans_where .= " and type in({$group['condition_type']})";
	// !empty($group['condition_city']) && $fans_where .= " and city='{$group['condition_city']}'";
	// !empty($group['condition_province']) && $fans_where .= " and province='{$group['condition_province']}'";
	// //!empty($group['condition_level']) && $fans_where .= " and level={$group['condition_level']}";
	// !empty($group['condition_cross_min']) && $fans_where .= " and interactive>={$group['condition_cross_min']}";
	// !empty($group['condition_cross_max']) && $fans_where .= " and interactive<={$group['condition_cross_max']}";
	// if(!empty($group['condition_exclude']) && $group['condition_exclude']){
	// $exclude = explode(',',$group['condition_exclude']);
	// foreach($exclude as &$val){
	// $val = "'{$val}'";
	// }
	// $exclude = implode(',', $exclude);
	// $fans_where .= " and openid not in({$exclude})";
	// }
	// $fans_where .= ")";
	// if(!empty($group['condition_added']) && $group['condition_added']){
	// $add = explode(',',$group['condition_added']);
	// foreach($add as &$val){
	// $val = "'{$val}'";
	// }
	// $add = implode(',', $add);
	// $fans_where .= " or openid in({$add})";
	// }
	//
	// if(!empty($group['condition_tags']) && $group['condition_tags']){
	// $tag = explode(',',$group['condition_tags']);
	// foreach($tag as $val){
	// $sql = "select openid from `sys_tags_member` WHERE tag_id=".$val;
	// $tmp = $this->db->query($sql);
	// foreach($tmp as $k=>$v){
	// $tagFansArr[] = $v['openid'];
	// }
	// }
	//
	// $FansArr = array_flip(array_flip($tagFansArr));
	// foreach($FansArr as $key=>$val){
	// $FansArr[$key] = "'".$val."'";
	// }
	// $FansStr = implode(",", $FansArr);
	// $fans_where .= " and openid in(".$FansStr.")";
	// }
	// $fans_where .= ")";
	// }
	//
	// }else{
	// $fans_where .= "))";
	// }
	// //用户标签条件
	// if(!empty($tid)){
	// $tidStr = substr($tid,0,-1);
	// $tidArr = explode(",", $tidStr);
	// foreach($tidArr as $key=>$val){
	// $sql = "select mopenid from `sys_mem_tag_relation` WHERE tagid=".$val;
	// $tmp = $this->db->query($sql);
	// foreach($tmp as $k=>$v){
	// $tagFansArr[] = $v['mopenid'];
	// }
	// }
	// $FansArr = array_flip(array_flip($tagFansArr));
	// foreach($FansArr as $key=>$val){
	// $FansArr[$key] = "'".$val."'";
	// }
	// $FansStr = implode(",", $FansArr);
	//
	// if($group_id !=0){
	// $sql = "select condition from `sys_membergroup` WHERE id=".$group_id;
	// //$sql = "select condition_tag from `sys_member_group` WHERE id=".$group_id;
	// $groupArr = $this->db->query($sql);
	// $groupTagStr = $groupArr[0]['condition_tag'];
	// if($groupTagStr){
	// //如果此组为标签组，则需要与之后 多出的标签 取差集
	// $groupTagArr = explode(",", $groupTagStr);
	// $diffArr = array_diff($tidArr,$groupTagArr);
	// if(!empty($diffArr)){
	// foreach($diffArr as $key=>$val){
	// $sql = "select openid from `sys_tags_member` WHERE tag_id=".$val;
	// $tmp = $this->db->query($sql);
	// foreach($tmp as $k=>$v){
	// $tagFansArr[] = $v['openid'];
	// }
	// }
	// $FansArr = array_flip(array_flip($tagFansArr));
	// foreach($FansArr as $key=>$val){
	// $FansArr[$key] = "'".$val."'";
	// }
	// $FansStr = implode(",", $FansArr);
	// }
	//
	// if($fan=="fan"){
	// $fans_where .= " and openid not in(".$FansStr.")";
	// }
	// }else{
	//
	// if($fan=="fan"){
	// $fans_where .= " and openid not in(".$FansStr.")";
	// }
	// }
	//
	// //$fan == "fan" 设置 用户分组和用户标签的反向选择条件
	// if($fan!="fan"){
	// $fans_where .= " or openid in(".$FansStr.")";
	// }
	//
	// }else{
	// // $fans_where .= "))";
	// $fans_where .= "";//2015.2.9 Harry UPDATE
	// //$fan == "fan" 设置 用户分组和用户标签的反向选择条件
	// if($fan=="fan"){
	// $fans_where .= " and openid not in(".$FansStr.")";
	// }else{
	// $fans_where .= " and openid in(".$FansStr.")";
	// }
	//
	// }
	// }
	//
	// if(is_array($param)){
	// $p = $param['p'];
	// $pagesize = $param['pagesize'];
	// $limit = ' limit ' . ($p-1)*$pagesize .',' . $pagesize;
	// if($param['keyword']){
	// $fans_where .= " and nickname like '%".$param['keyword']."%' ";
	// }
	// }else{
	// $limit = '';
	// }
	// $total_sql = "select count(distinct(openid)) as count from sys_member where {$fans_where}";
	// $res_total = $this->db->query($total_sql);
	//
	// $fans_sql = "select nickname,integration,sex,country,province,city,interactive,type,openid,headimgurl,headimg from sys_member where {$fans_where} group by openid order by id asc {$limit}";
	// //dump($fans_sql);die;
	// $res_fans = $this->query($fans_sql);
	// $res_fans['total'] = (int)$res_total[0]['count'];
	// return $res_fans;
	// }
	/**
	 * 按昵称查找
	 */
	public function according_nickname($nickname, $param) {
		$p = $param ['p'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$sql = "select * from sys_member where nickname like '%{$nickname}%' and subscribe = 1 group by openid {$limit}";
		$res = $this->db->query ( $sql );
		foreach ( $res as &$r ) {
			$sql = "select count(*) as num from sys_get_msg where fromusername ='" . $r ['openid'] . "'";
			$jh = $this->query ( $sql );
			$r ['jh'] = $jh [0] ['num'];
		}
		
		$sql_total = "select count(distinct(openid)) as count from sys_member where nickname like '%{$nickname}%' and subscribe = 1";
		$res_total = $this->db->query ( $sql_total );
		$res ['total'] = $res_total [0] ['count'];
		return $res;
	}
	public function according_nicknameAll($nickname) {
		$sql = "select openid from sys_member where nickname like '%{$nickname}%' and subscribe = 1";
		$res = $this->db->query ( $sql );
		return $res;
	}
	/**
	 * 添加推送消息
	 */
	public function add_activepush($info) {
		$this->db->tableName = "sys_activepush";
		$id = $this->db->insert ( $info );
		if ($info ['active_crontab_time'] == '0000-00-00 00:00:00') {
			$info ['active_type'] == 1 && $this->interaction48 ( $id );
			$info ['active_type'] == 2 && $this->advancedApi ( $id );
		}
		return $id;
	}
	/**
	 * 查找48小时之内交互的用户
	 */
	function LatestIntractUser($openids) {
		$openids = explode ( ',', $openids );
		foreach ( $openids as &$openid ) {
			$openid = "'{$openid}'";
		}
		$openids = implode ( ',', $openids );
		$stime = time () - 3600 * 48;
		$where = " 1 and ctime > {$stime}";
		$sql = "select distinct(fromusername) as openid from sys_weixin_getmsg_new where {$where} and fromusername in({$openids})";
		$result = $this->db->query ( $sql );
		return $result ? $result : array ();
	}
	/**
	 * 高级接口
	 */
	public function advancedApi($activepush_id) {
		$accesstoken = $this->M->getAccessToken ();
		$find_activepush = "select * from sys_activepush where id={$activepush_id} and active_type=2 limit 1";
		$res_activepush = $this->db->query ( $find_activepush );
		$activepush = $res_activepush [0];
		$num = 0;
		// $activepush['active_openids'] = explode(',',$activepush['active_openids']);
		$activepush ['active_openids'] = explode ( ',', $activepush ['active_openids'] );
		$openid_groups = array_chunk ( $activepush ['active_openids'], 9500 );
		
		// 处理高级群发的素材
		if ($activepush ['active_mode'] == 1) {
			
			$pushMessage ['msgtype'] = 'text';
			$pushMessage ['text'] ['content'] = $activepush ['active_content'];
			// $pushMessages = json_encode_zh($pushMessage);
		} else if ($activepush ['active_mode'] == 2) {
			$temp = $this->getMaterialInfo ( $activepush ['active_mid'] );
			foreach ( $temp ['articles'] as $key => $val ) {
				$picUrl = $val ['picUrl'];
				$up = array (
						"media" => "@" . $picUrl 
				);
				// 上传图片
				$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?type=image&access_token=' . $accesstoken;
				$up = json_decode ( curl_file_get_contents ( $url, $up ), TRUE );
				$temp ['articles'] [$key] ['thumb_media_id'] = $up ['media_id'];
				// $temp['articles'][$key]['show_cover_pic'] = $temp['articles'][$key]['coverFlag'];
				// 处理内空图片链接
				$temp ['articles'] [$key] ['content'] = str_replace ( 'src="/upload', 'src="' . APP_PATH . 'upload', $temp ['articles'] [$key] ['content'] );
				$temp ['articles'] [$key] ['content'] = str_replace ( '"', '\"', $temp ['articles'] [$key] ['content'] );
				foreach ( $temp ['articles'] [$key] as $key2 => $val2 ) {
					$temp ['articles'] [$key] [$key2] = urlencode ( $val2 );
				}
				unset ( $temp ['articles'] [$key] ['picUrl'] );
			}
			$content = urldecode ( json_encode ( $temp ) );
			// 上传素材
			$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $accesstoken;
			$returninfo = json_decode ( curl_file_get_contents ( $url, $content ), true );
			
			$pushMessage ['mpnews'] ['media_id'] = $returninfo ['media_id'];
			$pushMessage ['msgtype'] = 'mpnews';
		}
		
		foreach ( $openid_groups as $openids ) {
			// 给消息添加粉丝openid列表
			if (count ( $openids ) < 2) {
				$openids [] = 'omH22t4LU5l40BCZuyxeLa8w3uNw';
			}
			$pushMessage ['touser'] = $openids;
			$pushMessages = json_encode_zh ( $pushMessage );
			
			$sendUrl = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $accesstoken;
			$returninfo = curl_file_get_contents ( $sendUrl, $pushMessages );
			$return = json_decode ( $returninfo, true );
			if ($returninfo ['errcode']) {
				$this->db->tableName = 'sys_activepush';
				$result = $this->update ( array (
						'active_state' => 3 
				), 'id = "' . $activepush ['id'] . '"' );
			}
		}
	}
	public function advancedApi_test($activepush_id) {
		$accesstoken = $this->M->getAccessToken ();
		$find_activepush = "select * from sys_activepush where id={$activepush_id} and active_type=2 limit 1";
		$res_activepush = $this->db->query ( $find_activepush );
		$activepush = $res_activepush [0];
		$num = 0;
		// $activepush['active_openids'] = explode(',',$activepush['active_openids']);
		$activepush ['active_openids'] = explode ( ',', $activepush ['active_openids'] );
		$openid_groups = array_chunk ( $activepush ['active_openids'], 9500 );
		
		// 处理高级群发的素材
		if ($activepush ['active_mode'] == 1) {
			
			$pushMessage ['msgtype'] = 'text';
			$pushMessage ['text'] ['content'] = $activepush ['active_content'];
			// $pushMessages = json_encode_zh($pushMessage);
		} else if ($activepush ['active_mode'] == 2) {
			$temp = $this->getMaterialInfo ( $activepush ['active_mid'] );
			foreach ( $temp ['articles'] as $key => $val ) {
				$picUrl = $val ['picUrl'];
				$up = array (
						"media" => "@" . $picUrl 
				);
				// 上传图片
				
				$temp ['articles'] [$key] ['thumb_media_id'] = '2222';
				// $temp['articles'][$key]['show_cover_pic'] = $temp['articles'][$key]['coverFlag'];
				// 处理内空图片链接
				$temp ['articles'] [$key] ['content'] = str_replace ( 'src="/upload', 'src="' . APP_PATH . 'upload', $temp ['articles'] [$key] ['content'] );
				$temp ['articles'] [$key] ['content'] = str_replace ( '"', '\"', $temp ['articles'] [$key] ['content'] );
				foreach ( $temp ['articles'] [$key] as $key2 => $val2 ) {
					$temp ['articles'] [$key] [$key2] = urlencode ( $val2 );
				}
				unset ( $temp ['articles'] [$key] ['picUrl'] );
			}
			$content = urldecode ( json_encode ( $temp ) );
			// 上传素材
			// $returninfo = json_decode(curl_file_get_contents($url, $content),true);
			$returninfo ['media_id'] = '11111111111';
			$pushMessage ['mpnews'] ['media_id'] = $returninfo ['media_id'];
			$pushMessage ['msgtype'] = 'mpnews';
		}
		
		foreach ( $openid_groups as $openids ) {
			dump ( $pushMessage );
			dump ( $openids );
			dump ( "--------------------------------------" );
		}
	}
	
	/*
	 * 以下是王瀛处理的高级接口消息发送
	 * public function advancedApi($activepush_id){
	 * $accesstoken = $this->M->getAccessToken();
	 * $find_activepush = "select * from sys_activepush where id={$activepush_id} and active_type=2 limit 1";
	 * $res_activepush = $this->db->query($find_activepush);
	 * $activepush = $res_activepush[0];
	 * $num = 0;
	 * //$activepush['active_openids'] = explode(',',$activepush['active_openids']);
	 * $activepush['active_openids'] = explode(',',$activepush['active_openids']);
	 * $openid_groups = array_chunk($activepush['active_openids'],9500);
	 *
	 * foreach($openid_groups as $openids){
	 * if($activepush['active_mode'] == 1){
	 * // $openids = explode(',', $openids);
	 * $pushMessage['touser'] = $openids;
	 * $pushMessage['msgtype'] = 'text';
	 * $pushMessage['text']['content'] = $activepush['active_content'];
	 * $pushMessages = json_encode_zh($pushMessage);
	 * }else if($activepush['active_mode'] == 2){
	 * $temp = $this->getMaterialInfo($activepush['active_mid']);
	 * foreach($temp['articles'] as $key=>$val){
	 * $picUrl = $val['picUrl'];
	 * $up = array("media" => "@".$picUrl);
	 * //上传图片
	 * $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?type=image&access_token=' . $accesstoken;
	 * $up = json_decode(curl_file_get_contents($url,$up),TRUE);
	 * $temp['articles'][$key]['thumb_media_id'] = $up['media_id'];
	 * //$temp['articles'][$key]['show_cover_pic'] = $temp['articles'][$key]['coverFlag'];
	 * //处理内空图片链接
	 * $temp['articles'][$key]['content'] = str_replace('src="/upload', 'src="'.APP_PATH.'upload', $temp['articles'][$key]['content']);
	 * $temp['articles'][$key]['content'] = str_replace('"','\"',$temp['articles'][$key]['content']);
	 * foreach($temp['articles'][$key] as $key2=>$val2){
	 * $temp['articles'][$key][$key2] = urlencode ( $val2 );
	 * }
	 * unset($temp['articles'][$key]['picUrl']);
	 * }
	 * $content = urldecode(json_encode($temp));
	 * //上传素材
	 * $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$accesstoken;
	 * $returninfo = json_decode(curl_file_get_contents($url, $content),true);
	 *
	 * $pushMessage['touser'] = $openids;
	 * $pushMessage['mpnews']['media_id'] = $returninfo['media_id'];
	 * $pushMessage['msgtype'] = 'mpnews';
	 * $pushMessages = json_encode_zh($pushMessage);
	 * }
	 * $sendUrl = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$accesstoken;
	 * $returninfo = curl_file_get_contents($sendUrl, $pushMessages);
	 * $return = json_decode($returninfo,true);
	 * if($returninfo['errcode']){
	 * $this->db->tableName = 'sys_activepush';
	 * $result = $this->update(array('active_state'=>3), 'id = "' . $activepush['id'] . '"');
	 * }
	 * }
	 * }
	 *
	 */
	
	/**
	 * 48小时交互推送
	 */
	public function interaction48($activepush_id) {
		// 取推送消息
		$find_activepush = "select * from sys_activepush where id={$activepush_id} and active_type=1 and active_state!=3 limit 1";
		$res_activepush = $this->db->query ( $find_activepush );
		$activepush = $res_activepush [0];
		if ($activepush) {
			$member = new member ();
			// 取48小时交互的粉丝
			$memberlist = $member->getLatestIntractUser ();
			$wechat = new wechat ();
			// 判断发送类型（文本，素材）
			
			if ($activepush ['active_mode'] == 2 && $memberlist) {
				// 取素材
				$articles = $this->getMaterialInfo ( $activepush ['active_mid'], '48' );
				// 循环发送
				foreach ( $memberlist as $member ) {
					if ((strpos ( $activepush ['active_openids'], $member ['fromusername'] ) !== false) && (strpos ( $activepush ['active_finish_openids'], $member ['fromusername'] ) === false)) {
						$return = $wechat->putMsg ( $member ['fromusername'], 'news', $articles );
						if ($this->add_finish_openid ( $activepush_id, $member ['fromusername'] )) {
							sleep ( ( int ) $activepush ['interval'] );
						}
					}
				}
			} else if ($activepush ['active_mode'] == 1 && $memberlist) {
				foreach ( $memberlist as $member ) {
					if ((strpos ( $activepush ['active_openids'], $member ['fromusername'] ) !== false) && (strpos ( $activepush ['active_finish_openids'], $member ['fromusername'] ) === false)) {
						$content = array (
								'content' => $activepush ['active_content'] 
						);
						$return = $wechat->putMsg ( $member ['fromusername'], 'text', $content );
						if ($this->add_finish_openid ( $activepush_id, $member ['fromusername'] )) {
							sleep ( ( int ) $activepush ['interval'] );
						}
					}
				}
			}
			sleep ( 1 );
			// 修改发送状态
			$find_openids = "select active_openids,active_finish_openids from sys_activepush where id={$activepush['id']} limit 1";
			$res_openids = $this->db->query ( $find_openids );
			$active_openids = explode ( ',', $res_openids [0] ['active_openids'] );
			$active_finish_openids = explode ( ',', $res_openids [0] ['active_finish_openids'] );
			if (count ( $active_openids ) == count ( $active_finish_openids )) {
				$state = array (
						'active_state' => 3 
				);
			} else {
				$state = array (
						'active_state' => 2 
				);
			}
			$this->db->tableName = 'sys_activepush';
			$result = $this->update ( $state, 'id = "' . $activepush_id . '"' );
		}
	}
	/**
	 * 插入已发送的用户
	 */
	public function add_finish_openid($id, $openid) {
		$find_finish = "select active_finish_openids from sys_activepush where id={$id} limit 1";
		$res_finish = $this->db->query ( $find_finish );
		$finish = $res_finish [0] ['active_finish_openids'];
		$openids ['active_finish_openids'] = ! empty ( $finish ) ? $finish . ",{$openid}" : $openid;
		$openids ['active_state'] = 2;
		$this->db->tableName = 'sys_activepush';
		$result = $this->update ( $openids, 'id = "' . $id . '"' );
		return $result;
	}
	/**
	 * 获取素材信息
	 * 
	 * @param number $id        	
	 * @return Ambigous <string, 2>
	 */
	function getMaterialInfo($id = 0, $type = '') {
		$sql = "select * from sys_material where isdel='0' and id = $id";
		$data = $this->db->query ( $sql );
		$result = $data [0];
		if ($result && $result ['type'] == 'news') {
			$sql = "select id,title,description,url,author,thumb as picurl,content,original_url,coverFlag from sys_material_article where mid = $id and isdel='0' order by id asc";
			$tmp = array ();
			$tmp = $this->db->query ( $sql );
			$res = array ();
			if ($tmp) {
				foreach ( $tmp as $k => $v ) {
					if ($type == '48') {
						$res [$k] ['title'] = $v ['title'];
						$res [$k] ['description'] = $v ['description'];
						$res [$k] ['url'] = APP_PATH . 'index.php/article/material_article/id/' . $v ['id'];
						$res [$k] ['picurl'] = rtrim ( APP_PATH, '/' ) . $v ['picurl'];
					} else {
						$res [$k] ['title'] = $v ['title'];
						$res [$k] ['show_cover_pic'] = $v ['coverFlag'];
						$res [$k] ['author'] = $v ['author'];
						$res [$k] ['digest'] = $v ['description'];
						$content = $v ['content'];
						// $content = str_replace('/ueditor/php/upload/', rtrim(APP_PATH,'/').'/ueditor/php/upload/', $content);
						$content = str_replace ( '"/ueditor/php/upload/', '"' . rtrim ( APP_PATH, '/' ) . '/ueditor/php/upload/', $content );
						$res [$k] ['content'] = $content;
						$res [$k] ['picUrl'] = rtrim ( ROOT_PATH, '/' ) . $v ['picurl'];
						$res [$k] ['content_source_url'] = $v ['original_url'];
						unset ( $tmp [$k] ['id'] );
					}
				}
			}
			$return ['articles'] = $res;
		}
		return $return;
	}
	/**
	 * 定时任务
	 */
	public function activepushCrontab() {
		$date = date ( 'Y-m-d' );
		// 矫正服务器上的时间 （前后相差5分钟）
		$beforetime = date ( 'Y-m-d H:i:s', time () - 300 );
		$aftertime = date ( 'Y-m-d H:i:s', time () + 300 );
		// 48小时交互推送---持续时间
		$durationConform = "select * from sys_activepush where active_duration_start<='{$date}' and active_duration_end>='{$date}' and active_state!=3 and active_type=1";
		// dump($durationConform);
		$activepush = $this->db->query ( $durationConform );
		// dump($activepush);die;
		foreach ( $activepush as $val ) {
			$this->interaction48 ( $val ['id'] );
		}
		// 定时任务
		$findCrontab = "select * from sys_activepush where active_crontab_time>='{$beforetime}' and active_crontab_time<='{$aftertime}' and active_state!=3";
		$resCrontab = $this->db->query ( $findCrontab );
		foreach ( $resCrontab as $v ) {
			if ($v ['active_type'] == 1) {
				$this->interaction48 ( $v ['id'] );
			} else if ($v ['active_type'] == 2) {
				$this->advancedApi ( $v ['id'] );
			}
		}
	}
	/**
	 * 列表
	 */
	public function listall($param = null) {
		$p = $param ['p'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$sql = "select * from sys_activepush where 1 order by id desc {$limit}";
		$data = $this->query ( $sql );
		foreach ( $data as &$val ) {
			if ($val ['active_mode'] == 2) {
				$find_pic = "select thumb from sys_material_article where mid={$val['active_mid']} limit 1";
				$res_pic = $this->db->query ( $find_pic );
				$val ['active_content'] = $res_pic [0] ['thumb'];
			}
		}
		$sql_count = "select count(*) as num from `sys_activepush`";
		$tmp = $this->db->query ( $sql_count );
		$total = $tmp [0] ['num'];
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
	public function del_active($id) {
		$sql = "delete from sys_activepush where id={$id} limit 1";
		return $this->db->query ( $sql );
	}
	public function edit_active($id) {
		$find_openid = "select * from sys_activepush where id={$id} limit 1";
		$res_openid = $this->db->query ( $find_openid );
		$data ['msg'] = $res_openid [0];
		$res_openid = str_replace ( ",", "','", $res_openid [0] ['active_openids'] );
		$openids = "'{$res_openid}'";
		$find_members = "select * from sys_member where openid in ({$openids})";
		$data ['members'] = $this->db->query ( $find_members );
		return $data;
	}
	public function edit_active_new($id) {
		$find_openid = "select * from sys_activepush where id={$id} limit 1";
		$res_openid = $this->db->query ( $find_openid );
		$data = $res_openid [0];
		// $res_openid = str_replace(",","','",$res_openid[0]['active_openids']);
		return $data;
	}
	public function getActivePushInfo($where, $single = FALSE) {
		$this->db->tableName = 'sys_activepush';
		if ($single) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
}

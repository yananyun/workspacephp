<?php
/**
 * groupMessageAction.class.php
 *			群发信息
 * 
 * @author:Harry
 * @since:2014.7.3
 * @link:http://haoshengzhide.com/ 
 */
class groupMessageAction extends Action {
	public $M;
	public $G;
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new groupMessage ();
		$this->G = new group ();
	}
	public function list_manage() {
		$this->display ( 'admin/groupMessage/list_manage.html' );
	}
	
	// 获取群发消息列表
	public function groupMsgList() {
		$where = array ();
		$where ['p'] = $_GET ['p'] ? ( int ) $_GET ['p'] : 1;
		$where ['pagesize'] = 10;
		$where ['keyword'] = $_GET ['keyword'] ? strip_tags ( htmlspecialchars ( $_GET ['keyword'] ) ) : '';
		$result = $this->M->getList ( $where );
		$pages = $this->pages ( $result ['total'], $where ['p'], $where ['pagesize'], 10, 'intelListWrap' );
		
		$this->assign ( 'list', $result ['list'] );
		$this->assign ( 'pages', $pages );
		
		$this->display ( 'admin/groupMessage/groupMsgList.html' );
	}
	
	// 新增发送消息
	public function add() {
		$groupList = $this->G->getGroupByWhere ( array (
				'status' => 1 
		) );
		
		$this->assign ( 'groupList', $groupList );
		$this->display ( 'admin/groupMessage/add.html' );
	}
	public function doadd() {
		$info = array ();
		$info ['uid'] = $_SESSION ['userinfo'] ['id'] ? $_SESSION ['userinfo'] ['id'] : 1;
		$info ['gid'] = ( int ) $_POST ['gid']; // 当gid为-1的时候，则发送对象为全体用户。
		if ($_POST ['active_mid']) {
			$info ['type'] = 'news';
			$info ['materialid'] = ( int ) $_POST ['active_mid'];
		} else {
			$info ['type'] = 'text';
			$info ['content'] = filter_input ( INPUT_POST, 'active_content' );
		}
		$info ['mode'] = ( int ) $_POST ['active_type'];
		$info ['ctime'] = time ();
		
		$logId = $this->M->addLog ( $info );
		$this->sent ( $info, $logId );
		redirect ( '/index.php/groupMessage/list_manage' );
	}
	
	// 发送消息操作
	public function sent($sentInfo, $logId) {
		$WECHAT = new wechatAction ();
		$Material = new material ();
		$access_token = $WECHAT->getAccessToken ();
		$API = new WechatApi ( $access_token );
		
		if ($sentInfo ['mode'] == 1) // 48小时交互
{
			// 生成可以发送的消息格式
			switch ($sentInfo ['type']) {
				case 'text' :
					$content = array (
							'content' => $sentInfo ['content'] 
					);
					break;
				case 'news' :
					$temp = $Material->getMaterialInfo ( $sentInfo ['materialid'], 2 );
					$content = $temp ['content'];
					break;
			}
			// 获取48小时内交互过的用户
			$memberArr = $this->G->getMemberListByGid ( $sentInfo ['gid'], 2 );
			// 循环发送
			foreach ( $memberArr as $mem ) {
				$result = $API->putMsg ( $mem ['openid'], $sentInfo ['type'], $content );
			}
			// 更新log的status
			$this->M->updateLogInfo ( array (
					'result' => 2 
			), $logId );
		} else { // 走微信群发接口
			$tmpArr = $this->G->getOpenIdByGid ( $sentInfo ['gid'] );
			$mopenidArr = array ();
			foreach ( $tmpArr as $arr ) {
				if ($sentInfo ['gid'] == '-1') {
					$mopenidArr [] = $arr ['openid'];
				} else {
					$mopenidArr [] = $arr ['mopenid'];
				}
			}
			switch ($sentInfo ['type']) {
				case 'text' :
					$content = array (
							'content' => $sentInfo ['content'] 
					);
					break;
				case 'news' :
					$temp = $Material->getMaterialInfo ( $sentInfo ['materialid'] );
					foreach ( $temp ['articles'] as $key => $val ) {
						$picUrl = explode ( APP_PATH, $val ['picurl'] );
						$up = array (
								"media" => "@" . trim ( $picUrl [1], '/' ) 
						);
						// 上传图片
						$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?type=image&access_token=' . $access_token;
						$up = json_decode ( curl_file_get_contents ( $url, $up ), TRUE );
						$temp ['articles'] [$key] ['thumb_media_id'] = $up ['media_id'];
						$temp ['articles'] [$key] ['show_cover_pic'] = '1';
						$temp ['articles'] [$key] ['content'] = str_replace ( '"', '\"', $temp ['articles'] [$key] ['content'] );
						foreach ( $temp ['articles'] [$key] as $key2 => $val2 ) {
							$temp ['articles'] [$key] [$key2] = urlencode ( $val2 );
						}
						unset ( $temp ['articles'] [$key] ['picUrl'] );
					}
					
					$newsArr = urldecode ( json_encode ( $temp ) );
					// 上传素材
					$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $access_token;
					$returninfo = json_decode ( curl_file_get_contents ( $url, $newsArr ), true );
					$content = array (
							'media_id' => $returninfo ['media_id'] 
					);
					break;
			}
			$result = $API->putGroupMsgByOpenIds ( $mopenidArr, $sentInfo ['type'], $content );
			if ($result ['errcode'] == 0) {
				// 更新log的status
				$this->M->updateLogInfo ( array (
						'result' => 2 
				), $logId );
			} else {
				// 更新log的status
				$this->M->updateLogInfo ( array (
						'result' => 3,
						'remark' => $result ['errmsg'] 
				), $logId );
			}
		}
	}
	
	// 消息 详情
	public function detail() {
		$id = $_GET ['id']; // 单条群发消息的ID
		$where = array ();
		$where ['id'] = $id;
		
		$info = $this->M->selectLog ( $where );
		$info = $info [0];
		
		if ($info ['gid'] == '-1') {
			$gname = '全体成员';
		} else {
			$Group = new group ();
			$groupInfo = $Group->getGroupByWhere ( array (
					'id' => $info ['gid'] 
			) );
			$gname = $groupInfo [0] ['name'];
		}
		
		if ($info ['materialid'] !== '') {
			$Material = new material ();
			$materialInfo = $Material->getMaterialInfo ( $info ['materialid'] );
			$single = count ( $materialInfo ['content'] ['articles'] );
			
			$this->assign ( 'single', $single );
			$this->assign ( 'material', $materialInfo );
		}
		
		$this->assign ( 'info', $info );
		$this->assign ( 'gname', $gname );
		$this->display ( 'admin/groupMessage/detail.html' );
	}
	
	// 删除消息记录
	public function delete() {
		$where = array ();
		$where ['id'] = ( int ) $_POST ['id'];
		
		$row = array ();
		$row ['status'] = '4';
		$row ['uptime'] = time ();
		
		$return = $this->M->updateLogInfo ( $row, $where );
		if ($return) {
			echo 1;
		} else {
			echo 4;
		}
	}
}
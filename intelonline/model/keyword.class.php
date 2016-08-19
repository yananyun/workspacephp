<?php
/**
 * keyword.class.php
 * 
 * @author:Harry
 * @link:http://haoshengzhide.com
 * @since:2014.1.15 
 */
class keyword extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->db->tableName = 'sys_keyword';
	}
	public function getKeywordList($where = NULL) {
		return $this->select ( NULL, $where );
	}
	public function doadd($info) {
		$sql = "INSERT INTO `sys_keyword`(`keyword`,`content`,`type`,`ctime`) VALUES('{$info['keyword']}','{$info['content']}',{$info['type']},{$info['ctime']});";
		$return = $this->query ( $sql );
		return $return;
	}
	public function doupdate($id, $info) {
		$sql = "UPDATE `sys_keyword` SET `keyword` = '{$info['keyword']}',`content` = '{$info['content']}',`type` = {$info['type']},`ctime` = {$info['ctime']} WHERE id = $id;";
		$return = $this->query ( $sql );
		return $return;
	}
	public function dodelete($id) {
		return $this->delete ( 'id = ' . $id );
	}
	
	/**
	 * 判断文本内容是否满足关键词
	 *
	 * @param varchar $keyword
	 *        	关键词
	 * @param varchar $text
	 *        	msg数据
	 * @param int $type
	 *        	if($type==1){等同适用}else{包含适用}
	 * @return boolean
	 */
	public function checkKeyword($keyword, $text, $type = 1) {
		$state = false;
		if ($type == 2) {
			$return = explode ( $keyword, $text );
			if (isset ( $return [1] )) {
				$state = true;
			}
		}
		if ($keyword == $text) {
			$state = true;
		}
		return $state;
	}
	
	/**
	 * 加入关键词响应日志
	 *
	 * @param varchar $openid        	
	 * @param varchar $keyword        	
	 * @param varchar $content        	
	 */
	public function mkKwLog($openid, $keyword, $content) {
		$info = array ();
		$info ['openid'] = $openid;
		$info ['keyword'] = $keyword;
		$info ['content'] = $content;
		$info ['ctime'] = time ();
		
		$this->db->tableName = 'sys_keyword_log';
		$this->insert ( $info );
	}
}
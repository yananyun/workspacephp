<?php
/**
 * 会员地区模型类
 * @author zever
 *
 */
class location extends Module {
	public function __construct($platform = 'default') {
		// 设定平台调用的数据库配置项
		$this->platform = $platform;
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		// 连接数据库
		$this->connect ();
	}
	public function getList($fid = 0) {
		$this->tableName = 'location';
		$result = $this->select ( '*', array (
				'fid' => $fid 
		) );
		return $result ? $result : array ();
	}
	public function provinceList() {
		return array (
				'上海' => '1',
				'云南' => '2',
				'内蒙古' => '3',
				'北京' => '4',
				'台湾' => '5',
				'吉林' => '6',
				'四川' => '7',
				'天津' => '8',
				'宁夏' => '9',
				'安徽' => '10',
				'山东' => '11',
				'山西' => '12',
				'广东' => '13',
				'广西' => '14',
				'新疆' => '15',
				'江苏' => '16',
				'江西' => '17',
				'河北' => '18',
				'河南' => '19',
				'浙江' => '20',
				'海南' => '21',
				'湖北' => '22',
				'湖南' => '23',
				'澳门' => '24',
				'甘肃' => '25',
				'福建' => '26',
				'西藏' => '27',
				'贵州' => '28',
				'辽宁' => '29',
				'重庆' => '30',
				'陕西' => '31',
				'青海' => '32',
				'香港' => '33',
				'黑龙江' => '34' 
		);
	}
	public function getMemInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'sys_member';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
}
<?php

/**
 * 产品数据库操作
 */
class products extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	
	/**
	 * 获取产品列表
	 */
	public function getList($where, $limit) {
		$this->tableName = 'products';
		$res ['data'] = $this->select ( '', $where, '', 'id desc', $limit );
		$res ['total'] = $this->count ( $where );
		return $res;
	}
	
	/**
	 * 添加产品
	 * 
	 * @param array $data
	 *        	插入数据的信息
	 * @param array $price
	 *        	版本信息
	 */
	public function addInfo($data) {
		$this->tableName = 'products';
		$data ['ctime'] = time ();
		$res = $this->insert ( $data );
		// if($res){//插入价格表
		// $times = time();
		// foreach($price as $v){
		// $values.="('{$res}','{$v['versionid']}','{$v['oldprice']}','{$v['nowprice']}','{$times}'),";
		// }
		// $sql = "insert into ".TABLE_PREFIX."price ( `pid`,`versionid`,`oldprice`,`nowprice`,`ctime`) values ".trim($values,',');
		// $this->query($sql);
		// }
		return $res;
	}
	
	/**
	 * 获取单个产品的信息
	 * @int $pid 产品的id
	 */
	public function getInfo($pid) {
		$this->tableName = 'products';
		// "'id' = ".$pid." AND 'status' != '4'"
		$res = $this->first ( "`id` = " . $pid . " AND `status` != '4'" );
		return $res;
	}
	
	/**
	 * 判断产品的product_id的唯一性
	 * 
	 * @param
	 *        	product_id
	 */
	public function checkProductId($product_id, $id = NULL) {
		$this->tableName = 'products';
		// "'id' = ".$pid." AND 'status' != '4'"
		if ($id) {
			$res = $this->first ( "`product_id` = " . $product_id . " AND `id` != $id AND `status` != '4'" );
		} else {
			$res = $this->first ( "`product_id` = " . $product_id . " AND `status` != '4'" );
		}
		return $res ? true : false;
	}
	
	/**
	 * 修改产品信息
	 */
	public function editInfo($data) {
		$this->tableName = 'products';
		$data ['data'] ['uptime'] = time ();
		$res = $this->update ( $data ['data'], array (
				'id' => $data ['pid'] 
		) );
		// echo $this->getSql();
		return $res;
	}
	
	/**
	 * 删除产品
	 */
	public function delInfo($pid) {
		$this->tableName = 'products';
		$res = $this->update ( array (
				'status' => '4' 
		), array (
				'id' => $pid 
		) );
		return $res;
	}
	
	/**
	 * 获取订单列表
	 */
	public function getOrderPersonal($data) {
		$this->tableName = 'orders_personal';
		$res = $this->select ( '', $data );
		return $res;
	}
	
	/**
	 * 获取订单列表或详情
	 */
	public function getOrderCompany($data) {
		$this->tableName = 'orders_company';
		if ($data ['id']) {
			$res = $this->getOne ( '', $data );
		} else {
			$res = $this->select ( '', $data );
		}
		return $res;
	}
	public function getInfos($where) {
		$this->tableName = 'products';
		$return = $this->select ( NULL, $where );
		return $return;
	}
	public function closeOrder() {
		$where = array (
				'pay_status' => '1',
				'status' => '1' 
		);
		$list = $this->getOrderPersonal ( $where );
		
		$this->tableName = 'orders_personal';
		$time = time ();
		foreach ( $list as $t ) {
			if (($time - $t ['ctime']) > 86400) {
				$this->update ( array (
						'pay_status' => '4',
						'uptime' => $time 
				), array (
						'id' => $t ['id'] 
				) );
			}
		}
	}
}
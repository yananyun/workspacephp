<?php

/**
 * statistical.class.php
 * 		运营概况，统计分析
 * 
 * @author Harry
 * @link http://haoshengzhide.com 
 * @since 2014.7.24
 */
class statistical extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	
	/**
	 * 订单及消息统计
	 * 
	 * @author wgs <gaoshang_s@163.com>
	 */
	public function orderStatistics() {
		$yes_date = date ( 'Y-m-d', strtotime ( '-1 day' ) ); // 昨天
		                                                // $yes_date = date('Y-m-d', time());//测试今天
		$yes_time = strtotime ( $yes_date ); // 昨天
		                                  // $yes_time = strtotime($yes_date); //测试今天
		$day_time = strtotime ( date ( 'Y-m-d', time () ) ); // 今天
		                                              // $day_time = strtotime(date('Y-m-d',strtotime('+1 day')));//测试明天
		
		$year = date ( 'Y', $yes_time ); // 前一天年份
		$month = date ( 'm', $yes_time ); // 前一天月份
		$day = ( int ) date ( 'd', $yes_time ); // 前一天
		
		$statis_table = 'statistical_log';
		$where_ctime = "ctime > {$yes_time} AND ctime <= {$day_time}";
		$where_arr = array (
				1 => array (
						'tablename' => 'get_msg',
						'where' => $where_ctime 
				),
				6 => array (
						'tablename' => 'orders_personal',
						'where' => "status = '1' AND " . $where_ctime 
				),
				7 => array (
						'tablename' => 'orders_company',
						'where' => $where_ctime 
				) 
		);
		$total_num = 0;
		foreach ( $where_arr as $type => $where ) {
			$where_str = $where ['where'] ? " where " . $where ['where'] : '';
			$count_sql = "select count(*) as cnt from " . gtn ( $where ['tablename'] ) . $where_str;
			$res = $this->query ( $count_sql );
			$total_res = $res [0] ['cnt'] ? $res [0] ['cnt'] : 0;
			if ($type != 1) {
				$total_num += $total_res;
			}
			$this->tableName = $statis_table;
			$statis_res = $this->getOne ( '', array (
					'type' => $type,
					'year' => $year,
					'month' => $month,
					'day' => $day 
			) );
			if ($statis_res) {
				$res_id = $this->update ( array (
						'total' => $total_res,
						'uptime' => time () 
				), array (
						'id' => $statis_res ['id'] 
				) );
				$res_id ? p ( '统计类型为：' . $type . '的数据修改完成，共统计数量为：' . $total_res . '入库时间为：' . $yes_date ) : p ( '统计类型为：' . $type . '的数据修改失败' );
			} else {
				$res_id = $this->insert ( array (
						'total' => $total_res,
						'type' => $type,
						'year' => $year,
						'month' => $month,
						'day' => $day,
						'ctime' => time (),
						'uptime' => time () 
				) );
				$res_id ? p ( '统计类型为：' . $type . '的数据添加完成，共统计数量为：' . $total_res . '入库时间为：' . $yes_date ) : p ( '统计类型为：' . $type . '的数据添加失败' );
			}
		}
		$this->tableName = $statis_table;
		$statis_res = $this->getOne ( '', array (
				'type' => '5',
				'year' => $year,
				'month' => $month,
				'day' => $day 
		) );
		if ($statis_res) {
			$res_id = $this->update ( array (
					'total' => $total_num,
					'uptime' => time () 
			), array (
					'id' => $statis_res ['id'] 
			) );
			$res_id ? p ( '统计类型为：5 的数据修改完成，共统计数量为：' . $total_num . '入库时间为：' . $yes_date ) : p ( '统计类型为：' . $type . '的数据修改失败' );
		} else {
			$res_id = $this->insert ( array (
					'total' => $total_num,
					'type' => '5',
					'year' => $year,
					'month' => $month,
					'day' => $day,
					'ctime' => time (),
					'uptime' => time () 
			) );
			$res_id ? p ( '统计类型为：5 的数据添加完成，共统计数量为：' . $total_num . '入库时间为：' . $yes_date ) : p ( '统计类型为：' . $type . '的数据添加失败' );
		}
	}
	
	/**
	 * 获取统计数据
	 * 
	 * @param mix $data_type：statistical_log表中的type，若为数组则为该字段的集合        	
	 * @param
	 *        	day 显示多少天内的数据 默认显示10天的数据 暂时没用
	 * @return
	 *
	 */
	public function getChartData($data_type, $day = 10) {
		// 获取昨天的时间
		$yes_year = date ( 'Y', strtotime ( '-1 day' ) );
		$yes_month = date ( 'm', strtotime ( '-1 day' ) );
		$where = "year = '{$yes_year}' AND month = '{$yes_month}'";
		$where_type = '';
		if (is_array ( $data_type )) {
			foreach ( $data_type as $type_v ) {
				$where_type .= " type = '" . $type_v . "' or ";
			}
			$where_type = "(" . trim ( $where_type, 'or ' ) . ")";
		} else {
			$where_type = " type = '" . $data_type . "'";
		}
		$where .= " AND " . $where_type;
		$sql = "SELECT * FROM " . gtn ( 'statistical_log' ) . " WHERE " . $where . " ORDER BY ctime ASC";
		$query_res = $this->query ( $sql );
		// echo $this->getSql();
		// dump($query_res);
		return $query_res;
	}
}

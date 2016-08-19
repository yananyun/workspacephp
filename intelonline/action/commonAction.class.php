<?php

/**
 * 通用控制器类
 *
 */
class commonAction extends Action {
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	function index() {
	}
	
	/**
	 * 获取店面地区级联
	 */
	public function region_list() {
		$type = isset ( $_POST ['type'] ) && ! empty ( $_POST ['type'] ) ? intval ( $_POST ['type'] ) : 0;
		$single = isset ( $_POST ['single'] ) && ! empty ( $_POST ['single'] ) ? intval ( $_POST ['single'] ) : 0;
		$key = isset ( $_POST ['key'] ) && ! empty ( $_POST ['key'] ) ? trim ( $_POST ['key'] ) : 0;
		$key = urldecode ( $key );
		$data = regionList ( $type, $key, null, $single );
		ajaxReturn ( $data, '', 1 );
	}
	
	/**
	 * 会员省市级联
	 */
	public function location_list() {
		$fid = isset ( $_POST ['fid'] ) && ! empty ( $_POST ['fid'] ) ? intval ( $_POST ['fid'] ) : 0;
		$key = urldecode ( $key );
		$data = locationList ( $fid );
		ajaxReturn ( $data, '', 1 );
	}
	
	/**
	 * js查找城市
	 */
	function getCity() {
		$city = $_POST ['city'];
		$store = new store ();
		if ($city) {
			$result = $store->getCity ( $city );
			foreach ( $result as $val ) {
				$str .= '<option value="' . $val ['city'] . '">' . $val ['city'] . '</option>';
			}
		} else {
			$str .= '<option value="0">选择市区</option>';
		}
		echo $str;
	}
	function getStore() {
		$city = $_POST ['city'];
		$type = isset ( $_POST ['type'] ) && ! empty ( $_POST ['type'] ) ? intval ( $_POST ['type'] ) : 0;
		$store = new store ();
		if ($city) {
			$result = $store->getStore ( $city, $type );
			foreach ( $result as $val ) {
				$str .= '<option value="' . $val ['stor_id'] . '" title="' . ($val ['mall_nm'] ? $val ['mall_nm'] : '独立店铺') . '">' . $val ['stor_nm'] . '</option>';
			}
		} else {
			$str .= '<option value="0">选择店面</option>';
		}
		echo $str;
	}
	
	/**
	 * js查找店面
	 */
	function getStoreData() {
		$store = $_POST ['store'];
		$storeDb = new store ();
		$result = $storeDb->getStore ( $store );
		$this->assign ( array (
				'list' => $result 
		) );
		$this->display ( 'tpl/find_store_block.html' );
	}
	
	/**
	 * js查找店面
	 */
	function getStoreDataNew() {
		$city = isset ( $_POST ['city'] ) ? $_POST ['city'] : '';
		$name = isset ( $_POST ['name'] ) ? $_POST ['name'] : '';
		$type = isset ( $_POST ['type'] ) ? $_POST ['type'] : '';
		$storeDb = new store ();
		$result = $storeDb->getStoreByCityAndName ( $city, $name, $type );
		$this->assign ( array (
				'list' => $result 
		) );
		$this->display ( 'tpl/find_store_block.html' );
	}
	
	/**
	 * js查找店面
	 */
	function getBestStoreDataNew() {
		$city = isset ( $_POST ['city'] ) ? $_POST ['city'] : '';
		$name = isset ( $_POST ['name'] ) ? $_POST ['name'] : '';
		$storeDb = new store ();
		$result = $storeDb->getStoreBestByCityAndName ( $city, $name );
		$this->assign ( array (
				'list' => $result 
		) );
		$this->display ( 'tpl/find_store_block.html' );
	}
	
	/**
	 * 通用图片上传
	 */
	function upload() {
		$width = isset ( $_GET ['w'] ) ? intval ( $_GET ['w'] ) : 200;
		$height = isset ( $_GET ['h'] ) ? intval ( $_GET ['h'] ) : 150;
		$upload = new UploadFile ();
		
		$upload->savePath = ROOT_PATH . 'uploads/orginal/';
		$upload->thumbPath = ROOT_PATH . 'uploads/thumbnail/';
		
		$upload->thumb = true;
		$upload->thumbMaxHeight = $height;
		$upload->thumbMaxWidth = $width;
		$upload->upload ();
		$result = $upload->getUploadFileInfo ();
		if ($result [0]) {
			
			$info ['orginal'] = '/uploads/orginal/' . $result [0] ['savename'];
			$info ['thumbnail'] = '/uploads/thumbnail/' . $upload->thumbPrefix . $result [0] ['savename'];
			$result = $info ['orginal'];
			ajaxReturn ( $result, '', 1 );
		} else {
			ajaxReturn ( 'error', 'false', 0 );
		}
	}
	function uploadFile() {
		$upload = new UploadFile ();
		$upload->upload ();
		$result = $upload->getUploadFileInfo ();
		if ($result [0]) {
			$path = $result [0] ['orginal'];
			ajaxReturn ( $path, '上传成功', 1 );
		} else {
			ajaxReturn ( 'error', 'false', 0 );
		}
	}
}
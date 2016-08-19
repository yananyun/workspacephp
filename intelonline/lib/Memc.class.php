<?php
/**
 * Memcache操作类，主要用于memcache服务器上的缓存数据的添加、查询
 * 
 * @author shencangsong
 */
/**
 * 类库使用demo示例
 * require 'lib/Memc.class.php';
 * $memcache_config=array(
 * //'key'=>array('host'=>'','port'=>''), 前缀 服务器，端口号
 * 'default'=>array('host'=>'localhost','port'=>'11211'),
 * 'cn'=>array('host'=>'localhost','port'=>'11211'),
 * 'en'=>array('host'=>'localhost','port'=>'11211'),
 * );
 * $memc=new Memc($memcache_config);
 * $memc->set('cn_abc','111111111111111111');
 * $memc->set('en_abc','222222222222222222');
 * $result=$memc->get('cn_abc');
 * var_dump($result);
 * $result0=$memc->getMulti(array('cn_abc','en_abc'));
 * var_dump($result0);
 * $memc->remove('en_abc');
 * $result0=$memc->getMulti(array('cn_abc','en_abc'));
 * var_dump($result0);
 * //$memc->clear();
 * //$memc->clear('cn');
 * //$memc->clearAll();
 * $memc->remove('en_abc');
 * $result0=$memc->getMulti(array('cn_abc','en_abc'));
 * var_dump($result0);
 */
class Memc {
	private $memcache = null; // memcache实例
	private $servers = null; // 服务器列表
	private $single = true; // 单服务器标识
	private $prefix = ''; // 当前所用的服务器索引前缀
	private $expire = MEMCACHE_EXPIRE;
	
	/**
	 * 构造函数
	 *
	 * @param array $servers
	 *        	服务器数组 array('default'=>array('host'=>'localhost','port'=>'11211'),......)
	 */
	function __construct($platform = 'default') {
		global $memcConfig;
		$this->servers = $memcConfig [$platform];
		if ($this->servers == null) {
			// die("您尚未配置可用的Memcache服务器");
			return false;
		}
		// 判断是单服务器，还是多服务器
		if (count ( $this->servers ) > 1) {
			$this->single = false;
		}
		
		// 实例化一个Memcache
		$this->memcache = new Memcache ();
		// 验证所有的服务器列是否可用
		foreach ( $this->servers as $server ) {
			@$flag = $this->memcache->pconnect ( $server ['host'], $server ['port'] );
			if (! $flag) {
				// die("服务器 ".$server['host']."（端口：".$server['port']."）连接失败！");
				return false;
			}
			$this->memcache->close ();
		}
	}
	
	/**
	 * 获取memcache的连接
	 *
	 * @param string $key
	 *        	键值
	 */
	function connect($key) {
		$server = array ();
		// 默认的服务器
		if (array_key_exists ( 'default', $this->servers )) {
			$server = @$this->servers ['default'];
			$this->prefix = 'default';
		} else {
			$server = @$this->servers [0];
			$keyArr = array_keys ( $this->servers );
			$this->prefix = $keyArr [0];
		}
		
		// 若是多服务器，则连接对应的服务器
		if (! $this->single) {
			$this->prefix = strstr ( $key, '_', true );
			if (array_key_exists ( $this->prefix, $this->servers )) {
				$server = @$this->servers [$this->prefix];
			}
		}
		
		$this->memcache->pconnect ( $server ['host'], $server ['port'] );
	}
	
	/**
	 * 获取单个键值的缓存数据
	 *
	 * @param string $key
	 *        	键值
	 * @return string
	 */
	function get($key) {
		$this->connect ( $key );
		$returnValue = $this->memcache->get ( $key );
		return $returnValue;
	}
	
	/**
	 * 获取多个键值的缓存数据
	 *
	 * @param array $key
	 *        	键值数组
	 * @return array:NULL
	 */
	function getMulti($key) {
		$data = array ();
		if (! is_array ( $key )) {
			$data [$key] = $this->get ( $key );
		} else {
			foreach ( $key as $v ) {
				$data [$v] = $this->get ( $v );
			}
		}
		return $data;
	}
	
	/**
	 * 设置缓存数据
	 *
	 * @param string $key
	 *        	要设置值的key。
	 * @param string $value
	 *        	要存储的值，字符串和数值直接存储，其他类型序列化后存储。
	 * @param bool $flag
	 *        	使用MEMCACHE_COMPRESSED指定对值进行压缩(使用zlib)。
	 * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE.
	 */
	function set($key, $value, $flag = false) {
		$this->connect ( $key );
		$returnValue = false;
		$returnValue = $this->memcache->set ( $key, $value, $flag, $this->expire );
		return $returnValue;
	}
	
	/**
	 * 移除指定键值的缓存数据
	 *
	 * @param string $key
	 *        	要移除的键值
	 * @param int $expire
	 *        	延迟移除时间，0为立刻移除
	 */
	function remove($key, $expire = 0) {
		$this->connect ( $key );
		$this->memcache->delete ( $key, $expire );
	}
	
	/**
	 * 根所键值前缀清除某一个缓存服务器上的数据
	 *
	 * @param string $keyPrefix
	 *        	键值前缀
	 */
	function clear($keyPrefix = null) {
		if ($keyPrefix != null) {
			$this->connect ( $keyPrefix . '_' );
		} else {
			$this->connect ( $this->prefix . '_' );
		}
		$this->memcache->flush ();
	}
	
	/**
	 * 清除所有缓存服务器上的数据
	 */
	function clearAll() {
		foreach ( $this->servers as $key => $v ) {
			$this->clear ( $key );
		}
	}
	
	/**
	 * 回收资源
	 */
	function __destruct() {
		if ($this->memcache) {
			$this->memcache->close ();
		}
	}
}
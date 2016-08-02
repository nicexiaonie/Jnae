<?php


namespace Library;
use Core\Config;

class Cache{

	public static $instance;
	private $drive;

	public function __construct($CacheType){
		$class = '\\Library\\Cache\\'.$CacheType;
		if(empty(self::$instance[md5($class)]))
			self::$instance[md5($class)] = new $class();

		$this->drive = self::$instance[md5($class)];
	}


	/**
	 * 	Instance
	 * 	获取对应实例，
	 *
	 * 	param string $CacheType	缓存类型
	 */
	public static function Instance($CacheType = null){
		$cache_config = Config::get('cache');
		if($CacheType === null) $CacheType = $cache_config['type'];
		$cache = new Cache($CacheType);
		return $cache;
	}

	/**
	 * 	set
	 * 	写入缓存
	 * 	return bool
	 */
	public function set($key,$value,$second = 0){

		$this->write($key,$value,$second);
	}

	/**
	 * 	get
	 * 	获取缓存
	 * 	return;
	 */
	public function get($key){
		return $this->read($key);
	}

	/**
	 * 	delete
	 * 	删除变量
	 * 	return bool
	 */
	public function delete($key){
		$this->drive->delete($key);
	}

	/**
	 * 	clear
	 * 	清楚缓存
	 * 	return bool
	 */
	public function clear(){
		$this->drive->clear();
	}

	/**
	 * 	has
	 * 	判断
	 * 	return bool
	 */
	public function has($key){
		return $this->drive->has($key);
	}

	/**
	 * 	expire
	 * 	设置有效期
	 * 	return bool
	 */
	public function expire($name,$second = 0){
		return $this->drive->expire($name,$second);
	}

	/**
	 * 	ttl
	 * 	获取有效时间
	 * return int
	 */
	public function ttl($name){
		return $this->drive->ttl($name);
	}

	/**
	 * 	write
	 * 	写入变量
	 * 	return bool
	 */
	private function write($name, $content, $second = 0){
		return $this->drive->write($name,$content,$second);
	}

	/**
	 * 	read
	 * 	读取变量
	 * 	return bool
	 */
	private function read($name){
		return $this->drive->read($name);
	}



}
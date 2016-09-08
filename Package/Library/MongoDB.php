<?php

namespace Library;

use Core\Config;

class MongoDB{
	protected static $handler;

	/**
	 * MongoClient 类(实例)
	 * @var array
	 */
	protected static $MongoClient;

	/**
	 * MongoDB 类(实例)
	 * @var array
	 */
	protected static $MongoDB;

	/**
	 * 	配置
	  	host' => '188.188.3.200',
		port' => '27017',
		user' => 'anhaoroot',
		passwd' => 'kele',
		database' => 'anhao',
	 */
	protected static $config  = [];

	/**
	 * 	配置缓存
	 */
	protected static $config_bak;

	/**
	 * 	处理配置信息
	 */
	private function setConfig(){
		if( empty(self::$config) ){
			if(empty(self::$config_bak)){
				Config::load('mongo');
				self::$config_bak = Config::get('mongo/');
			}
			empty($Sign) ?
				$key = self::$config_bak['default'] :
				$key = $Sign;
			self::$config = self::$config_bak[$key];
		}
		return $config = self::$config;
	}

	/**
	 * 	架构函数
	 */
	public function __construct(){
		$this->setConfig();
	}

	/**
	 *	MongoDB 类
	 */
	public function getDB(){
		$sign = md5(json_encode(self::$config));
		if(empty(self::$MongoDB[$sign])){
			$db = $this->getClient()->selectDB(self::$config['database']);
			$db->authenticate(self::$config['user'],self::$config['passwd']);
			return self::$MongoDB[$sign] = $db;
		}
		return self::$MongoDB[$sign];
	}

	/**
	 *	The MongoCollection class
	 */
	public function getCollection($value){
		return $this->getDB()->$value;
	}

	/**
	 *	获取 MongoClient 类
	 */
	public function getClient(){
		$server = self::$config['host'].':'.self::$config['port'];
		$sign = md5($server);
		if(empty(self::$MongoClient[$sign])){
			$mongo = new \MongoClient($server);
			return self::$MongoClient[$sign] = $mongo;
		}
		return self::$MongoClient[$sign];
	}

	/**
	 * 	单例模式，获取mongo对象
	 */
	public static function Instance($Sign = null){
		return new MongoDB();
	}

}
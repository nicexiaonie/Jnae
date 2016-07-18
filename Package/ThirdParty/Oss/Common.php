<?php


namespace ThirdParty\Oss;


use OSS\OssClient;
use OSS\Core\OssException;
use Core\Config;

Trait Common {
	private $_get_instance;
	private $Oss;

	//设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果上传文件很大，消耗的时间会比较长
	private $timeOut = 3600;
	//设置连接超时时间，单位秒，默认是10秒
	private $connectTimeout = 10;

	private $accessKeyId = "";
	private $accessKeySecret = "";
	private $endpoint = "";
	private $bucket = '';

	private static $config = array();



	public function __construct(){
		//step1、读取配置
			if(empty(self::$config)){
				Config::load('oss');
			}
			self::$config = $config = Config::get('Oss:');
			$this->timeOut = $config['TIMEOUT'];
			$this->connectTimeout = $config['CONNECTTIMEOUT'];
			$this->config_default();

		//step2、加载文件实例化
			require_once __DIR__ . '/Vendor/autoload.php';
			$this->connect();
	}
	private function connect(){

		//step1、根据配置确定code 如果实例存在 则直接返回 无需再次连接
		static $oss_instance;
		$config['accessKeyId']	=	$this->accessKeyId;
		$config['accessKeySecret']	=	$this->accessKeySecret;
		$config['endpoint']	=	$this->endpoint;
		$key_code = md5(json_encode($config));
		if($oss_instance[$key_code]) return $this->Oss = $oss_instance[$key_code];


		try {
			$ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint,true);
			$ossClient->setTimeout($this->timeOut);
			$ossClient->setConnectTimeout($this->connectTimeout);
			$this->Oss = $oss_instance[$key_code] = $ossClient;
		} catch (OssException $e) {
			print $e->getMessage();
		}

	}

	private function config_default(){
		$config = self::$config;
		$distribution = $config[$config['DEFAULT']];
		$this->accessKeyId = $distribution['accessKeyId'];
		$this->accessKeySecret = $distribution['accessKeySecret'];
		$this->endpoint = $distribution['endpoint'];
		$this->bucket = $distribution['bucket'];
	}
}

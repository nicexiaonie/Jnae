<?php
/*
 *
 *
 */

namespace Databases;

use Core\Config;

abstract class Db {

	public $_db_driver = null;	//驱动

	public $_get_instance;
	/*
	 *
	 * 加载数据库驱动
	 */
	public function __construct($db = null){

		//step1、获取数据库配置
			if(empty($db)){
				$default_db = Config::get('db/default_db');
			}else{
				$default_db = $db;
			}
			$db_config = Config::get('db/'.$default_db);

		if($db_config){
			//step2、加载驱动文件
				$drivers_file = __DIR__.'/Drivers/'.$db_config['driver'].'/'.$db_config['driver'].'_driver.php';
				if(is_file($drivers_file)){
					trace_add('load_file',$drivers_file);	//记录加载文件
					require_once($drivers_file);
				}else
					show_error("错误：{$drivers_file} 驱动不存在!");

			//step3、实例化驱动  并且连接数据库
				$class_name = $db_config['driver'].'_driver';
				$this->_db_driver = new $class_name($db_config);
				try{
					$this->_db_driver = $this->_db_driver->_connection();
				}catch(Exception $e){
					show_error('Message: ' .$e->getMessage());
				}

		}
	}






}

?>
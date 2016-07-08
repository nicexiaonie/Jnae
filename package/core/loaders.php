<?php


class loaders{
	public static $is_helper = array();	//已加载的函数文件
	public function _initialize($app){

		$this->config = $app->config;
	}

	public function load($className = false , $Object = true){
		if(!$className) return false;
		

		
	}
	/*	装载函数
	 * 	param1 string
	 * 		[demo]	//加载当前模块下的函数
	 * 		[/demo]	//加载框架下函数
	 * 		[admin/demo]	//加载某个模块下的函数
	 *
	 */
	public function helper($className = false){
		if($className == '/common') return false;
		if(!$className) return false;
		$className = explode('/',$className);

		if(count($className) == 1){
			$common_dir = $this->config->item('common_dir');
			if($common_dir) $common_dir .='/';
			$file_path = APP_PATH.$common_dir.'helpers/'.$className[0].$this->config->item('suffix');
		}else if(count($className) == 2){
			if(empty($className[0])){
				$file_path = PACKAGE_DIR.'helpers/'.$className[1].$this->config->item('suffix');
			}else{
				$file_path = APP_PATH.$className[0].'/helpers/'.$className[1].$this->config->item('suffix');
			}

		}


		//show($file_path);
		if(empty($file_path)) exit('helpers filename does not empty');
		if(empty(self::$is_helper[$file_path])){
			if(!is_file($file_path)){
				show_error('helpers file('.$file_path.') does not exist');
			}
			self::$is_helper[$file_path] = 1;
			trace_add('load_file',$file_path);	//记录加载文件
			include($file_path);
			return true;
		}
		return true;

	}




	

}
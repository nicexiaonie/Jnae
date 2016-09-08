<?php
namespace Core;

use View\View;
use Core\Hook;


abstract class Controller{


	private  $_get_instance;

	public function __construct(){
		#控制器开始标签位
		Hook::listen('action_begin');
		$this->_get_instance = get_instance();
	}

	//执行
	public function _execute($function_name = null,$param = null){

		if(method_exists($this,'_initialize'))
			$this->_initialize();

		if(empty($function_name)) $function_name = $this->uri->function_name;

		if(!method_exists($this,$function_name))
			show_error('This function/action('.$function_name.') does not exist');

		$before_function_name = '_before_'.$function_name;
		if(method_exists($this,$before_function_name))
			$this->$before_function_name();

		$content = $this->$function_name($param);

		$after_function_name = '_after_'.$function_name;
		if(method_exists($this,$after_function_name))
			$this->$after_function_name();

		if(Config::get('view_auto')){

		}

		return $content;
	}

	/*
	 *
	 *
	 */
	public function __get($name){
		return ($this->_get_instance->$name);
	}

	/*
	 * 	当方法不存在是调用  优先级
	 * 		APP超级对象
	 *
	 */
	public function __call($key,$value){
		$object = (object)array();

		if(method_exists($this->_get_instance,$key))
			$object = $this->_get_instance;
		else
			show_error('This method('.$key.') does not exist');
		list($value1,$value2,$value3,$value4) = $value;
		if(method_exists($object,$key))
			$result = $object->$key(
				$value1,
				$value2
			);
		return $this;
	}

	/**
	 * 操作错误跳转的快捷方法
	 * @access protected
	 * @param string $message 错误信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function error($message='',$jumpUrl='',$ajax=false) {
		$this->dispatchJump($message,'error',$jumpUrl,$ajax);
	}

	/**
	 * 操作成功跳转的快捷方法
	 * @access protected
	 * @param string $message 提示信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	protected function success($message='',$jumpUrl='',$ajax=false) {
		$this->dispatchJump($message,'success',$jumpUrl,$ajax);
	}
	/**
	 * 默认跳转操作 支持错误导向和正确跳转
	 * 调用模板显示 默认为public目录下面的success页面
	 * 提示页面为可配置 支持模板标签
	 * @param string $message 提示信息
	 * @param Boolean $status 状态
	 * @param string $jumpUrl 页面跳转地址
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @access private
	 * @return void
	 */
	private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
		$waitSecond = is_bool($ajax) ? 3 : $ajax;	//等待时间 S
		if(true === $ajax || IS_AJAX) {// AJAX提交
			\Core\Config::set('SHOW_TRACE',false);
			$data['info']   =   $message;
			$data['status'] =   $status;
			$data['url']    =   $jumpUrl;
			$this->ajaxReturn($data);
		}
		$view = View::init();

		View::$temp_dir = PACKAGE_DIR.'Tpl/';
		View::$filename = $status.Config::get('template_suffix');
		$view->assign('info',$message);
		$view->assign('status',$status);
		$view->assign('url',$jumpUrl);
		$view->assign('waitSecond',$waitSecond);

		$view->display();

	}

	/**
	 * Ajax方式返回数据到客户端
	 * @access protected
	 * @param mixed $data 要返回的数据
	 * @param String $type AJAX返回数据格式
	 * @param int $json_option 传递给json_encode的option参数
	 * @return void
	 */
	protected function ajaxReturn($data,$type='',$json_option=0) {

		if(empty($type)) $type  =   'json';
		switch (strtoupper($type)){
			case 'JSON' :
				// 返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				exit(json_encode($data,$json_option));
			case 'XML'  :
				// 返回xml格式数据
				header('Content-Type:text/xml; charset=utf-8');
				exit(xml_encode($data));
			case 'JSONP':
				// 返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				$handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
				exit($handler.'('.json_encode($data,$json_option).');');
			case 'EVAL' :
				// 返回可执行的js脚本
				header('Content-Type:text/html; charset=utf-8');
				exit($data);
			default     :
				// 用于扩展其他返回格式数据
				break;
		}
	}



	public function __destruct(){

		#控制器结束标签位
		Hook::listen('action_end');

	}



}
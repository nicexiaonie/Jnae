<?php
namespace Component;

use View\View;
use Core\Config;
class Response{

	/**
	 * 操作错误跳转的快捷方法
	 * @access protected
	 * @param string $message 错误信息
	 * @param string $jumpUrl 页面跳转地址 默认返回上一页，支持负数
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	static function error($message='',$jumpUrl='',$ajax=false) {
		static::dispatchJump($message,'error',$jumpUrl,$ajax);
	}

	/**
	 * 操作成功跳转的快捷方法
	 * @access protected
	 * @param string $message 提示信息
	 * @param string $jumpUrl 页面跳转地址 默认返回上一页，支持负数
	 * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
	 * @return void
	 */
	static function success($message='',$jumpUrl='',$ajax=false) {
		static::dispatchJump($message,'success',$jumpUrl,$ajax);
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
	static function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
		$waitSecond = is_bool($ajax) ? 3 : $ajax;	//等待时间 S
		if(true === $ajax || IS_AJAX) {// AJAX提交
			\Core\Config::set('SHOW_TRACE',false);
			$data['info']   =   $message;
			$data['status'] =   $status;
			$data['url']    =   $jumpUrl;
			static::ajaxReturn($data);
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
	static function ajaxReturn($data,$type='',$json_option=0) {
		Config::set('SHOW_TRACE',false);
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
}
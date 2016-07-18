<?php
namespace Component;
use View\View;
trait Jump{

	public function success($msg = '', $url = '', $sec = 3){
		View::assign('msg',$msg);
		View::assign('url',$url);
		View::assign('sec',$sec);
		View::setTempDir(PACKAGE_DIR.'Tpl/');
		View::display('success');
		View::$tempDir = null;
	}
	public function error($msg = '', $url = '', $sec = 3){
		exit($msg);
	}
}
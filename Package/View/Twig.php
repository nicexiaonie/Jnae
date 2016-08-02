<?php
/*
 * 	模版驱动类
 *
 * 		当进行display时，模版类会自动加载此类
 *
 * 		对模版进行检查 加载引擎驱动
 *
 * 		此类的方法为公共方法，如果方法不存在时  则调用对应引擎的驱动中的方法
 * 		（对应的引擎驱动的方法按需编写，如果不存在则直接操作引擎提供的方法，直至完成操作）
 *
 */
namespace View;

use \Core\Config;


require_once __DIR__.'/Twig/lib/Twig/Autoloader.php';

class Twig{

	private $assign =	array();

	/**
	 *	驱动入口
	 */
	public function run($value = null){
		\Twig_Autoloader::register();

		//$loader = new \Twig_Loader_Filesystem(View::$temp_dir);
		$loader = new \Twig_Loader_Array(array(
			'index.html' => 'Hello {{ the }}!',
		));



		$twig = new \Twig_Environment($loader, array(
			'cache' => View::$cache_dir,
			//'debug' => true,
		));


/*
		$template = $twig->loadTemplate('index.html');

		echo $template->render(array('the' => 'variables', 'go' => 'here'));
		//echo $twig->render('index.html', array('the' => 'variables', 'go' => 'here'));
*/
	}

	public function display($filename = null){

	}

	public function __call($key,$value){

	}





}
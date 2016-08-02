<?php
namespace Demo\Controller\View;

use \Core\Controller;

use \View\View;

class TwigController extends  Controller{


	public function index(){

		View::init();
		$loader = new \Twig_Loader_Filesystem(View::$temp_dir);
		/*
		$loader = new \Twig_Loader_Array(array(
			'index.html' => 'Hello {{ the }}!',
		));
		*/
		$twig = new \Twig_Environment($loader, array(
			'cache' => View::$cache_dir,
			'debug' => true,
		));

		$template = $twig->loadTemplate('index.html');

		echo $template->render(array('the' => 'variables', 'go' => 'here'));

	}
}
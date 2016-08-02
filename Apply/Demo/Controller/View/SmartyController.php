<?php
namespace Demo\Controller\View;

use \Core\Controller;

use \View\View;

class SmartyController extends  Controller{


	public function index(){

		$view = View::init();

		//Need to open the cache
		if(!$view->is_cached(View::$filename)) {
			// No cache available, do variable assignments here.

			$view->assign('aa','11');

		}

		$view->display();


	}
}
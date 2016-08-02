<?php

namespace Demo\Controller\Library;

use Core\Controller;

use Library\Qrlib;
use View\View;

class QrlibController extends Controller{


	public function _initialize(){


	}

	public function index(){



	}

	public function png(){

		$qrlib = new Qrlib();
		$url = $qrlib->create_png('http://www.baidu.com/',RUNTIME_DIR.'qrlib/');
		View::assign('url','/Runtime/qrlib/'.$url);
		View::display();

	}









}
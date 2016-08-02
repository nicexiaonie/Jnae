<?php

namespace Demo\Controller\Library;

use Core\Controller;

use Library\Input;


class InputController extends Controller{


	public function _initialize(){


	}

	public function index(){

		$get = Input::get();

		show($get);
	}



}
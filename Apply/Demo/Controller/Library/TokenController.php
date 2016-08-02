<?php
/*
 * 	验证类 Demo
 *
 */
namespace Demo\Controller\Library;

use Core\Controller;
use Core\Token;
use Library\Session;
use Library\Redis;


class TokenController extends Controller{


	public function _initialize(){

		Token::_initialize();
	}

	public function register(){



		echo Token::register();
	}









}
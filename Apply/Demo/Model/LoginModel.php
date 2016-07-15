<?php

namespace Demo\Model;

use Core\Model;
use Library\Session;
use Library\Cookie;

class LoginModel extends Model{


	public function __construct(){
		parent::__construct();
		show('success:('.__FILE__.')');

	}

	public function demo(){

		echo Session::get('aa');


		echo Cookie::get('aa');



	}









}
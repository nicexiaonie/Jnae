<?php
use Core\Model;

use Library\Session;
use Library\Cookie;
class loginModel extends Model{


	public function __construct(){
		parent::__construct();
		//show('success:('.__FILE__.')');

	}

	public function demo(){

		echo Session::get('aa');


		echo Cookie::get('aa');



	}









}
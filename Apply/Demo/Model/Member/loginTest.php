<?php
namespace Demo\Model\Member;

use Demo\Model\LoginModel;
class LoginTest extends LoginModel {


	public function __construct(){
		show('success_:('.__FILE__.')');
		parent::__construct();

	}
}
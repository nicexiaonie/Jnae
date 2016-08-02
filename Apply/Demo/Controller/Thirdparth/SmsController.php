<?php

namespace Demo\Controller\ThirdParth;

use \Core\Controller;
use \ThirdParty\Sms\Alidayu;


class SmsController extends Controller{


	public function _initialize(){


	}

	public function index(){

		$alidayu = new Alidayu();

		$result = $alidayu->send_sms(
			'13303826607',
			'安帮医生集团',
			'SMS_9565089',
			array(
				'code'	=>	'5116'
			)
		);

		show($result);

	}









}
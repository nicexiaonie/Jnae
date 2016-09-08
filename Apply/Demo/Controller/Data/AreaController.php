<?php
namespace Demo\Controller\Data;

use Core\Controller;
use Library\Input;
use View\View;

class AreaController extends Controller{


	public function Index(){

		$view = View::init();


		$view->display();
	}

	public function area(){
		$province = Input::get('province',1);
		$city = Input::get('city',1);

		$text = explode(',',Input::get('text',false));

		if(in_array('province',$text)){
			$m = M('sys_province');
			$result['province'] = $m->select();
			array_unshift($result['province'],array('name'=>'默认'));
		}

		if(in_array('city',$text)){
			$m = M('sys_city');
			$city_w['province_id'] = $province;
			$result['city'] = $m->where($city_w)->select();
			array_unshift($result['city'],array('name'=>'默认'));
		}

		if(in_array('area',$text)){
			$m = M('sys_area');
			$area['AND']['province_id'] = $province;
			$area['AND']['city_id'] = $city;
			$result['area'] = $m->where($area)->select();
			array_unshift($result['area'],array('id'=>'0','name'=>'默认'));
		}


		$this->success($result);

	}


}
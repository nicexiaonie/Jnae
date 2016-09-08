<?php
namespace Demo\Controller\Index;

use Core\Controller;
use Core\Exception;
use Core\Loaders;
use Library\Input;
use Library\MongoDB;
use Library\Session;
use Library\Upload;
use View\View;
use Library\Cache;
use Library\Redis;
use Core\Config;
use Library\Ueditor;
use Library\File;

class IndexController extends Controller{


	public function _initialize(){

		Config::set('SHOW_TRACE',false);
	}


	/*
	public function index(){
		$m = M('users');
		Config::set('SHOW_TRACE',false);
		$count = $m->count();
		$ids = array();
		for($i = 0; $i<1000;$i++){
			$ids[] = mt_rand(1,$count);
		}
		$ids = array_unique($ids);
		$list = $m->where(array('AND'=>array('id'=>$ids)))->select();
		foreach($list as &$v){
			unset($v['id']);
		}
		$result = $m->add($list);
		echo count($result)."    ";
		exit;

	}
	*/
/*
	public function index(){
		$m = M('sell_data');
		$copy = M('sell_data_copy');
		Config::set('SHOW_TRACE',false);


		$list = $m->select();

		foreach($list as &$v){
			unset($v['id']);
		}
		$result = $copy->add($list);
		echo count($result)."    ";
		exit;

	}
*/
	public function index(){
		exit;
		$m = M('sell');
		$copy = M('sell_data');


		$count = $m->field('id')->order('id DESC')->find();

		$ids = array();
		for($i = 0; $i<500;$i++){
			$ids[] = mt_rand(1,$count);
		}
		$ids = array_unique($ids);


		$sql = '
			select sell.*,sell_data.content
			from sell right join sell_data on sell.id = sell_data.id
			where sell.id in ('.join(',',$ids).')
			;
		';

		$list = $m->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

		$data = array();
		foreach($list as &$v){
			$data_tmp['content'] = $v['content'];
			$data[] = $data_tmp;
			unset($v['id']);
			unset($v['content']);
		}

		$result = $m->add($list);
		$result = $copy->add($data);

		echo count($result).'   ';

		exit;

	}


	public function create(){
		$m = M('sell_data');

		$list = $m->limit([4500,4529])->select();

		foreach($list as &$v){
			unset($v['id']);
		}



		$file = File::Instance('./Runtime/s/sell_data_2');

		$result = $file->write(json_encode($list));

		show($result);

	}





}
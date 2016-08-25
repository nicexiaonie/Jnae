<?php


namespace Core;

use Library\File;

class Handler{


	public function handleFatal(){
		if (null === ($e = error_get_last())) {
			return;
		}



		$n = 10;
		for($i = $e['line']-$n; $i<$e['line']+5; $i++){
			if($i<1) continue;
			$line[] = $i;

		}
		$file = File::Instance($e['file']);
		$code = $file->getLine($line);


		if(defined('DEBUG') && DEBUG === true){
			if(defined('IS_AJAX') && IS_AJAX === true){
				$e['code'] = $code;
				$result['status'] = 'error';
				$result['msg'] = 'System error';
				$result['data'] = $e;
				echo json_encode($result);
			}else{
				$dir = PACKAGE_DIR.'Tpl/Handler/';
				$style = '<style>'.file_get_contents($dir.'css/style.css').'</style>';
				$jquery = '<script>'.file_get_contents($dir.'js/jquery.min.js').'</script>';
				$prettify = '<script>'.file_get_contents($dir.'js/prettify.js').'</script>';
				include($dir.'index.php');
			}
		}else{


		}











		exit();
	}

	public function handle($errno, $errstr, $errfile, $errline){
		$error = "[{$errno}]：{$errstr} &nbsp;&nbsp; {$errfile} &nbsp; 第{$errline}行";
		trace_add('debug',$error);
	}


}
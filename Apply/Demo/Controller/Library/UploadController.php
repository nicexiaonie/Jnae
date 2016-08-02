<?php

namespace Demo\Controller\Library;

use Core\Controller;
use Core\Loaders;
use Library\Upload;
use View\View;


class UploadController extends Controller{

	public function _initialize(){

	}


	public function Oss(){
		if($_POST){
			//$oss = new \ThirdParty\Oss\Object();



		}else{
			View::display();
		}
	}


	/*
	 * 	分片上传（本地）
	 *	结合 webupload
	 */
	public function index(){
		if($_POST){

			Loaders::helper('/directory');
			Loaders::helper('/file');
			$upload = new Upload();
			#对文件设置唯一key
			$sign = md5($_POST['chunks'].$_POST['name']);
			#上传目录
			$dir = 'Upload/';
			#获取文件类型
			$arrStr=explode('.',$_POST['name']);

			//step1、验证上传文件 和 post中文件信息
				if(empty($_POST) || empty($_FILES['file']['name'])){
					$result['status'] = 0;
					$result['message'] = 'Error: Upload a file not found ';
					$result['data'] = '';
					echo json_encode($result);
					exit;
				}
				if(is_file($dir.$sign.'.'.$arrStr[count($arrStr)-1])){
					$result['status'] = 0;
					$result['message'] = 'Error：File already exists！';
					$result['data'] = '';
					echo json_encode($result);
					exit;
				}

			//step2、设置文件分片上传路径，进行分片上传
				$config['filepath'] = $dir.$sign.'/'; //上传分片目录
				$config['isranname'] = 3;
				$config['newFileName'] = $sign.'-'.$_POST['chunk'];
				create_dir($config['filepath']);
				$upload->start('file',$config);

			//step3、当上传目录中文件数与分片总数相同时，则代表分片上传完成 开始合并分片
				$files = directory_map($config['filepath']);

				if(count($files) == $_POST['chunks']){

					#睡眠2秒 等待文件系统处理完成
					sleep(2);
					#真实上传文件名
					$new_file = 'Upload/'.$sign.'.'.$upload->fileType;
					#循环对分片进行合并
					for($i = 0 ; $i < count($files); $i++){
						$tmp_file = $sign.'-'.$i;
						if(!is_file($new_file)){
							rename($config['filepath'].$tmp_file,$new_file);
							continue;
						}
						file_put_contents($new_file,file_get_contents($config['filepath'].$tmp_file),FILE_APPEND);
						//unlink($config['filepath'].$tmp_file);
					}
					//rmdir($config['filepath']);
					$result['status'] = 1;
					$result['message'] = 'Upload Success！';
					$result['data'] = '';
					echo json_encode($result);
					exit;
				}
		}else{

			View::display();
			exit;
		}
	}








}
<?php
/**
 * 	缓存静态文件驱动
 *
 */

namespace Library\Cache;
use Core\Config;

class File{
	private $cache_dir = '';


	public function __construct(){
		$cache_config = Config::get('cache');
		if(empty($cache_config['cache_dir']))
			$cache_config['cache_dir'] = 'Data';
		$this->cache_dir = RUNTIME_DIR.rtrim($cache_config['cache_dir'],'/');
		if(!is_dir($this->cache_dir))
			create_dir($this->cache_dir);
	}

	private function getName($name = ''){
		if(!empty($name)) $name = md5($name);
		$filename = $this->cache_dir.'/'.$name;
		return $filename;
	}

	/**
	 * 	write
	 * 	写入文件内容
	 */
	public function write($name , $content,$second = 0){
		$filename = $this->getName($name);
		$text['content'] = $content;
		$text['ctime'] = time();
		$text['utime'] = time();
		$text['expire'] = $second;

		return file_put_contents($filename,json_encode($text));
	}

	/**
	 * 	read
	 * 	读取文件内容
	 */
	public function read($name){
		$filename = $this->getName($name);
		if(!is_file($filename)) return false;
		$text = json_decode(file_get_contents($filename));

		//验证是否过期
			if($text->expire > 0){
				if(time() > ($text->ctime+$text->expire)){
					unlink($filename);
					return null;
				}
			}

		return $text->content;
	}

	/**
	 * 	expire
	 * 	设置文件获取时间
	 */
	public function expire($name , $second){
		$filename = $this->getName($name);
		if(!is_file($filename)) return false;
		$text = json_decode(file_get_contents($filename));

		$text->expire = $second;
		$text->utime = time();
		return file_put_contents($filename,json_encode($text));
	}

	/**
	 * 	ttl
	 * 	设置文件获取时间
	 */
	public function ttl($name){
		$filename = $this->getName($name);
		if(!is_file($filename)) return false;
		$text = json_decode(file_get_contents($filename));

		$surplus = ( ($text->ctime+$text->expire)-time() );

		return ($surplus<0) ? -1 : $surplus;
	}

	/**
	 * 	delete
	 * 	删除缓存
	 */
	public function delete($name){
		$filename = $this->getName($name);
		if(is_file($filename)) unlink($filename);
		return true;
	}

	/**
	 * 	clear
	 * 	清楚全部缓存
	 */
	public function clear(){
		$dir = $this->getName();
		return delete_files($dir);;
	}

	/**
	 * 	read
	 * 	读取文件内容
	 */
	public function has($name){
		$filename = $this->getName($name);
		if(!is_file($filename)) return false;
		$text = json_decode(file_get_contents($filename));

		//验证是否过期
		if($text->expire > 0){
			if(time() > ($text->ctime+$text->expire)){
				unlink($filename);
				return false;
			}
		}

		return true;
	}





}
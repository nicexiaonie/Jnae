<?php


namespace ThirdParty\Oss;

class Object {
	use \ThirdParty\Oss\Common;

	/**
	 * 字符串上传
	 *
	 * @param $fileName 上传后文件名称
	 * @param $content 文件内容
	 * @return null
	 */
	public function putObject($fileName,$content){
		try {
			$bucket= $this->bucket;
			$object = $fileName;	//也就是上传后文件名
			$content = $content;
			$this->Oss->putObject($bucket, $object, $content);
		} catch (OssException $e) {
			print $e->getMessage();
		}
		return true;
	}

	/**
	 * 上传指定的本地文件内容
	 *
	 * @param $oldFilePath 本地文件名称
	 * @param $newFilePath 上传后文件名称
	 * @return null
	 */
	public function uploadFile($oldFilePath, $newFilePath)
	{

		try{
			$this->Oss->uploadFile($this->bucket, $newFilePath, $oldFilePath);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		return true;
	}

	/**
	 * 判断object是否存在
	 *
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket bucket名字
	 * @return null
	 */
	public function doesObjectExist($object)
	{
		try{
			$exist = $this->Oss->doesObjectExist($this->bucket, $object);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}

		return $exist;
	}
	/**
	 * 创建虚拟目录
	 *
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function createObjectDir($dir) {
		$ossClient = $this->Oss;
		$bucket = $this->bucket;
		try{
			$ossClient->createObjectDir($bucket, $dir);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		return true;
	}

	/**
	 * 删除object
	 *
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket bucket名字
	 * @return null
	 */
	function deleteObject($object)
	{
		$ossClient = $this->Oss;
		$bucket = $this->bucket;

		try{
			$result = $ossClient->deleteObject($bucket, $object);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		return true;
	}

	/**
	 * 批量删除object
	 *
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket bucket名字
	 * @return null
	 */
	function deleteObjects($objects)
	{
		$ossClient = $this->Oss;
		$bucket = $this->bucket;
		try{
			$ossClient->deleteObjects($bucket, $objects);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		return true;
	}

	/**
	 * 拷贝object
	 *
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket bucket名字
	 * @return null
	 */
	function copyObject($object,$to_object)
	{
		$ossClient = $this->Oss;
		$bucket = $this->bucket;

		$from_bucket = $bucket;
		$from_object = $object;
		$to_bucket = 'xiaonie365-a';
		$to_object = $to_object;
		try{
			$ossClient->copyObject($from_bucket, $from_object, $to_bucket, $to_object);
		} catch(OssException $e) {
			printf(__FUNCTION__ . ": FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		return true;
	}


}
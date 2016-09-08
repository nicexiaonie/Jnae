<?php

if ( ! function_exists('read_file'))
{
	/**
	 * Read File
	 */
	function read_file($file)
	{
		return @file_get_contents($file);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('write_file'))
{
	/**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	$path	File path
	 * @param	string	$data	Data to write
	 * @param	string	$mode	fopen() mode (default: 'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('delete_files'))
{
	/**
	 * Delete Files
	 *
	 * Deletes all files contained in the supplied directory path.
	 * Files must be writable or owned by the system in order to be deleted.
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 *
	 * @param	string	$path		File path
	 * @param	bool	$del_dir	Whether to delete any directories found in the path
	 * @param	bool	$htdocs		Whether to skip deleting .htaccess and index page files
	 * @param	int	$_level		Current directory depth level (default: 0; internal use only)
	 * @return	bool
	 */
	function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0)
	{
		// Trim the trailing slash
		$path = rtrim($path, '/\\');

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename !== '.' && $filename !== '..')
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename) && $filename[0] !== '.')
				{
					delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $htdocs, $_level + 1);
				}
				elseif ($htdocs !== TRUE OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
				{
					@unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}

		closedir($current_dir);

		return ($del_dir === TRUE && $_level > 0)
			? @rmdir($path)
			: TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_filenames'))
{
	/**
	 * Get Filenames
	 *
	 * 读取一个目录中的所有文件
	 *
	 * @param	string	path to source
	 * @param	bool	是否返回真实路径
	 * @param	bool	internal variable to determine recursion status - do not use in calls
	 * @return	array
	 */
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.')
				{
					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[] = ($include_path === TRUE) ? $source_dir.$file : $file;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}


/**
 * 递归创建目录
 */
function create_dir($source_dir = null){
	$dir = explode('/',rtrim($source_dir,'/'));

	$dir_path = '';
	foreach($dir as $k=>$v){
		$dir_path .= $v.'/';
		if(is_dir($dir_path)) continue;
		if(!mkdir($dir_path)) show_error('Failed to create the directory ('.$dir_path.')');
	}
	return true;
}




<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Libre_install extends CI_Controller {
	function __construct ()
	{
		parent::__construct();
	}
	
	function index ()
	{
		try {
			// check_permission of modules folder
			if (!is_writable("./application/modules"))
			{
				throw new Exception("please change permission of this folder: /application/modules to 777", 1);
			}
			if (!is_writable("./libre_assets"))
			{
				throw new Exception("please change permission of this folder: /libre_assets to 777", 1);
			}
			
			if (file_exists("./application/modules/modulemanagement"))
			{
				$this -> delete_folder("./application/modules/modulemanagement");	
			}
			if (file_exists("./libre_assets/modulemanagement"))
			$this->delete_folder("./libre_assets/modulemanagement");

			$link = "http://market.plusigniter.com/assets/modules/modulemanagement/modulemanagement.zip";
			$this -> download_zip($link);
	
			$this -> extract_zip("modulemanagement.zip");
			// change mode 
			$this->chmod_r("./application/modules/modulemanagement", 0755);
			$this->chmod_r("./application/modules/modulemanagement", 0777);
			// move assets
			if (file_exists("./application/modules/modulemanagement/modulemanagement"))
			{
				rename("./application/modules/modulemanagement/modulemanagement", "./libre_assets/modulemanagement");
				$this->chmod_r("./libre_assets/modulemanagement", 0755);
				$this->chmod_r("./libre_assets/modulemanagement", 0777);
			}
			// import sql
			if (file_exists('./application/modules/modulemanagement/sql/sql.php'))
			{
				require_once './application/modules/modulemanagement/sql/sql.php';	
				foreach ($query as $value) {
					$this->db->query($value);			
				}
			}
				
			redirect("/modulemanagement/");
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * download_zip() - Download module from market
	 * @param link: absolute url to the module
	 * @access protected
	 */
	protected function download_zip($link) {
		$this -> load -> helper('file');
		$content = get_headers($link, 1);
		$content = array_change_key_case($content, CASE_LOWER);

		// by header
		if (isset($content['content-disposition'])) {
			$tmp_name = explode('=', $content['content-disposition']);
			if ($tmp_name[1])
				$realfilename = trim($tmp_name[1], '";\'');
		} else// by URL Basename
		{
			$stripped_url = preg_replace('/\\?.*/', '', $link);
			$realfilename = basename($stripped_url);
		}
		if (!$data = @file_get_contents($link)) {
			throw new Exception("Invalid keyname", 1);
		}
		write_file('./application/modules/' . $realfilename, $data);
	}
	/**
	 * extract_zip() - Extract *module*.zip after download
	 * @param fileName: *module*.zip
	 * @access protected
	 */
	protected function extract_zip($fileName) {
		$this -> load -> helper('file');
		
		$path = './application/modules/';
		$zip = new ZipArchive;

		$res = $zip -> open($path . $fileName);

		if ($res == TRUE) {
			$zip -> extractTo($path);
			$zip -> close();
		}
		// remove zip
		unlink($path . $fileName);
	}
	/**
	 * delete_folder() - delete a folder
	 * @param void
	 * @access protected
	 */
	protected function delete_folder($directory = null) {
		$dir_iterator = new RecursiveDirectoryIterator($directory);
		$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach ($iterator as $file) {
			if (is_dir($file)) {
				rmdir($file);
			} else {
				unlink($file);
			}
		}
		
		if (file_exists($directory))
		{
			rmdir($directory);
		}
	}
	/**
	 * chmod_r() - change mode of a file/folder recursive
	 * @access public
	 */
	function chmod_r ($pathname, $filemode)
	{
		if (file_exists($pathname))
		{
			chmod($pathname, $filemode);
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname), RecursiveIteratorIterator::SELF_FIRST);
			
			foreach($iterator as $item) {
			    chmod($item, $filemode);
			}
		}
	}
}
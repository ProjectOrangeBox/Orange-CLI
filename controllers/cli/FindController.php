<?php

class FindController extends MY_Controller {

	/**
		Search your application for files.
	*/
	public function fileCliAction($filename=null) {
		require_once __DIR__.'/../../libraries/Console.php';

		$console = new Console;

		//$filename = end(explode('/',$_SERVER['argv'][2]));

		if (empty($filename)) {
			$console->error('Please provide a filename to search for.');
		}

		$console->info('Looking for "'.$filename.'"');

		require APPPATH.'/config/autoload.php';

		$autoload['packages'][] = APPPATH;
		$autoload['packages'][] = BASEPATH;

		foreach ($autoload['packages'] as $package) {

			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($package,FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_SELF));
    
			foreach ($files as $name=>$file) {
				if (!$file->isDot()) {
					if (substr($file->getFilename(),0,1) != '.') {
						$re = '/'.preg_quote($filename).'/mi';
		
						if (preg_match_all($re, $file->getFilename(), $matches, PREG_SET_ORDER, 0)) {
							$parts = pathinfo(str_replace(ROOTPATH,'',$name));

							$styled = str_ireplace($matches[0][0],'<cyan>'.$matches[0][0].'</off>',$parts['basename']);
							
							$console->e($parts['dirname'].'/'.$styled);
						}
					}
				}
			}
		}

	}

} /* end class */

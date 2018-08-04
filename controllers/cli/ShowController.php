<?php

class ShowController extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->console = new League\CLImate\CLImate;
	}

	/**
		Show all available validation classes. Use optional -p to show path instead of help
	*/
	public function validateCliAction($arg=null) {
		$orange_paths = orange_autoload_files::paths('classes');

		foreach ($orange_paths as $name=>$path) {
			if (substr($name,0,strlen('validate_')) == 'validate_') {
				$this->line($name,$path,$arg);
			}
		}
	}

	/**
		Show all available pear classes. Use optional -p to show path instead of help
	*/
	public function pearCliAction($arg=null) {
		$orange_paths = orange_autoload_files::paths('classes');

		foreach ($orange_paths as $name=>$path) {
			if (substr($name,0,strlen('pear_')) == 'pear_') {
				$this->line($name,$path,$arg);
			}
		}
	}

	/**
		Show all available filter classes. Use optional -p to show path instead of help
	*/
	public function filterCliAction($arg=null) {
		$orange_paths = orange_autoload_files::paths('classes');

		foreach ($orange_paths as $name=>$path) {
			if (substr($name,0,strlen('filter_')) == 'filter_') {
				$this->line($name,$path,$arg);
			}
		}
	}

	/**
		Show all available models classes. Use optional -p to show path instead of help
	*/
	public function modelsCliAction($arg=null) {
		$orange_paths = orange_autoload_files::paths('models');

		foreach ($orange_paths as $name=>$path) {
			$this->line($name,$path,$arg);
		}
	}

	/**
		Show all available libraries classes. Use optional -p to show path instead of help
	*/
	public function librariesCliAction($arg=null) {
		$orange_paths = orange_autoload_files::paths('libraries');

		foreach ($orange_paths as $name=>$path) {
			$this->line($name,$path,$arg);
		}
	}

	protected function line($name,$path,$arg) {
		$padding = 40;

		echo str_pad($name,$padding);

		if ($arg == '_p') {
			/* show path */
			echo $path.PHP_EOL;
		} else {
			echo $this->get_help($path,$padding);
		}
	}

	protected function get_help($filepath,$padding) {
		$content = file_get_contents($filepath);

		$int = preg_match_all('/@show(.*)/m', $content, $matches, PREG_SET_ORDER, 0);

		$text = '';

		if ($int) {
			$idx = 1;

			foreach ($matches as $match) {
				if ($idx == 1) {
					$text .= trim($match[1]).PHP_EOL;
				} else {
					$text .= str_repeat(' ',$padding).trim($match[1]).PHP_EOL;
				}

				$idx++;
			}
		}

		return trim($text).PHP_EOL;
	}

}
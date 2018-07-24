<?php

class ShowController extends MY_Controller {

	/**
		Show all available validation classes. Use optional -p to show path instead of help
	*/
	public function validateCliAction($arg=null) {
		$orange_paths = orange_paths('classes');
		
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
		$orange_paths = orange_paths('classes');
		
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
		$orange_paths = orange_paths('classes');
		
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
		$orange_paths = orange_paths('models');
		
		foreach ($orange_paths as $name=>$path) {
			$this->line($name,$path,$arg);
		}
	}

	/**
		Show all available libraries classes. Use optional -p to show path instead of help
	*/
	public function librariesCliAction($arg=null) {
		$orange_paths = orange_paths('libraries');
		
		foreach ($orange_paths as $name=>$path) {
			$this->line($name,$path,$arg);
		}
	}
	
	protected function line($name,$path,$arg) {
		echo str_pad($name, 40);
		
		if ($arg == '_p') {
			/* show path */
			echo $path;
		} else {
			echo $this->get_help($path);
		}
		
		echo chr(10);
	}
	
	protected function get_help($filepath) {
		$content = file_get_contents($filepath);
		
		$int = preg_match_all('/@show(.*)/m', $content, $matches, PREG_SET_ORDER, 0);
		
		return ($int) ? trim($matches[0][1]) : '';
	}

}
<?php

class ShowController extends MY_Controller {
	protected $console;

	public function __construct() {
		parent::__construct();
		
		require_once __DIR__.'/../../libraries/Console.php';

		$this->console = new Console;
	}

	/**
	Show all of the filters
	*/
	public function filtersCliAction() {
		$this->loop('classes','/^filter_(.*)$/');
	}

	/**
	Show all of the Pear Plugins
	*/
	public function Pear_pluginsCliAction() {
		$this->loop('classes','/^pear_(.*)$/');
	}

	/**
	Show all of the validations
	*/
	public function validationsCliAction() {
		$this->loop('classes','/^validate_(.*)$/');
	}

	/**
	Show all of the models
	*/
	public function modelsCliAction() {
		$this->loop('models');
	}

	/**
	Show all of the libraries
	*/
	public function librariesCliAction() {
		$this->loop('libraries');
	}

	/**
	Show all of the Middleware
	*/
	public function middlewareCliAction() {
		$this->loop('classes','/^(.*)middleware$/');
	}

	protected function loop($key,$pattern='/(.*)/',$comment_from='methods') {
		$array = orange_paths($key);

		foreach ($array as $name=>$path) {
			if (preg_match($pattern,$name)) {
				$inspection = (new Fruit_inspector)->inspect($path,'class');

				$this->console->e('<cyan>'.$name.'</off>');
				
				if ($comment_from == 'methods') {
					Fruit_inspector::clean_comments($inspection['methods']['filter']['comments'],$this->console);
				} else {
					Fruit_inspector::clean_comments($inspection[$comment_from]['comments'],$this->console);
				}

			}
		}
	}

} /* end class */
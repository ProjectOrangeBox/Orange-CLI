<?php

class HelpController extends MY_Controller {
	public $dump;

	public function __construct() {
		parent::__construct();

		require __DIR__.'/../../libraries/Dump.php';
		
		$this->dump = new Dump;
	}
	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		$this->dump->cli_list();
	}
}

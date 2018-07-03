<?php

class DumpController extends MY_Controller {
	public $dump;

	public function __construct() {
		parent::__construct();
	
		require __DIR__.'/../../libraries/Dump.php';
		
		$this->dump = new Dump;
	}

	public function permissionsCliAction() {
		$this->dump->permissions();
	}

} /* end class */

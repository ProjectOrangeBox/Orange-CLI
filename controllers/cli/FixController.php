<?php

class FixController extends MY_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library(['orange_tools','migration_extras','console']);
	}

	public function indexCliAction() {
		$this->console->out($this->load->view('fix_help',null,true));
	}

	public function completeCliAction() {
		$this->orange_tools->fix();
	}

	public function aclCliAction() {
		$this->orange_tools->fix_acl();
	}

	public function permissionsCliAction() {
		$this->orange_tools->fix_file_permissions();
	}

	public function symlinkCliAction() {
		$this->orange_tools->fix_symlink();
	}

	public function cacheCliAction() {
		$results = $this->orange_tools->clear_cache();

		$this->console->e($results);
	}

	public function cacheAction($key='') {
		die($this->orange_tools->clear_cache_process($key));
	}

	public function gitAction() {
		$table_template = [
			'table_open' => '<table class="table table-condensed table-bordered">',
		];

		ci('page')->data(['html'=>ci('orange_tools')->git_status('html',$table_template)])->render();
	}

} /* end class */

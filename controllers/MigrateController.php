<?php 

class MigrateController extends MY_Controller {

	public function gitAction() {
		$this->load->library('orange_tools');

		$table_template = [
			'table_open' => '<table class="table table-condensed table-bordered">',
		];

		$this->page->data(['html'=>$this->orange_tools->git_status('html',$table_template)])->render();
	}

	public function gitCliAction() {
		$this->load->library('orange_tools');

		echo $this->orange_tools->git_status('cli');
	}
	
	public function migrationCliAction($action='current') {
		$this->load->library('orange_tools');
		
		$this->orange_tools->migration($action);
	}
	
	public function fixCliAction() {
		$this->load->library('orange_tools');

		$this->orange_tools->fix();
	}
	
	public function fix_aclAction() {
		delete_cache_by_tags('acl');	
	}

} /* end class */

<?php

class MigrateController extends MY_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library(['orange_tools','migration_extras','console']);
	}

	public function indexCliAction() {
		$this->console->out($this->load->view('migrate_help',null,true));
	}

	public function gitCliAction() {
		$results = $this->orange_tools->git_status('cli');

		$this->console->e('<yellow>'.$results);
	}

	public function currentCliAction() {
		/* TRUE if no migrations are found, current version string on success, FALSE on failure */

		$results = $this->migration_extras->current();

		if ($results === true) {
			$results = 'No migrations are found';
		} elseif($results === false) {
			$results = 'failure';
		}

		$this->console->e($results);
	}

	public function findCliAction() {
		/* An array of migration files */

		$results = $this->migration_extras->find();

		foreach ($results as $result) {
			$pathinfo = pathinfo($result);

			$this->console->e($pathinfo['filename']);
		}
	}

	public function latestCliAction() {
		/* Current version string on success, FALSE on failure */

		$results = $this->migration_extras->latest();

		if ($results === false) {
			$results = 'failure';
		} else {
			$results = 'Latest Version '.$results;
		}

		$this->console->e($results);
	}

	public function versionCliAction($mixed='') {
		/* TRUE if no migrations are found, current version string on success, FALSE on failure */

		if (empty($mixed)) {
			$this->console->error('Version not specified.');
		}

		$results = $this->migration_extras->version($mixed);

		if ($results === true) {
			$results = 'No migrations are found';
		} elseif($results === false) {
			$results = 'failure';
		}

		$this->console->e($results);
	}

	public function createCliAction($description='') {
		if (empty($description)) {
			$this->console->error('description not specified.');
		}

		$results = $this->migration_extras->create($description);

		$this->console->e('<yellow>'.$results);
	}

	public function composerCliAction() {
		chdir(ROOTPATH);

		echo getcwd().chr(10);

		passthru('composer update');
	}

	public function site_upCliAction() {
		ci('orange_tools')->site_up();
	}

	public function site_downCliAction() {
		ci('orange_tools')->site_down();
	}

} /* end class */

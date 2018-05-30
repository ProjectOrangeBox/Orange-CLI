<?php

class MigrateController extends MY_Controller {
	/* https://www.codeigniter.com/user_guide/libraries/migration.html */
	public $responds = null;

	/**
		Wrapper for migrate/current
	*/
	public function upCliAction() {
		$this->currentCliAction();
	}

	/**
		Wrapper for migrate/version/###
	*/
	public function downCliAction($version=null) {
		$this->versionCliAction($version);
	}

	/* built in functions */

	/**
		Migrates up to the current version
		whatever is set for $config['migration_version'] in application/config/migration.php.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::current
	*/
	public function currentCliAction() {
		$mixed = ci('migration')->current();

		if ($mixed) {

		} else {

		}
	}

	/**
		Return an array of migration filenames that are found in the migration_path property.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::find_migrations
	*/
	public function findCliAction() {
		$this->responds = ci('migration')->find_migrations();
	}

	/**
		This works much the same way as current() but instead of looking for the $config['migration_version']
		the Migration class will use the very newest migration found in the filesystem.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::latest
	*/
	public function latestCliAction() {
		$this->responds = ci('migration')->latest();
	}

	/**
		Version can be used to roll back changes or step forwards programmatically to specific versions.
		It works just like current() but ignores $config['migration_version'].

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::version
	*/
	public function versionCliAction($version=null) {
		if ((int)$version < 1) {
			ci('console')->error('Please provide version number.');
		}

		$this->responds = ci('migration')->version($version);
	}

	/**
	Builds a standard migration template
	*/
	public function createCliAction($description=null) {
		if (empty($description)) {
			ci('console')->error('description not specified.');
		}

		ci('console')->e('<yellow>'.ci('create_migration_file')->create($description));

		exit(0);
	}

	public function _output($output) {
		if (is_scalar($this->responds)) {
			ci('console')->e($this->responds);
		} elseif (is_array($this->responds)) {
			foreach ($this->responds as $o) {
				ci('console')->e(trim($o));
			}
		}

		/* did we get any errors? */
		$errors = trim(ci('migration')->error_string());

		if (!empty($errors)) {
			ci('console')->error($errors);
		}
	}

} /* end class */

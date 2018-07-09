<?php

class MigrateController extends MY_Controller {
	protected $version_arg = 1;
	protected $description_arg = 1;
	protected $folder_arg = 1;

	/**
		Wrapper for migrate/current
	*/
	public function upCliAction() {
		$this->get_folder();

		ci('package_migration_cli_wrapper')->latest();
	}

	/**
		Wrapper for migrate/version/###
	*/
	public function downCliAction() {
		$this->get_folder();

		ci('package_migration_cli_wrapper')->version((int)$this->get_section($this->version_arg,'version'));
	}

	/* built in functions */

	/**
		This works much the same way as current() but instead of looking for the $config['migration_version']
		the Migration class will use the very newest migration found in the filesystem.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::latest

		TRUE if no migrations are found, current version string on success, FALSE on failure
		*/
	public function latestCliAction() {
		$this->get_folder();

		ci('package_migration_cli_wrapper')->latest();
	}

	/**
		Version can be used to roll back changes or step forwards programmatically to specific versions.
		It works just like current() but ignores $config['migration_version'].

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::version

		TRUE if no migrations are found, current version string on success, FALSE on failure
	*/
	public function versionCliAction() {
		$this->get_folder();

		ci('package_migration_cli_wrapper')->version((int)$this->get_section($this->version_arg,'version'));
	}

	/**
		Return an array of migration filenames that are found in the migration_path property.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::find_migrations
	*/
	public function findCliAction() {
		require APPPATH.'/config/autoload.php';

		ci('package_migration_cli_wrapper')->set_path()->find();

		foreach ($autoload['packages'] as $package) {
			ci('package_migration_cli_wrapper')->set_path($package.'/support/migrations/')->find($package);
		}
	}

	/**
	Builds a standard migration template
	*/
	public function createCliAction() {
		$this->get_folder();

		ci('package_migration_cli_wrapper')->create($this->get_section($this->description_arg,'description'));
	}

	protected function get_folder() {
		/* did they include anything? */
		$raw_folder = $this->get_section($this->folder_arg,'package folder',false);

		/* is arg1 a folder */
		if (strpos($raw_folder,'/') !== false) {
			$this->version_arg++;
			$this->description_arg++;

			/* verify it's a valid package */
			$folder = ROOTPATH.'/'.trim($raw_folder,'/');

			if (!file_exists($folder)) {
				show_error('"'.$raw_folder.'" does not seem to be a valid package path.');
			}

			/* verify it has a valid migration folder */
			$folder .='/support/migrations';

			if (!file_exists($folder)) {
				mkdir($folder,0777,true);
				
				if (!file_exists($folder)) {
					show_error('"'.$raw_folder.'" does not seem to be a valid package migration path.');
				}
			}

			ci('package_migration_cli_wrapper')->set_path($folder.'/');
		}
	}

	protected function get_section($num,$text,$required=true) {
		/* the first useable arg is 2 */
		$num = $num + 1;

		if ($required) {
			if (trim($_SERVER['argv'][$num]) == '') {
				show_error('Please provide a '.$text.'.');
			}
		}

		return $_SERVER['argv'][$num];
	}

} /* end class */
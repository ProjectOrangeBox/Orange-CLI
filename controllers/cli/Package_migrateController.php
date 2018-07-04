<?php

class Package_migrateController extends MY_Controller {
	/* https://www.codeigniter.com/user_guide/libraries/migration.html */

	/**
		Wrapper for migrate/current
	*/
	public function upCliAction() {
		$this->latestCliAction();
	}

	/**
		Wrapper for migrate/version/###
	*/
	public function downCliAction() {
		$this->versionCliAction();
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

		$mixed = ci('migration_handler')->latest();

		if ($mixed === true) {
			echo 'No migrations found.'.chr(10);
		} elseif($mixed === false) {
			show_error('Error updating to the latest migration. ['.ci('migration_handler')->error_string().']');
		} else {
			echo 'Version upgraded to '.$mixed.'.'.chr(10);
		}
	}

	/**
		Version can be used to roll back changes or step forwards programmatically to specific versions.
		It works just like current() but ignores $config['migration_version'].

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::version
			
		TRUE if no migrations are found, current version string on success, FALSE on failure
	*/
	public function versionCliAction() {
		$this->get_folder();
		
		$mixed = ci('migration_handler')->version($this->get_version());

		if ($mixed === true) {
			echo 'No migrations found.'.chr(10);
		} elseif($mixed === false) {
			show_error('Error updating to the latest migration. ['.ci('migration_handler')->error_string().']');
		} else {
			echo 'Version changed to '.$mixed.'.'.chr(10);
		}
	}

	/**
		Return an array of migration filenames that are found in the migration_path property.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::find_migrations
	*/
	public function findCliAction() {
		$found_sets = [];

		require APPPATH.'/config/autoload.php';

		$packages = $autoload['packages'];

		foreach ($packages as $package) {
			ci('migration_handler')->_migration_path = $package.'/support/migrations/';

			$found = ci('migration_handler')->find_migrations();

			if (count($found)) {
				$highest = ci('migration_handler')->get_version();
				
				echo str_replace(ROOTPATH,'',$package).' - Current Version '.$highest.chr(10);
	
				foreach ($found as $num=>$value) {
					if ((int)$num <= $highest) {
						echo "  \e[3m*".str_pad($num,6).basename($value)."\e[0m".chr(10);
					} else {
						echo '   '.str_pad($num,6).basename($value).chr(10);
					}
				}
			}
		}

	}

	/**
	Builds a standard migration template
	*/
	public function createCliAction($description=null) {
		$folder = $this->get_folder();
		$description = $this->get_section(3,'description');

		ci('create_migration_file')->create($description,$folder);
	}

	protected function get_folder($set = true) {
		/* did they include anything? */
		$folder = $this->get_section(2,'package folder');

		/* verify it's a valid package */
		$folder = ROOTPATH.'/'.trim($folder,'/');

		if (!file_exists($folder)) {
			show_error('"'.$folder.'" does not seem to be a valid package path.');
		}

		/* verify it has a valid migration folder */
		$folder .='/support/migrations';

		if (!file_exists($folder)) {
			show_error('"'.$folder.'" does not seem to be a valid package migration path.');
		}

		if ($set) {
			ci('migration_handler')->_migration_path = $folder.'/';

			echo 'Switching to folder "'.ci('migration_handler')->_migration_path.'".'.chr(10);
		}

		return $folder.'/';
	}

	protected function get_version() {
		return (int)$this->get_section(3,'version');
	}
	
	protected function get_section($num,$text) {
		if (empty($_SERVER['argv'][$num])) {
			show_error('Please provide a '.$text.'.');
		}

		return $_SERVER['argv'][$num];
	}

} /* end class */
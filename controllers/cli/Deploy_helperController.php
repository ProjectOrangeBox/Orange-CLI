<?php

class Deploy_helperController extends MY_Controller {

	/**
		Generate the Deploy JSON for adding all found GIT Repositories
	*/
	public function indexCliAction() {
		$console = new League\CLImate\CLImate;

		$autoload = load_config('autoload','autoload');
		$packages = $autoload['packages'];

		$packages = array_merge([''],$packages);

		foreach ($packages as $package) {
			/* "gitx update {PWD}/packages/projectorangebox/extra-validations {GITBRANCH}" */
			$git_update[] = '"gitx update {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';

			/* "cd {PWD}/public;php index.php cli/migrate/up packages/projectorangebox/scaffolding" 	*/
			$migrations[] = '"cd {PWD}/public;php index.php cli/migrate/up'.str_replace(ROOTPATH,' ',$package).'"';
		}

		$climate->border();

		foreach ($git_update as $line) {
			$console->out($line);
		}

		$climate->border();
		
		foreach ($migrations as $line) {
			$console->out($line);
		}

		$climate->border();

	}
}
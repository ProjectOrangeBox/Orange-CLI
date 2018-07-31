<?php

class Deploy_helperController extends MY_Controller {

	/**
		Generate the Deploy JSON for adding all found GIT Repositories
	*/
	public function indexCliAction() {
		$autoload = load_config('autoload','autoload');
		$packages = $autoload['packages'];

		$packages = array_merge([''],$packages);

		foreach ($packages as $package) {
			/* "gitx update {PWD}/packages/projectorangebox/extra-validations {GITBRANCH}" */
			$git_update[] = '"gitx update {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';

			/* "cd {PWD}/public;php index.php cli/migrate/up packages/projectorangebox/scaffolding" 	*/
			$migrations[] = '"cd {PWD}/public;php index.php cli/migrate/up'.str_replace(ROOTPATH,' ',$package).'"';
		}

		echo implode(','.PHP_EOL,$git_update).PHP_EOL.PHP_EOL.implode(','.PHP_EOL,$migrations).PHP_EOL;

	}
}
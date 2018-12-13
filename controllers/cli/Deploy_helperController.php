<?php

class Deploy_helperController extends MY_Controller
{

	/**
		Generate the Deploy JSON for adding all found GIT Repositories
	 */
	public function indexCliAction()
	{
		$console = new League\CLImate\CLImate;

		$autoload = load_config('autoload','autoload');
		$packages = $autoload['packages'];

		$packages = array_merge([''],$packages);

		foreach ($packages as $package) {
			/* "gitx update {PWD}/packages/projectorangebox/extra-validations {GITBRANCH}" */
			$git_update[] = '"gitx update {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';

			/* "cd {PWD}/public;php index.php cli/migrate/up packages/projectorangebox/scaffolding" 	*/
			$migrations[] = '"cd {PWD}/public;php index.php cli/migrate/up'.str_replace(ROOTPATH,' ',$package).'"';

			/* gitx checkout https://github.com/ProjectOrangeBox/Orange_v2_cli.git {PWD}/packages/projectorangebox/migrations {GITBRANCH} */
			$checkout[] = '"gitx checkout '.$this->get_remote($package).' {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';
		}

		$console->border();

		$console->out(implode(','.PHP_EOL,$git_update));

		$console->border();

		$console->out(implode(','.PHP_EOL,$migrations));

		$console->border();

		$console->out(implode(','.PHP_EOL,$checkout));

		$console->border();
	}

	protected function get_remote($package)
	{
		echo '.';

		$package = (empty($package)) ? ROOTPATH : $package;

		$cli = 'cd '.$package.';git remote show origin';

		exec($cli,$output,$return_var);

		$fetch = trim($output[1]);

		$line = '';

		if (substr($fetch,0,5) == 'Fetch') {
			$line = substr($fetch,11);
		}

		return $line;
	}

}

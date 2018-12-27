<?php

class Deploy_helperController extends MY_Controller
{

	/**
		Generate the Deploy JSON for adding all found GIT Repositories
	 */
	public function indexCliAction()
	{
		$console = new League\CLImate\CLImate;

		$console->Blue()->out('Inspecting Packages');

		$autoload = load_config('autoload','autoload');
		$packages = $autoload['packages'];

		/* this adds root application folder */
		$packages = array_merge([''],$packages);

		$progress = $console->progress()->total(count($packages));

		foreach ($packages as $idx=>$package) {
			$progress->current($idx+1);

			/* "gitx update {PWD}/packages/projectorangebox/extra-validations {GITBRANCH}" */
			$git_update[] = '"gitx update {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';

			/* "cd {PWD}/public;php index.php cli/migrate/up packages/projectorangebox/scaffolding" 	*/
			$migrations[] = '"cd {PWD}/public;php index.php cli/migrate/up'.str_replace(ROOTPATH,' ',$package).'"';

			if ($remote = $this->get_remote($package)) {
				/* gitx checkout https://github.com/ProjectOrangeBox/Orange_v2_cli.git {PWD}/packages/projectorangebox/migrations {GITBRANCH} */
				$checkout[] = '"gitx checkout '.$remote.' {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}"';
			}
		}

		$console->br()->Blue()->out('Deploy commands to update packages - copy and paste as needed.');
		$console->out(implode(','.PHP_EOL,$git_update));

		$console->br()->Blue()->out('Deploy commands to migrate packages up - copy and paste as needed.');
		$console->out(implode(','.PHP_EOL,$migrations));

		$console->br()->Blue()->out('Deploy commands to checkout packages - copy and paste as needed.');
		$console->out(implode(','.PHP_EOL,$checkout));
	}

	protected function get_remote($package)
	{
		$package = (empty($package)) ? ROOTPATH : $package;

		if (file_exists($package.'/.git')) {
			exec('cd '.$package.';git remote show origin',$output,$return_var);

			foreach ($output as $line) {
				$line = trim($line);

				if (substr(strtolower($line),0,11) == 'fetch url: ') {
					return trim(substr($line,11));
				}
			}
		}

		return false;
	}

}

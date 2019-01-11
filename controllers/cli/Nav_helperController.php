<?php

class Nav_helperController extends MY_Controller {

	/**
	 * Generate the Migration PHP for adding all found get http requests
	 */
	public function _remap($method)
	{
		$console = new League\CLImate\CLImate;

		if (!isset($_SERVER['argv'][2])) {
			$console->error('Please enter a dynamic controller url.');
			exit(1);
		}

		$inspection = (new Fruit_inspector)->get_controllers_methods();
		$source_for = str_replace('-','_',trim($_SERVER['argv'][2],'/'));
		$groups = [];
		
		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] == 'get') {
						$action = ($method['action'] == 'index') ? '' : $method['action'];
						$url = str_replace('_','-','/'.strtolower(trim($controller['url'].'/'.$action,'/')));
						$text = trim(ucwords(strtolower(str_replace(['/','_','-'],' ',$url))),' ');
					
						$groups[trim($controller['url'],'/')][] = "ci('o_nav_model')->migration_add('".$url."','".$text."',\$hash);";
					}
				}
			}
		}
		
		if (isset($groups[$source_for])) {
			$console->br()->info('The following source has been generated.')->br();
			
			foreach ($groups[$source_for] as $s) {
				$console->white($s);
			}
			
			$console->br();
		} else {
			$console->br()->error('No dynamic controller urls found for "'.$source_for.'"')->br();
			$console->blue('Current dynamic controllers urls:')->border();
			
			foreach ($groups as $url=>$source) {
				$console->out('/'.$url);
			}

			$console->br();
		}

	} /* end indexCliAction */

} /* end controller */
<?php

class HelpController extends MY_Controller {
	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		require_once __DIR__.'/../../libraries/Console.php';

		$console = new Console;
		$inspection = (new Fruit_inspector)->get_controllers_methods();

		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] == 'cli') {
						$console->line();

						$action = ($method['action'] != 'index') ? '/'.$method['action'] : '';
	
						$console->e('<light_cyan>'.$controller['url'].$action.'</off>');
	
						if (strlen($method['comments'])) {
							$lines = explode(PHP_EOL,trim(substr($method['comments'],3,-2)));
							
							foreach ($lines as $l) {
								$console->e('  '.trim($l).'</off>');
							}
						}

					}
				}
			}
		}
	}
} /* end class */
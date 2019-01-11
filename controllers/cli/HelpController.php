<?php

class HelpController extends MY_Controller {
	/**
	 * Show all of the available Command Line Functions
	 */
	public function indexCliAction() {
		$console = new League\CLImate\CLImate;

		$inspection = (new Fruit_inspector)->get_controllers_methods();

		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] == 'cli' && $method['parent'] != 'MY_Controller') {
						$console->border();

						$action = ($method['action'] != 'index') ? '/'.$method['action'] : '';
	
						$console->info($controller['url'].$action);
	
						if (strlen($method['comments'])) {
							$lines = explode(PHP_EOL,trim(substr($method['comments'],3,-2)));
							
							foreach ($lines as $l) {
								$console->out('  '.trim($l));
							}
						}

					}
				}
			}
		}
	}
} /* end class */
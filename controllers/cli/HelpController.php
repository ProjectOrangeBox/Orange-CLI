<?php

class HelpController extends MY_Controller {

	/**
	 * Show all of the available Command Line Functions
	 */
	public function indexCliAction()
	{
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
	
	/**
	 * Test all database connections (no query's run)
	 */
	public function test_databasesCliAction()
	{
		$console = new League\CLImate\CLImate;

		$padding = $console->padding(16)->char('.');

		$db = load_config('database','db');

		foreach ($db as $name=>$values)
		{
			$console->info($name);
			
			foreach (['dsn','hostname','username','password','database'] as $key) {
				$padding->label($key)->result($values[$key]);
			}
			
			try {
				$this->load->database($name,true);
				$console->tab()->info('Success');
			} catch (Exception $e) {
				$console->tab()->error('Failed');
			}
		}

	}
	
} /* end class */
<?php

class Nav_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found get http requests
	*/
	public function indexCliAction() {
		$console = new League\CLImate\CLImate;
		$inspection = (new Fruit_inspector)->get_controllers_methods();

		$previous_group = '';
		$groups = [];
		
		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] == 'get') {
						$action = ($method['action'] == 'index') ? '' : $method['action'];
						$url = str_replace('_','-','/'.strtolower(trim($controller['url'].'/'.$action,'/')));
					
						$groups[$controller['url']][] = "ci('o_nav_model')->migration_add('".$url."','".$group."',\$hash);";
					}
				}
			}
		}
		
		ksort($groups);

		$input = $console->checkboxes('Select the Controllers you are interested in:',array_keys($groups));
		$response = $input->prompt();

		$console->border();

		foreach ($groups as $key=>$val) {
			if (in_array($key,$response)) {
				$console->out($val);
			}
		}

		$console->border();
	} /* end indexCliAction */

} /* end controller */
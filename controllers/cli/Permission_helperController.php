<?php

class Permission_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found permissions
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
					if ($method['request_method'] != 'cli') {
						$group = filter('human',$controller['url']);

						$key = 'url::'.$controller['url'].'::'.$method['action'].'~'.$method['request_method'];
						$group = filter('human',$controller['url']);
						$description = filter('human',$controller['url'].' '.$method['action'].' '.$method['request_method']);

						$groups[$controller['url']][] = "ci('o_permission_model')->migration_add('".$key."','".$group."','".$description."',\$hash);";
					} /* end if */
				}
			} /* end $package */
		} /* end inspection */

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
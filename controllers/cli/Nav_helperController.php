<?php

class Nav_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found get http requests
	*/
	public function indexCliAction() {
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
					
						$groups[filter('human',$controller['url'])][] = "ci('o_nav_model')->migration_add('".$url."','".$group."',\$hash);";
					}
				}
			}
		}
		
		ksort($groups);

		foreach ($groups as $group=>$record) {
			ci('console')->e('<cyan>'.(++$idx).'</off> '.$group);
		}

		$idx = 0;

		$number = ci('console')->prompt('Select the Controller you are interested in');

		ci('console')->new_line();
		ci('console')->info('The controller has the following Get HTTP Request Methods');
		ci('console')->new_line();

		foreach ($groups as $group=>$records) {
			++$idx;
			
			if ($idx == $number) {
				ci('console')->e('/* '.$group.' */');
				foreach ($records as $r) {
					ci('console')->e($r);
				}
			}
		}

		ci('console')->new_line();

	}
}
<?php

class Permission_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found permissions
	*/
	public function indexCliAction() {
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

						$groups[$group][] = "ci('o_permission_model')->migration_add('".$key."','".$group."','".$description."',\$hash);";
					} /* end if */
				}
			} /* end $package */
		} /* end inspection */

		ksort($groups);

		foreach ($groups as $group=>$record) {
			ci('console')->e('<cyan>'.(++$idx).'</off> '.$group);
		}

		$idx = 0;

		$number = ci('console')->prompt('Select the Controller you are interested in');
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

	} /* end indexCliAction */
} /* end controller */
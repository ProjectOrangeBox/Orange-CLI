<?php

class Permission_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found permissions
	*/
	public function indexCliAction() {
		$inspection = (new Fruit_inspector)->get_controllers_methods();

		$previous_group = '';
		$html = '';

		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] != 'cli') {
						$group = filter('human',$controller['url']);

						if ($group != $previous_group) {
							$html .= chr(10).'/* '.$group.' */'.chr(10);

							$previous_group = $group;
						}

						$key = 'url::'.$controller['url'].'::'.$method['action'].'~'.$method['request_method'];
						$group = filter('human',$controller['url']);
						$description = filter('human',$controller['url'].' '.$method['action'].' '.$method['request_method']);

						$html .= "ci('o_permission_model')->migration_add('".$key."','".$group."','".$description."',\$hash);".chr(10);
					} /* end if */
				}
			} /* end $package */
		} /* end inspection */

		echo trim($html).chr(10);
	} /* end indexCliAction */
} /* end controller */
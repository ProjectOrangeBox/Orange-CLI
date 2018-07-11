<?php

class Nav_helperController extends MY_Controller {

	/**
		Generate the Migration PHP for adding all found get http requests
	*/
	public function indexCliAction() {
		$inspection = (new Fruit_inspector)->get_controllers_methods();

		$previous_group = '';
		$html = '';
		
		foreach ($inspection as $package) {
			foreach ($package as $controller=>$details) {
				$controller = $details['controller'];

				foreach ($details['methods'] as $method) {
					if ($method['request_method'] == 'get') {
						$group = filter('human',$controller['url']);

						if ($group != $previous_group) {
							$html .= chr(10).'/* '.$group.' */'.chr(10);

							$previous_group = $group;
						}

						$action = ($method['action'] == 'index') ? '' : $method['action'];

						$url = str_replace('_','-','/'.strtolower(trim($controller['url'].'/'.$action,'/')));
					
						$html .= "ci('o_nav_model')->migration_add('".$url."','".$group."',__CLASS__);".chr(10);
					}
				}
			}
		}
		
		echo trim($html).chr(10);

	}
}
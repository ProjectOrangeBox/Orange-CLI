<?php

class Permission_helperController extends MY_Controller {

	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		$inspection = (new Fruit_inspector)->get_controllers_methods();

		$previous_group = '';

		foreach ($inspection as $controller) {
			foreach ($controller as $method=>$details) {
				$directory = $details['directory'];
				$class = $details['human_controller'];
				$method = $details['human_method'];
				$request_method = $details['request_method'];
				$group = filter('human',$details['human_package']);
				$key = 'url::/'.strtolower($directory.$class.'::'.$method.'~'.$request_method);
				$description = filter('human',$directory.' '.$request_method.' '.$class.' '.$method);

				if ($group != $previous_group) {
					echo '/* '.$group.' */'.chr(10);
					
					$previous_group = $group;
				}
				
				if ($request_method != 'cli') {
					echo "ci('o_permission_model')->add('".$key."','".$group."','".$description."');".chr(10);
				}
			}
			
			echo chr(10);
		}

	}
}
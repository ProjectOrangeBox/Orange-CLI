<?php

class Permission_helperController extends MY_Controller {

	/**
	 *
	 * Generate the Migration PHP for adding all found permissions
	 *
	 */
	public function _remap($method)
	{
		$console = new League\CLImate\CLImate;
		$source_for = str_replace('-','_',trim($_SERVER['argv'][2],'/'));

		$groups = $this->build_groups();

		if (!isset($_SERVER['argv'][2])) {
			$this->show_avaiable('Please enter a dynamic controller url.',$groups,$console);
		}

		if (isset($groups[$source_for])) {
			$console->br()->info('/* Orange URL Key, Permission Tab, Permission Text, Migration Hash */');

			foreach ($groups[$source_for] as $s) {
				$console->white($s);
			}

			$console->br();
		} else {
			$this->show_avaiable('No dynamic controller urls found at "'.$source_for.'"',$groups,$console);
		}
	}

	protected function build_groups()
	{
		$inspection = (new Fruit_inspector)->get_controllers_methods();
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

						$groups[trim($controller['url'],'/')][] = "ci('o_permission_model')->migration_add('".$key."','".$group."','".$description."',\$this->get_hash());";
					}
				}
			}
		}

		return $groups;
	}

	protected function show_avaiable($text,$groups,$console)
	{
		$console->br()->error($text)->border('-',(int)exec('tput cols'));

		foreach ($groups as $url=>$source) {
			$console->out('/'.$url);
		}

		$console->br();
		exit(1);
	}

} /* end controller */

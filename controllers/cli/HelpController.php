<?php

class HelpController extends MY_Controller {
	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		require_once __DIR__.'/../../libraries/Console.php';

		$console = new Console;
		$controllers = (new Fruit_inspector)->get_controllers_methods();

		foreach ($controllers as $controller=>$methods) {
			foreach ($methods as $method=>$extras) {
				if ($extras['request_method'] == 'cli') {
					$console->line();

					$d = trim($extras['directory'],'/');
					$c = trim($extras['human_controller'],'/');
					$m = trim($extras['human_method'],'/');
					$m = ($m == 'index') ? '' : $m;

					$console->e('<light_cyan>'.str_replace('/','/',trim(strtolower($d.'/'.$c.'/'.$m),'/')).'</off>');

					if (strlen($extras['comments'])) {
						$lines = explode(PHP_EOL,trim(substr($extras['comments'],3,-2)));
						foreach ($lines as $l) {
							$console->e('  '.trim($l).'</off>');
						}
					}
				}
			}
		}
	}

} /* end class */

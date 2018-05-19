<?php

class Orange_cli_library {
	protected $output = [];
	protected $controllers = [];

	public function output($combined=null) {
		return ($combined) ? implode($combined,$this->output) : $this->output;
	}

	public function auto_add_permissions() {
		$controllers = $this->get_controllers_methods();

		foreach ($controllers as $controller=>$methods) {
			foreach ($methods as $method=>$extras) {
				if ($extras['request_method'] != 'cli') {
					$key = 'url::/'.strtolower($extras['directory'].$extras['human_controller'].'::'.$extras['human_method'].'~'.$extras['request_method']);
					$group = filter('human',$extras['human_controller']);
					$description = filter('human',$extras['directory'].' '.$extras['request_method'].' '.$extras['human_controller'].' '.$extras['human_method']);

					if (ci('o_permission_model')->add($key,$group,$description)) {
						/* this was added */
						$this->output[] = '<yellow>'.trim($extras['directory'].' <red>'.$extras['human_controller'].' <yellow>'.$extras['human_method'].' <green>'.$extras['request_method']).'\n';
					}
				}
			}
		}

		return $this;
	}

	public function cli_list() {
		$controllers = $this->get_controllers_methods();

		foreach ($controllers as $controller=>$methods) {
			foreach ($methods as $method=>$extras) {
				if ($extras['request_method'] == 'cli') {
					ci('console')->line();
					
					$d = trim($extras['directory'],'/');
					$c = trim($extras['human_controller'],'/');
					$m = trim($extras['human_method'],'/');
					
					ci('console')->e(str_replace('/','<blue>/</blue>',trim(strtolower($d.'/'.$c.'/'.$m),'/')));
					
					if (strlen($extras['comments'])) {
						$lines = explode(PHP_EOL,trim(substr($extras['comments'],3,-2)));
						$formatted = [];
						
						foreach ($lines as $l) {
							$formatted[] = trim($l);
						}
						
						ci('console')->e('<light_cyan>'.implode($formatted,PHP_EOL).'</light_cyan>');
					}
				}
			}
		}

		return $this;
	}

	/* internal */
	protected function get_controllers_methods($all=false) {
		if ($all) {
			$this->globr(ROOTPATH,'Controller.php');
		} else {
			$autoload = [];

			include ROOTPATH.'/application/config/autoload.php';

			foreach ($autoload['packages'] as $path) {
				$this->globr($path,'Controller.php');
			}
		}

		return $this->controllers;
	}

	protected function globr($searchDirectory,$searchPattern) {
		foreach (glob(escapeshellcmd($searchDirectory).'/*') as $folderitem) {
			if (is_dir($folderitem)) {
				$this->globr($folderitem,$searchPattern);
			} elseif (substr($folderitem,-strlen($searchPattern)) == $searchPattern) {
				$this->add($folderitem);
			}
		}
	}

	protected function add($path) {
		/* is it a Orange / CI Controller? */
		if (strpos(file_get_contents($path),'MY_Controller') === false) {
			/* no */
			return;
		}

		$new_class_file = $this->make_dummy_class_file($path);

		$pos = strpos($path,'/controllers/');
		$path = substr($path,$pos + strlen('/controllers/'));

		$pathinfo = pathinfo($path);

		$directory = ($pathinfo['dirname'] == '.') ? '' : trim($pathinfo['dirname'],'/').'/';
		$original_class_name = substr($pathinfo['filename'],0,-10);

		/* now we can reflect */
		include $new_class_file;

		$new_class_name = basename($new_class_file,'.php');

		$class = new ReflectionClass($new_class_name);
		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $idx=>$ref_method) {
			$method = $raw_method = $ref_method->name;

			if (substr($method,-6) == 'Action') {
				$method = substr($method,0,-6);
				$request_method = 'get';
				$human_method = substr($raw_method,0,-6);

				foreach (['post','put','delete','patch','cli'] as $http_method) {
					if (strtolower(substr($method,-strlen($http_method))) == $http_method) {
						$request_method = $http_method;
						$human_method = substr($raw_method,0,-(strlen($http_method) + 6));
					}
				}

				$this->controllers[$pathinfo['filename']][$method] = [
					'comments'=>trim($class->getMethod($raw_method)->getDocComment()),
					'directory'=>$directory,
					'human_controller'=>$original_class_name,
					'human_method'=>$human_method,
					'request_method'=>$request_method,
					'method'=>$raw_method,
					'controller'=>$original_class_name.'Controller',
				];
			}
		}

		unlink($new_class_file);
	}

	protected function make_dummy_class_file($real_path) {
		$new_class_name = 'Controller_'.md5($real_path);
		$new_class_filepath = CACHEPATH.'/'.$new_class_name.'.php';
		$old_class_name = basename($real_path,'.php');

		$file_content = file_get_contents($real_path);

		if (strpos($file_content,'class '.$old_class_name.' extends') === false) {
			die('Could not locate class name "'.$old_class_name.'" in "'.$real_path.'"'.chr(10));
		}

		file_put_contents($new_class_filepath,str_replace('class '.$old_class_name.' extends','class '.$new_class_name.' extends',$file_content));

		return $new_class_filepath;
	}

}
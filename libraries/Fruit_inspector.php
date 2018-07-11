<?php

class Fruit_inspector {
	protected $controllers = [];
	protected $current_package = '';

	/* internal */
	public function get_controllers_methods($all=false) {
		if ($all) {
			$this->globr(ROOTPATH,'Controller.php');
		} else {
			$autoload = [];

			include ROOTPATH.'/application/config/autoload.php';

			/* add application folder */
			$autoload['packages'][] = ROOTPATH.'/application';

			foreach ($autoload['packages'] as $path) {
				$this->current_package = str_replace(ROOTPATH,'',$path);

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
				$this->controllers[$this->current_package][str_replace([ROOTPATH,$this->current_package],'',$folderitem)] = $this->add($folderitem);
			}
		}
	}

	protected function add($path) {
		/* is it a Orange / CI Controller? */
		if (strpos(file_get_contents($path),'MY_Controller') === false) {
			/* no */
			return;
		}

		$inspection = [];

		$original_class_name = basename($path,'.php');

		/* need to make a dummy class so names don't conflict */
		$new_class_file = $this->make_dummy_class_file($path);
		$new_class_name = basename($new_class_file,'.php');

		/* now we can reflect */
		include $new_class_file;
		
		/* loaded so remove the "fake" file */
		unlink($new_class_file);

		$reflect_class = new ReflectionClass($new_class_name);

		/* go up the tree finding parents */
		$parent_class = $reflect_class;
		$class_parents = [];

		while ($parent = $parent_class->getParentClass()) {
			$inspection['controller']['parents'][] = $parent->getName();

			$parent_class = $parent;
		}

		$inspection['controller']['file'] = str_replace(ROOTPATH,'',$path);
		$inspection['controller']['controller'] = $original_class_name;
		$inspection['controller']['short_controller'] = substr($original_class_name,0,-10);
		$inspection['controller']['package'] = $this->current_package;
		$inspection['controller']['url'] = substr(strtolower(substr($path,strpos($path,'/controllers') + 12)),0,-14);

		$inspection['methods'] = $this->get_methods($reflect_class);
		
		return $inspection;
	}

	protected function get_methods($reflect_class) {
		$inspection = [];
		
		$methods = $reflect_class->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $idx=>$ref_method) {
			$raw_method = $ref_method->name;

			/* is it a Action */
			if (substr($raw_method,-6) == 'Action') {
				$pieces = preg_split('/(?=[A-Z])/',$raw_method);

				$request_method = (count($pieces) == 3) ? strtolower($pieces[1]) : 'get';

				$inspection[$raw_method] = [
					'comments'=>trim($reflect_class->getMethod($raw_method)->getDocComment()),
					'request_method'=>$request_method,
					'method'=>$raw_method,
					'action'=>$pieces[0],
				];
			}
		}
		
		return $inspection;
	}

	protected function get_properties($prop) {
		$p = [];
		
		foreach ((array)$prop as $pp) {
			$p[$pp['name']] = $pp['name'];
		}
	
		return $p;
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
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

			$autoload['packages'][] = ROOTPATH.'/application';

			foreach ($autoload['packages'] as $path) {
				$this->current_package = $path;

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

		/* go up the tree */
		$parent_class = $class;
		$class_parents = [];

		while ($parent = $parent_class->getParentClass()) {
			$class_parents[] = $parent->getName();

			$parent_class = $parent;
		}

		$this->controllers[$pathinfo['filename']]['controller'] = [
			'directory'=>$directory,
			'human_controller'=>$original_class_name,
			'controller'=>$original_class_name.'Controller',
			'parents'=>$class_parents,
			'package'=>$this->current_package,
			'human_package'=>end(explode('/',$this->current_package)),
			'properties'=>[
				'public'=>$class->getProperties(ReflectionProperty::IS_PUBLIC),
				'protected'=>$class->getProperties(ReflectionProperty::IS_PROTECTED),
				'static'=>$class->getProperties(ReflectionProperty::IS_STATIC),
				'private'=>$class->getProperties(ReflectionProperty::IS_PRIVATE),
			],
		];

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
					'package'=>$this->current_package,
					'human_package'=>end(explode('/',$this->current_package)),
					'properties'=>$class->getMethod($raw_method)->getParameters($raw_method),
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
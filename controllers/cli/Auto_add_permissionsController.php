<?php

class Auto_add_permissionsController extends MY_Controller {
	public $libraries = ['console'];

	public function indexCliAction() {
		ci('console')->out('start\n');

		$this->globr(ROOTPATH,'Controller.php');

		ci('console')->out('end\n');
	}

	public function globr($searchDirectory,$searchPattern) {
		foreach (glob(escapeshellcmd($searchDirectory).'/*') as $folderitem) {
			if (is_dir($folderitem)) {
				$this->globr($folderitem,$searchPattern);
			} elseif (substr($folderitem,-strlen($searchPattern)) == $searchPattern) {
				$this->processone($folderitem);
			}
		}
	}

	public function processone($path) {
		/* is it a Orange / CI Controller? */
		if (strpos(file_get_contents($path),'MY_Controller') === false) {
			/* no */
			return;
		}

		ci('console')->out('<cyan>'.$path.'\n');

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
			$method = $ref_method->name;

			if (substr($method,-6) == 'Action') {
				//echo $method.chr(10);

				if (substr($method,-12) == 'DeleteAction') {
					$request_method = 'delete';
					$method = substr($method,0,-12);

					$this->add_access($directory,$original_class_name,$method,$request_method);
				} elseif(substr($method,-11) == 'PatchAction') {
					$request_method = 'patch';
					$method = substr($method,0,-11);

					$this->add_access($directory,$original_class_name,$method,$request_method);
				} elseif(substr($method,-10) == 'PostAction') {
					$request_method = 'post';
					$method = substr($method,0,-10);

					$this->add_access($directory,$original_class_name,$method,$request_method);
				} elseif(substr($method,-9) == 'CliAction') {
					/* don't insert */
				} elseif(substr($method,-6) == 'Action') {
					$request_method = 'get';
					$method = substr($method,0,-6);

					$this->add_access($directory,$original_class_name,$method,$request_method);
				}
			}
		}
	}

	public function add_access($dir,$class,$method,$request_method) {
		$key = 'url::/'.strtolower($dir.$class.'::'.$method.'~'.$request_method);
		$group = filter('human',$class);
		$description = filter('human',$dir.' '.$request_method.' '.$class.' '.$method);

		if (ci('o_permission_model')->add($key,$group,$description)) {
			ci('console')->out('<yellow>'.trim($dir.' <red>'.$class.' <yellow>'.$method.' <green>'.$request_method).'\n');
		}
	}

	public function make_dummy_class_file($real_path) {
		$new_class_name = 'Controller_'.md5($real_path);
		$new_class_filepath = '/tmp/'.$new_class_name.'.php';
		$old_class_name = basename($real_path,'.php');

		$file_content = file_get_contents($real_path);

		if (strpos($file_content,'class '.$old_class_name.' extends') === false) {
			die('Could not locate class name "'.$old_class_name.'" in "'.$real_path.'"'.chr(10));
		}

		file_put_contents($new_class_filepath,str_replace('class '.$old_class_name.' extends','class '.$new_class_name.' extends',$file_content));

		return $new_class_filepath;
	}

} /* end class */

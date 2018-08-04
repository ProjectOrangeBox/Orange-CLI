<?php

class CreateController extends MY_Controller {
	protected $package_folder;

	/**
		Generate a generic package
	*/
	public function packageCliAction($package=null) {
		$console = new League\CLImate\CLImate;

		if (empty($package)) {
			$console->error('Please provide a package name');
		}

		$package_name = filter('filename',$package);

		$package_folder = ROOTPATH.'/packages/';

		if (!is_writable($package_folder)) {
			$console->error($package_folder.' is not writable.');
		}

		$this->package_folder = ROOTPATH.'/packages/'.$package;

		/* make the package folder */
		@mkdir($this->package_folder,0777);

		@mkdir($this->package_folder.'/libraries',0777);
		@mkdir($this->package_folder.'/support',0777);

		$data = [
			'upackage'=>ucfirst($package_name),
			'package'=>$package_name,
		];

		$this->make('controllers/'.$data['upackage'].'Controller.php','controller',$data);
		$this->make('models/'.$data['upackage'].'_model.php','model',$data);
		$this->make('views/'.$data['package'].'/index.php','index',$data);
		$this->make('views/'.$data['package'].'/details.php','details',$data);
	}

	protected function make($name,$template,$data) {
		$template = file_get_contents(__DIR__.'/../../support/templates/'.$template.'.php');

		foreach ($data as $key=>$val) {
			$template = str_replace('{'.$key.'}',$val,$template);
		}

		$path = $this->package_folder.'/'.$name;

		@mkdir(dirname($path),0777,true);
		file_put_contents($path,$template);
		chmod($path,0777);
	}

}
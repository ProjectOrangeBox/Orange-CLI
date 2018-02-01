<?php

class Create_packageController extends MY_Controller {

	public function generateCliAction() {
		$where_in_packages = trim(implode('/',func_get_args()),'/');

		ci('validate')->variable('required',$where_in_packages,'Package Path')->die_on_fail();

		$main_path = ROOTPATH.'/packages/'.$where_in_packages;

		umask(0777);

		@mkdir($main_path.'/controllers',0777,true);
		@mkdir($main_path.'/models',0777,true);
		@mkdir($main_path.'/views',0777,true);
		@mkdir($main_path.'/libraries',0777,true);
	}

} /* end class */

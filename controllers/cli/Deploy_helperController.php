<?php

class Deploy_helperController extends MY_Controller {

	/**
		Generate the Deploy JSON for adding all found GIT Repositories
	*/
	public function indexCliAction() {
		/*
		"gitx update {PWD}/packages/projectorangebox/extra-validations {GITBRANCH}"
		*/

		require APPPATH.'/config/autoload.php';

		foreach ($autoload['packages'] as $package) {
			echo '"gitx update {PWD}'.str_replace(ROOTPATH,'',$package).' {GITBRANCH}",'.chr(10);
		}

	}
}
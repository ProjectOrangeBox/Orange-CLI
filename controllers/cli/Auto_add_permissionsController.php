<?php

class Auto_add_permissionsController extends MY_Controller {
	public $libraries = ['console'];

	public function indexCliAction() {
		ci('console')->out('start\n');

		ci('auto_add_permisssions_library')->run();

		ci('console')->out('end\n');
	}



} /* end class */

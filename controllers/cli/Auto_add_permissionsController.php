<?php

class Auto_add_permissionsController extends MY_Controller {

	/**
		Automatically search for and add any missing url permissions
	*/
	public function indexCliAction() {
		ci('console')->e(ci('orange_cli_library')->auto_add_permissions()->output(PHP_EOL));
	}

} /* end class */

<?php

class ImportController extends MY_Controller {

	public function permissionsCliAction() {
		ci('composer_library')->import()->permissions();
	}

	public function rolesCliAction() {
		ci('composer_library')->import()->roles();
	}

	public function settingsCliAction() {
		ci('composer_library')->import()->settings();
	}

	public function navCliAction() {
		ci('composer_library')->import()->nav();
	}

} /* end class */
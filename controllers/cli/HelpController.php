<?php

class HelpController extends MY_Controller {

	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		ci('orange_cli_library')->cli_list();
	}
}
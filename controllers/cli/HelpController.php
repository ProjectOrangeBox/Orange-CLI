<?php

class HelpController extends MY_Controller {

	/**
		Show all of the available Command Line Functions
	*/
	public function indexCliAction() {
		(new Fruit_inspector)->cli_list();
	}
}

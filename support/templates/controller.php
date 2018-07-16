<?php

class {upackage}Controller extends MY_Controller {
	use admin_controller_trait;

	public $controller					= '{controller_name}';
	public $controller_path			= '{controller_path}';
	public $controller_model		= '{tablename}_model';
	public $controller_title		= '{controller_title}';
	public $controller_titles		= '{controller_titles}';
	public $controller_order_by	= '{controller_order_by}';
	public $controller_limit		= {controller_limit_to};

} /* end class {upackage}Controller */

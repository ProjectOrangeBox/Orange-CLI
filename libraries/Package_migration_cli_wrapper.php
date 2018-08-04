<?php 

class Package_migration_cli_wrapper {
	protected $console;

	/* https://www.codeigniter.com/user_guide/libraries/migration.html */

	public function __construct() {
		$this->console = new League\CLImate\CLImate;
	}

	public function latest() {
		/* Standard CodeIgniter */
		$this->process_mixed(ci('package_migration')->latest(),'latest');
	}
	
	public function version($version) {
		/* Standard CodeIgniter */
		$this->process_mixed(ci('package_migration')->version($version),$version);
	}

	public function find() {
		$found = ci('package_migration')->find_migrations();

		if (count($found)) {
			$highest = ci('package_migration')->get_version();

			//$this->console->sub_heading('Current Version '.$highest);

			foreach ($found as $num=>$value) {
				$text = substr_replace(basename($value),' ',3,1);

				if ((int)$num <= $highest) {
					$this->console->tab()->dim()->out('√ '.$text);
				} else {
					$this->console->tab()->info('> '.$text);
				}
			}
		}
	}

	public function set_path($folder='') {
		ci('package_migration')->set_path($folder);
		
		$this->console->border();
		
		$this->console->out('Path switched to '.str_replace(ROOTPATH,'',$folder));
	
		return $this;
	}

	public function create($description) {
		$filename = ci('package_migration')->create($description);
		
		if ($filename) {
			$this->console->success(str_replace(ROOTPATH,'',$filename).' created.');
		}
	}
	
	protected function process_mixed($mixed,$error_text) {
		if ($mixed === true) {
			$this->console->success('No migrations found.');
		} elseif($mixed === false) {
			$this->console->error(ci('package_migration')->error_string());
		} else {
			$this->console->success('Version changed to '.$mixed.'.');
		}
	}
		
} /* end class */

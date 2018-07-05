<?php 

class Package_migration_cli_wrapper {
	protected $console;

	/* https://www.codeigniter.com/user_guide/libraries/migration.html */

	public function __construct() {
		require_once __DIR__.'/Console.php';

		$this->console = new console;
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

			$this->console->sub_heading('Current Version '.$highest);

			foreach ($found as $num=>$value) {
				$text = str_pad($num,6).basename($value);

				if ((int)$num <= $highest) {
						$this->console->warning($text);
				} else {
						$this->console->info($text);
				}
			}
		}
	}

	public function set_path($folder='') {
		ci('package_migration')->set_path($folder);

		$text = (empty($folder)) ? config('migration.migration_path') : $folder;

		$this->console->info('Path switched to '.strip_rp($text));
	
		return $this;
	}

	public function create($description) {
		$filename = ci('package_migration')->create($description);
		
		if ($filename) {
			$this->console->success(strip_rp($filename).' created.');
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

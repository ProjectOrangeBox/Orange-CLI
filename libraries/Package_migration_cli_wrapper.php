<?php

/*
This provides cli output because the package_migration library doesn't
*/
class Package_migration_cli_wrapper {
	protected $console;
	protected $package = '';
	protected $folder = '';

	/* https://www.codeigniter.com/user_guide/libraries/migration.html */

	public function __construct()
	{
		$this->console = new League\CLImate\CLImate;
	}

	public function latest()
	{
		/* Standard CodeIgniter */
		$this->process_mixed(ci('package_migration')->latest(),'latest');
	}

	public function version($version)
	{
		/* Standard CodeIgniter */
		$this->process_mixed(ci('package_migration')->version($version),$version);
	}

	public function current($version)
	{
		$this->process_mixed(ci('package_migration')->current($version),$version);
	}

	public function find()
	{
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

	public function packages($packages)
	{
		/* look in each folder */
		foreach ($packages as $package) {
			$this->console->out(str_replace(ROOTPATH,'',$package));
		}
	}

	public function set_path($package,$folder)
	{
		$this->package = rtrim($package,'/');
		$this->folder = '/'.trim($folder,'/');

		ci('package_migration')->set_path($this->package.$this->folder);

		$this->console->out('Migration search path switched to '.str_replace(ROOTPATH,'',$this->package).$folder);

		return $this;
	}

	public function get_path()
	{
		return $this->package.$this->folder;
	}

	public function create($description)
	{
		$filename = ci('package_migration')->create($description);

		if ($filename) {
			$this->console->success(str_replace(ROOTPATH,'',$filename).' created.');
		}
	}

	protected function process_mixed($mixed,$error_text)
	{
		if ($mixed === true) {
			$this->console->success('No migrations found.');
		} elseif($mixed === false) {
			$this->console->error(ci('package_migration')->error_string());
		} else {
			$this->console->success('Version changed to '.$mixed.'.');
		}
	}

} /* end class */

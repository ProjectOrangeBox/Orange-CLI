<?php
/**
Migrates up to the current version
php public/index.php cli/migrate/up /packages/example/vendor
php public/index.php cli/migrate/latest /packages/example/vendor

Roll back changes or step forwards programmatically to specific versions
php public/index.php cli/migrate/version /packages/example/vendor 2
php public/index.php cli/migrate/down /packages/example/vendor 2

Find all migrations and show status
php public/index.php cli/migrate/find

Create a new migration
php public/index.php cli/migrate/create /packages/example/vendor "description with spaces"

 */

class MigrateController extends MY_Controller {
	protected $version_arg = 1;
	protected $description_arg = 1;
	protected $folder_arg = 1;
	protected $migration_folder_path = '';
	protected $package_folder_path = '';
	protected $console;
	protected $packages = [];
	protected $args = [];

	public function __construct()
	{
		parent::__construct();

		$this->args = $_SERVER['argv'];

		$this->console = new League\CLImate\CLImate;

		$this->migration_folder_path = '/'.trim(str_replace(ROOTPATH,'',config('migration.migration_path','/support/migrations/')),'/');
		$this->package_folder_path = $this->get_package();

		ci('package_migration_cli_wrapper')->set_path($this->package_folder_path,$this->migration_folder_path);

		$autoload = load_config('autoload','autoload');

		$this->packages = $autoload['packages'];
	}

	public function helpCliAction()
	{
		
		$this->console->br()->out('php public/index.php cli/migrate/help');
		$this->console->tab()->info('Display this help')->br();

		$this->console->out('php public/index.php cli/migrate/up');
		$this->console->out('php public/index.php cli/migrate/latest');
		$this->console->tab()->info('Run all migrations found in Application migration folder.')->br();

		$this->console->out('php public/index.php cli/migrate/up /packages/misc/orange_snippets');
		$this->console->out('php public/index.php cli/migrate/latest /packages/misc/orange_snippets');
		$this->console->tab()->info('Run all migrations found in /packages/misc/orange_snippets migration folder.')->br();

		$this->console->out('php public/index.php cli/migrate/down 3');
		$this->console->tab()->info('Run all migrations down to number 3 in Application migration folder.')->br();

		$this->console->out('php public/index.php cli/migrate/down /packages/misc/orange_snippets 3');
		$this->console->tab()->info('Run all migrations down to number 3 in /packages/misc/orange_snippets migration folder.')->br();

		$this->console->out('php public/index.php cli/migrate/current');
		$this->console->tab()->info('Migrates up to the current version (whatever is set for $config[\'migration_version\'] in application/config/migration.php)..')->br();

		$this->console->out('php public/index.php cli/migrate/current /packages/misc/orange_snippets');
		$this->console->tab()->info('Migrates up to the current version (whatever is set for $config[\'migration_version@/packages/misc/orange_snippets\'] in application/config/migration.php)..')->br();

		$this->console->out('php public/index.php cli/migrate/version 3');
		$this->console->tab()->info('Run all migrations in Application migration folder up or down to 3.')->br();

		$this->console->out('php public/index.php cli/migrate/version /packages/misc/orange_snippets 3');
		$this->console->tab()->info('Run all migrations in /packages/misc/orange_snippets migration folder up or down to 3.')->br();

		$this->console->out('php public/index.php cli/migrate/find');
		$this->console->tab()->info('Display all migration found.')->br();

		$this->console->out('php public/index.php cli/migrate/packages');
		$this->console->tab()->info('Display all packages found (this is great for copying and pasting the package path).')->br();

		$this->console->out('php public/index.php cli/migrate/create "This is the migration"');
		$this->console->tab()->info('Create an empty migration file in the Application migration folder.')->br();

		$this->console->out('php public/index.php cli/migrate/version /packages/misc/orange_snippets "This is the migration"');
		$this->console->tab()->info('Create an empty migration file in the /packages/misc/orange_snippets migration folder.')->br();
	}

	/**
		Wrapper for migrate/current
	 */
	public function upCliAction()
	{
		ci('package_migration_cli_wrapper')->latest();
	}

	/**
		Wrapper for migrate/version/###
	 */
	public function downCliAction()
	{
		ci('package_migration_cli_wrapper')->version((int)$this->get_section($this->version_arg,'version'));
	}

	/* built in functions */

	/**
		This works much the same way as current() but instead of looking for the $config['migration_version']
		the Migration class will use the very newest migration found in the filesystem.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::latest

		TRUE if no migrations are found, current version string on success, FALSE on failure
		 */
	public function latestCliAction()
	{
		ci('package_migration_cli_wrapper')->latest();
	}

	/**
		Migrates up to the current version (whatever is set for $config['migration_version'] in application/config/migration.php).

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::current

		TRUE if no migrations are found, current version string on success, FALSE on failure
		 */
	public function currentCliAction()
	{
		$key = 'migration.migration_version@'.$this->package_folder_path;

		$version = config($key,false);

		if (!$version) {
			show_error('Not current configuration found for "'.$key.'".');
		}

		ci('package_migration_cli_wrapper')->current((int)$version);
	}

	/**
		Version can be used to roll back changes or step forwards programmatically to specific versions.
		It works just like current() but ignores $config['migration_version'].

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::version

		TRUE if no migrations are found, current version string on success, FALSE on failure
	 */
	public function versionCliAction()
	{
		ci('package_migration_cli_wrapper')->version((int)$this->get_section($this->version_arg,'version'));
	}

	/**
		Return an array of migration filenames that are found in the migration_path property.

		https://www.codeigniter.com/user_guide/libraries/migration.html#CI_Migration::find_migrations
	 */
	public function findCliAction()
	{
		ci('package_migration_cli_wrapper')->find();

		/* look in each folder */
		foreach ($this->packages as $package) {
			ci('package_migration_cli_wrapper')->set_path($package,$this->migration_folder_path)->find();
		}
	}

	public function packagesCliAction()
	{
		ci('package_migration_cli_wrapper')->packages($this->packages);
	}

	/**
	Builds a standard migration template
	 */
	public function createCliAction()
	{
		ci('package_migration_cli_wrapper')->create($this->get_section($this->description_arg,'description'));
	}

	protected function get_package()
	{
		$path = '';

		/* did they include anything? */
		$raw_folder = $this->get_section($this->folder_arg,'package folder',false);

		/* is arg1 a folder */
		if (strpos($raw_folder,'/') !== false) {
			/* yes it's a package folder - let's verify it */
			$this->version_arg++;
			$this->description_arg++;

			/* verify it's a valid package */
			$path = '/'.trim($raw_folder,'/');

			if (!file_exists(ROOTPATH.$folder)) {
				show_error('"'.$path.'" does not seem to be a valid package path.');
			}

			if (!file_exists(ROOTPATH.$folder.$this->migration_folder_path)) {
				mkdir(ROOTPATH.$folder.$this->migration_folder_path,0777,true);

				if (!file_exists(ROOTPATH.$folder.$this->migration_folder_path)) {
					show_error('"'.$path.'" does not seem to be a valid package migration path.');
				}
			}
		}

		return $path;
	}

	protected function get_section($num,$text,$required=true)
	{
		/* the first useable arg is 2 */
		$num = $num + 1;

		if ($required) {
			if (trim($this->args[$num]) == '') {
				show_error('Please provide a '.$text.'.');
			}
		}

		return $this->args[$num];
	}

} /* end class */

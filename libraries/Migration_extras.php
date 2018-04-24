<?php

class Migration_extras {

	public function __construct() {
		ci()->load->library('migration');
	}

	public function copy_config($name) {
		$child          = debug_backtrace(null, 1);
		$child_file     = $child[0]['file'];
		$package_folder = dirname(dirname(dirname(substr($child_file, strlen(ROOTPATH)))));

		$name        = (substr($name, -4) == '.php') ? substr($name, 0, -4) : $name;
		$config_file = '/config/' . $name . '.php';
		$success     = true;

		if (file_exists(ROOTPATH . $package_folder . $config_file)) {
			if (is_writable(APPPATH . 'config')) {
				$success = copy(ROOTPATH . $package_folder . $config_file, rtrim(APPPATH, '/') . $config_file);
			} else {
				throw new Exception('Can not write to "' . APPPATH . 'config"');
			}
		} else {
			throw new Exception('Could not locate the specified config file "' . $package_folder . $config_file . '"');
		}

		return $success;
	}

	public function remove_config($name) {
		$child          = debug_backtrace(null, 1);
		$child_file     = $child[0]['file'];
		$package_folder = dirname(dirname(dirname(substr($child_file, strlen(ROOTPATH)))));

		$name        = (substr($name, -4) == '.php') ? substr($name, 0, -4) : $name;
		$config_file = 'config/' . $name . '.php';
		$success     = true;

		if (file_exists(APPPATH . $config_file)) {
			$success = unlink(APPPATH . $config_file);
		}

		return $success;
	}

	public function add_access($data) {
		$defaults = [
			'is_editable'  => 0,
			'is_deletable' => 0,
			'name'         => '',
			'group'        => $this->name,
			/* key is auto filled in */
			'description'  => $data['name'],
			'type'         => 2,
			'internal'     => $this->internal,
		];

		$data = array_diff_key($defaults, $data) + array_intersect_key($data, $defaults);

		/* special insert just for packages */
		return $this->o_access_model->upsert($data);
	}

	public function remove_access($package = null) {
		$internal = ($package) ? $package : $this->internal;

		return $this->o_access_model->delete_by('internal', $internal);
	}

	public function add_setting($data) {
		$defaults = [
			'is_editable'  => 1,
			'is_deletable' => 0,
			'name'         => '',
			'value'        => '',
			'group'        => $this->name,
			'enabled'      => 1,
			'help'         => '',
			'internal'     => $this->internal,
			'managed'      => 1,
			'show_as'      => 0, /* 0 Textarea,1 Boolean T/F,2 Radios (json),3 Text Input (option width) */
			'options' => '', /* Radio {'name': 'value','name2': 'value2'},text width */
		];

		$data = array_diff_key($defaults, $data) + array_intersect_key($data, $defaults);

		return $this->o_setting_model->upsert($data);
	}

	public function remove_setting($package = null) {
		$internal = ($package) ? $package : $this->internal;

		return $this->o_setting_model->delete_by('internal', $internal);
	}

	public function add_route($key, $route) {
		return ci()->router->add($key, $route);
	}

	public function remove_route($key) {
		return ci()->router->remove($key);
	}

	public function add_symlink($asset) {
		$asset = trim($asset, '/');

		$package_folder = $this->_find_package($asset);

		if (!$package_folder) {
			ci()->wallet->msg('Couldn\'t find package folder "' . $this->internal . '/public/' . $asset . '".','red', '/admin/configure/packages');

			return false;
		}

		$public_folder = WWW . '/' . $asset;

		/* let's make the public path if it's not there */
		$drop_folder = dirname($public_folder);

		if (!is_dir($drop_folder)) {
			mkdir($drop_folder, 0777, true);
		}

		/* remove the link/file if it's there */
		$this->remove_symlink($asset);

		if (!$this->relative_symlink($package_folder, $public_folder)) {
			ci()->wallet->msg('Couldn\'t create Link "' . $this->internal . '::' . $asset . '".','red', '/admin/configure/packages');

			return false;
		}

		return true;
	}

	public function remove_symlink($asset) {
		$asset = trim($asset, '/');

		$public_folder = WWW . '/' . $asset;

		return unlink($public_folder);
	}

	/* get the symlinks in the composer.json file and rebuild as needed */
	public function fix_symlinks() {
		$autoload_packages = $this->o_packages_model->active();

		foreach ($autoload_packages as $p) {
			$composer_file = ROOTPATH . '/composer.json';

			if (file_exists($composer_file)) {
				$composer_obj = json_decode(file_get_contents($composer_file));

				if ($composer_obj) {
					if (isset($composer_obj->orange->symlink)) {

						$links = $composer_obj->orange->symlink;

						if (is_array($links)) {
							foreach ($links[0] as $public => $private) {
								$this->relative_symlink($private, $public);
							}
						}
					}
				}
			}
		}
	}

	public function add_rw_folder($name) {
		return (is_writable($this->var_folder)) ? mkdir($this->var_folder . '/' . rtrim($name, '/'), 0777, true) : false;
	}

	public function remove_rw_folder($name) {
		return $this->rmdirr($this->var_folder . '/' . rtrim($name, '/'));
	}

	public function rmdirr($dir) {
		//checks
		if (!is_string($dir) || empty($dir)) {
			return $false;
		}
		if (!is_dir($dir) || is_link($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (!$this->rmdirr($dir . '/' . $file)) {
				chmod($dir . '/' . $file, 0777);

				if (!$this->rmdirr($dir . '/' . $file)) {
					return false;
				}
			};
		}

		/* finally the folder */
		return rmdir($dir);
	}

	public function query($sql, $database_config = 'default') {
		$success = false;

		if (!empty(trim($sql))) {
			$db = ci()->load->database($database_config, true);

			$success = $db->query($sql);

			if (!$success) {
				log_message('error', $db->error());
			}
		}

		list($func) = strtolower(explode(' ', $sql, 1));

		switch ($func) {
		case 'select':
			$success = $success->row_array();
			break;
		case 'insert':
			$success = $db->insert_id();
			break;
		case 'update':
			$success = $db->affected_rows();
			break;
		case 'delete':
			$success = $db->affected_rows();
			break;
		}

		return $success;
	}

	public function drop_table($tablename) {
		return $this->query('DROP TABLE IF EXISTS `' . $tablename . '`');
	}

	public function describe_table($tablename, $database_config = 'default') {
		$db = ci()->load->database($database_config, true);

		$table_exists = $db->table_exists($tablename);

		if ($table_exists) {
			$fields = (array) $db->list_fields($tablename);
		} else {
			/* if the table doesn't exist return a empty array */
			$fields = [];
		}

		return $fields;
	}

	public function db_has_column($column, $tablename, $database_config = 'default') {
		$columns = $this->describe_table($tablename, $database_config);

		return in_array($column, $columns);
	}


	public function find_n_replace($file_path, $find, $replace) {
		$success = false;

		if (file_exists($file_path)) {
			$contents = file_get_contents($file_path);

			$contents = str_replace($find, $replace, $contents);

			$success = file_put_contents($file_path, $contents);
		}

		return $success;
	}

	public function relative_symlink($target, $link) {
		/* remove the link that might be there */

		/* let's make sure the rootpath is NOT there since we add it */
		if (substr($link, 0, strlen(ROOTPATH)) == ROOTPATH) {
			$link = substr($link, strlen(ROOTPATH));
		}

		if (substr($target, 0, strlen(ROOTPATH)) == ROOTPATH) {
			$target = substr($target, strlen(ROOTPATH));
		}

		/* remove it if it's already there */
		@unlink(ROOTPATH . $link);

		/* create it */
		return symlink(ROOTPATH . $target, ROOTPATH . $link);
	}

	public function get_relative_path($from, $to) {
		// some compatibility fixes for Windows paths
		$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
		$to   = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
		$from = str_replace('\\', '/', $from);
		$to   = str_replace('\\', '/', $to);

		$from    = explode('/', $from);
		$to      = explode('/', $to);
		$relPath = $to;

		foreach ($from as $depth => $dir) {
			// find first non-matching dir
			if ($dir === $to[$depth]) {
				// ignore this directory
				array_shift($relPath);
			} else {
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;

				if ($remaining > 1) {
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * -1;
					$relPath   = array_pad($relPath, $padLength, '..');
					break;
				} else {
					$relPath[0] = './' . $relPath[0];
				}
			}
		}

		return implode('/', $relPath);
	}

	public function get_next_sequential() {
		$folder = config('migration.migration_path');

		$files = glob($folder.'*.php');

		$highest = 0;

		foreach ($files as $file) {
			$parts = explode('_',basename($file));

			$highest = (int)ltrim($parts[0],'0') + 1;
		}

		return substr('000'.$highest,-3);
	}

	public function create($name) {
		$name = ($name) ? filter('filename',$name) : 'migration';

		if (config('migration.migration_type') == 'timestamp') {
			$stamp = date('YmdHis');
		} else {
			$stamp = ci()->migration_extras->get_next_sequential();
		}

		$folder = config('migration.migration_path');
		$file = $folder.$stamp.'_'.$name.'.php';
		$template = $this->get_migration_template($name,$stamp);

		if (!is_writable(rtrim($folder,'/'))) {
			die('Can not write to '.rtrim($folder,'/').chr(10));
		}

		file_put_contents($file,$template);

		return 'Created: '.$file;
	}

	public function current() {
		/* TRUE if no migrations are found, current version string on success, FALSE on failure */
		$current = ci()->migration->current();

		if ($current === FALSE) {
 			show_error(ci()->migration->error_string());
 		}

 		return $current;
	}

	public function find() {
		/* An array of migration files */
		return ci()->migration->find_migrations();
	}

	public function latest() {
		/* Current version string on success, FALSE on failure */
		$latest = ci()->migration->latest();

		if ($latest === FALSE) {
 			show_error(ci()->migration->error_string());
 		}

 		return $latest;
	}

	public function version($mixed) {
		/* TRUE if no migrations are found, current version string on success, FALSE on failure */

		/* $target_version (mixed) – Migration version to process */
		$version = ci()->migration->version($mixed);

		if ($version === FALSE) {
 			show_error(ci()->migration->error_string());
 		}

 		return $version;
	}

	public function get_migration_template($name,$stamp) {
		ci()->load->library('parser');

		$data = [
			'name'=>$name,
			'stamp'=>$stamp,
			'ucfirst'=>ucfirst($name),
		];

$template = <<<EOF
<?php

/* {stamp}_{ucfirst} */

class Migration_{ucfirst} extends CI_Migration {

	/* example up function */
	public function up() {
		\$this->dbforge->add_field([
			'blog_id' => [
				'type' => 'INT',
				'constraint' => 5,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			],
			'blog_title' => [
				'type' => 'VARCHAR',
				'constraint' => '100',
			],
			'blog_description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
		]);

		\$this->dbforge->add_key('blog_id', TRUE);

		\$this->dbforge->create_table('blog');
	}

	/* example down function */
	public function down() {
		\$this->dbforge->drop_table('blog');
	}

} /* end migration */
EOF;

		return ci()->parser->parse_string($template,$data,true);
	}

} /* end class */

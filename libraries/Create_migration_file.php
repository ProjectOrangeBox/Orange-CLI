<?php

class Create_migration_file {

	public function create($name=null) {
		$name = ($name) ? filter('filename',$name) : 'migration';
		$stamp = (config('migration.migration_type') == 'timestamp') ? date('YmdHis') : $this->get_next_sequential();
		$folder = config('migration.migration_path');
		$file = $folder.$stamp.'_'.$name.'.php';
		$template = $this->get_migration_template($name,$stamp);

		if (!is_writable(rtrim($folder,'/'))) {
			die('Can not write to '.rtrim($folder,'/').chr(10));
		}

		file_put_contents($file,$template);

		return 'Created: '.$file;
	}
	
	/* protected */
	protected function get_next_sequential() {
		$folder = config('migration.migration_path');
		$files = glob($folder.'*.php');
		$highest = 0;

		foreach ($files as $file) {
			$parts = explode('_',basename($file));

			$highest = (int)ltrim($parts[0],'0') + 1;
		}

		return substr('000'.$highest,-3);
	}

	protected function get_migration_template($name,$stamp) {
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

		return ci('parser')->parse_string($template,$data,true);
	}

} /* end class */

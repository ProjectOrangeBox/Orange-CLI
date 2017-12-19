<?php 

class Orange_tools {
	public function __construct() {
		ci()->load->library('console');
	}

	public function git_status($output_as='none',$table_template=null) {
		/* search all the folder under root for .git/HEAD */		
		exec('find "'.ROOTPATH.'" -name HEAD',$output);

		$array = false;

		foreach ($output as $o) {
			if (strpos($o,'/.git/HEAD') !== false) {
				$stringfromfile = file($o);
				
				$firstLine = $stringfromfile[0]; //get the string from the array
				
				$explodedstring = explode("/", $firstLine, 3); //separate out by the "/" in the string
				
				$branchname = $explodedstring[2]; //get the one that is always the branch name

				$key = str_replace('/.git/HEAD','',$o);

				$sections = explode('/',$key);
				
				$key = end($sections);

				$array[$key] = $branchname;
			}
		}
		
		$table = [];
	
		if ($array) {
			$table[] = ['Package','Branch'];
			
			foreach ($array as $key=>$val) {
				$table[] = [$key,$val];
			}
		
			$responds = '';
			
			switch($output_as) {
				case 'cli':
					foreach ($table as $row) {
						$responds .= str_pad($row[0],32,' ',STR_PAD_RIGHT).$row[1].chr(10);	
					}
				break;
				case 'html':
					ci()->load->library('table');
					
					if ($table_template) {
						ci()->table->set_template($table_template);
					}
					
					$responds .= ci()->table->generate($table);
				break;
				default:
					$responds = $table;
			}
		} else {
			$responds = 'No GIT directory\'s found';
		}

		return $responds;
	}

	public function fix() {
		$this->fix_permissions();
		$this->fix_symlink();
	}

	public function fix_permissions() {
		$composer_obj = $this->get_composer_object(ROOTPATH . '/composer.json');
	
		ci()->console->output('Setting the Default Permissions');
		
		/* files */
		$cli = 'find "'.ROOTPATH.'" -type f | xargs chmod 664';

		@system($cli);

		/* directories */
		$cli = 'find "'.ROOTPATH.'" -type d | xargs chmod 775';

		@system($cli);

		/* change the others back */
		ci()->console->output('Changes Cache folder and stuff');
		
		if (isset($composer_obj->orange->permission)) {
			
			$permissions = $composer_obj->orange->permission;
		
			if (is_array($permissions)) {
				foreach ($permissions[0] as $filename=>$filemode) {
		
					if (substr($filename,0,1) !== '#') {
						ci()->console->output('<yellow>'.$filename.' <cyan>'.$filemode);
					
						/* does this folder exist? */
						if (!file_exists(ROOTPATH.'/'.$filename)) {
							@mkdir(ROOTPATH.'/'.$filename, octdec($filemode), true);
						}
						
						$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH.'/'.$filename));
						
						foreach($iterator as $item) {
					    @chmod($item, octdec($filemode));
						}
					}
				}
			}
		}

	}

	public function fix_symlink() {
		$composer_obj = $this->get_composer_object(ROOTPATH . '/composer.json');

		ci()->console->output('Relink Symbolic Links');

		if (isset($composer_obj->orange->symlink)) {
			
			$links = $composer_obj->orange->symlink;

			if (is_array($links)) {
				foreach ($links[0] as $public => $private) {
					if (substr($public,0,1) !== '#') {
						ci()->console->output('<yellow>'.$private.' <white>>> <cyan>'.$public);
					
						$this->relative_symlink($private, $public);
					}
				}
			}
		}

	}

	public function migration($action='current') {
		ci()->load->library('migration');

		echo $action.chr(10);
		
		switch($action) {
			case 'current':
				$current = ci()->migration->current();
			
				if ($current === FALSE) {
		 			show_error(ci()->migration->error_string());
		 		}
		 		
		 		var_dump($current);
			break;
			case 'find_migrations':
			case 'find':
				$migrations = ci()->migration->find_migrations();
				
				var_dump($migrations);
			break;
			case 'latest':
				$latest = ci()->migration->latest();
			
				if ($latest === FALSE) {
		 			show_error(ci()->migration->error_string());
		 		}
		 		
		 		var_dump($latest);
			break;
			case 'version':
				$version = ci()->migration->version();
			
				if ($version === FALSE) {
		 			show_error(ci()->migration->error_string());
		 		}
		 		
		 		var_dump($version);
			break;
		}
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
	
	protected function get_composer_object($composer_file) {
		ci()->console->output('Using Composer File <yellow>'.$composer_file);

		if (file_exists($composer_file)) {
			$composer_obj = json_decode(file_get_contents($composer_file));

			if ($composer_obj === null) {
				ci()->console->error('composer.json malformed');
				
				exit(1);
			}
		} else {
			ci()->console->error('can not locate composer.json as "'.$composer_file.'"');
			
			exit(1);
		}
		
		return $composer_obj;
	}

} /* end class */

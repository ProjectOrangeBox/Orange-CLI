<?php 

class Composer_library {
	protected $json = [];
	
	public function import() {
		$packages = $this->get_packages();
		
		foreach ($packages as $p) {
			if (file_exists($p.'/composer.json')) {
				$json = json_decode(file_get_contents($p.'/composer.json'));
			
				if ($json !== false) {
					$this->json[str_replace(ROOTPATH.'/','',$p)] = $json;
				} else {
					echo $p.' composer.json'.chr(10);
				}
			}
		}
		
		return $this;
	}
	
	protected function get_packages() {
		require APPPATH.'/config/autoload.php';
	
		return $autoload['packages'];
	}
	
	public function permissions() {
		foreach ($this->json as $package=>$json) {
			echo $package.chr(10);

			if (is_array($json->orange->permissions)) {
				foreach ($json->orange->permissions as $permission) {
					ci('o_permission_model')->add($permission->key,$json->orange->group,$permission->description);
				}
			}
		}

		return $this;
	}

	public function roles() {
		foreach ($this->json as $package=>$json) {
			echo $package.chr(10);

			if (is_array($json->orange->roles)) {
				foreach ($json->orange->roles as $role) {
					ci('o_role_model')->add($role->name,$role->description);
				}
			}
		}

		return $this;
	}

	public function settings() {
		foreach ($this->json as $package=>$json) {
			echo $package.chr(10);

			if (is_array($json->orange->settings)) {
				foreach ($json->orange->settings as $setting) {
					ci('o_setting_model')->add($setting->name,$json->orange->group,$setting->value,$setting->help,$setting->options);
				}
			}
		}

		return $this;
	}

	public function nav() {
		foreach ($this->json as $package=>$json) {
			echo $package.chr(10);
		
			if (is_array($json->orange->nav)) {
				foreach ($json->orange->nav as $nav) {
					ci('o_nav_model')->add($nav->url,$nav->text);
				}
			}
		}
		
		return $this;
	}

} /* end class */
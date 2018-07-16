<?php

class Migration_005_add_setting_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		ci('o_setting_model')->migration_add('Foobar','Example',0,'','',$hash);
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		ci('o_setting_model')->migration_remove($hash);
		
		return true;
	}

} /* end migration */

<?php

class Migration_005_add_setting_example extends Migration_base {

	/* example up function */
	public function up() {
		echo $this->migration('up');

		ci('o_setting_model')->migration_add('Foobar','Example',0,'','',$this->hash());
		
		return true;
	}

	/* example down function */
	public function down() {
		echo $this->migration('down');

		ci('o_setting_model')->migration_remove($this->hash());
		
		return true;
	}

} /* end migration */

<?php

class Migration_004_copy_config_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		$this->_copy_config('support/config/nuts.php');
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		$this->_unlink_config('support/config/nuts.php');
		
		return true;
	}

} /* end migration */

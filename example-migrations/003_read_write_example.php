<?php

class Migration_003_read_write_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		$this->_add_rw_folder('/downloads/cookies');
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		$this->_remove_rw_folder('/downloads/cookies');
		
		return true;
	}

} /* end migration */

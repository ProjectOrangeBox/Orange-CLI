<?php

class Migration_002_symlink_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		$this->_link_public('/assets/example');
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		$this->_unlink_public('/assets/example');
		
		return true;
	}

} /* end migration */

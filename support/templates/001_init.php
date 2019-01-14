<?php

class Migration_001_init extends Migration_base {

	/* example up function */
	public function up() {
		echo $this->migration('up');
		
		return true;
	}

	/* example down function */
	public function down() {
		echo $this->migration('down');
		
		return true;
	}

} /* end migration */

<?php

class Migration_006_add_role_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		ci('o_role_model')->migration_add('Cookie Admin','Cookie Designer and Eater',$hash);
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		ci('o_role_model')->migration_remove($hash);
		
		return true;
	}

} /* end migration */

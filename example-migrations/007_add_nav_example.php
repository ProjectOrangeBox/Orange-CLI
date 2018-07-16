<?php

class Migration_007_add_nav_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		ci('o_nav_model')->migration_add('/scaffolding/columns','Columns',$hash);
		ci('o_nav_model')->migration_add('/scaffolding/regenerate','Regenerate',$hash);
		ci('o_nav_model')->migration_add('/scaffolding/tables','Tables',$hash);
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		ci('o_nav_model')->migration_remove($hash);
		
		return true;
	}

} /* end migration */

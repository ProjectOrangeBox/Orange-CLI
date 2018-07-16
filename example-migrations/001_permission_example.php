<?php

class Migration_001_permission_example extends Migration_base {

	/* example up function */
	public function up() {
		$hash = $this->get_hash();

		echo $hash.' up'.chr(10);

		ci('o_permission_model')->migration_add('url::/cli/scaffolding::generate~cli','Scaffolding','Cli Cli Scaffolding Generate',$hash);
		ci('o_permission_model')->migration_add('url::/cli/scaffolding::create_columns~cli','Scaffolding','Cli Cli Scaffolding Create Columns');
		ci('o_permission_model')->migration_add('url::/cli/scaffolding::create_missing_columns~cli','Scaffolding','Cli Cli Scaffolding Create Missing Columns',$hash);
		ci('o_permission_model')->migration_add('url::/cli/scaffolding::create_files~cli','Scaffolding','Cli Cli Scaffolding Create Files',$hash);
		ci('o_permission_model')->migration_add('url::/cli/scaffolding::create_missing_files~cli','Scaffolding','Cli Cli Scaffolding Create Missing Files',$hash);
		ci('o_permission_model')->migration_add('url::/cli/scaffolding::display_permissions~cli','Scaffolding','Cli Cli Scaffolding Display Permissions',$hash);
		
		ci('o_permission_model')->migration_add('url::/scaffolding/columns::index~get','Scaffolding','Scaffolding Get Columns Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/columns::details~get','Scaffolding','Scaffolding Get Columns Details',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/columns::index~post','Scaffolding','Scaffolding Post Columns Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/columns::index~patch','Scaffolding','Scaffolding Patch Columns Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/columns::index~delete','Scaffolding','Scaffolding Delete Columns Index',$hash);
		
		ci('o_permission_model')->migration_add('url::/scaffolding/regenerate::button_regenerate_all_files~get','Scaffolding','Scaffolding Get Regenerate Button Regenerate All Files',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/regenerate::button_regenerate_missing_files~get','Scaffolding','Scaffolding Get Regenerate Button Regenerate Missing Files',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/regenerate::button_regenerate_all_columns~get','Scaffolding','Scaffolding Get Regenerate Button Regenerate All Columns',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/regenerate::button_regenerate_missing_columns~get','Scaffolding','Scaffolding Get Regenerate Button Regenerate Missing Columns',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/regenerate::index~get','Scaffolding','Scaffolding Get Regenerate Index');
		
		ci('o_permission_model')->migration_add('url::/scaffolding/tables::index~get','Scaffolding','Scaffolding Get Tables Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/tables::details~get','Scaffolding','Scaffolding Get Tables Details',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/tables::index~post','Scaffolding','Scaffolding Post Tables Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/tables::index~patch','Scaffolding','Scaffolding Patch Tables Index',$hash);
		ci('o_permission_model')->migration_add('url::/scaffolding/tables::index~delete','Scaffolding','Scaffolding Delete Tables Index',$hash);
		
		return true;
	}

	/* example down function */
	public function down() {
		$hash = $this->get_hash();

		echo $hash.' down'.chr(10);

		ci('o_permission_model')->migration_remove($hash);
		
		return true;
	}

} /* end migration */

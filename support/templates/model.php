<?php

class {upackage}_model extends Database_model {
	protected $table = '{table}';
	protected $primary_key = '{model_primary_key}';
	protected $auto_generated_primary = {model_auto_generated_primary};
	protected $has_soft_delete = {model_has_soft_delete};
	protected $rules = [
		{rules}
	];
	protected $rule_sets = [
		'insert'=>'{model_insert_columns}',
	];

} /* end class {upackage}_model */

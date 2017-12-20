<yellow>git <off>Shows the current git branch for all found git folders

<yellow>current <off>Migrates up to the current version (whatever is set for $config['migration_version'] in application/config/migration.php).

<yellow>find <off>An list of migration filenames are returned that are found in the migration_path property.

<yellow>latest <off>This works much the same way as current() but instead of looking for the $config['migration_version'] the Migration class will use the very newest migration found in the filesystem.

<yellow>version <off>Can be used to roll back changes or step forwards programmatically to specific versions.

<yellow>create/description <off>Creates a migration with the description given

<yellow>composer <off>Runs composer update

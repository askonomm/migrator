# Migrator

A simple, extendable database migration tool with out of box support for MySQL.

## Installation

```bash
composer require asko/migrator
```

## Usage

Your migration files should be placed in a directory. Each migration file is just a regular SQL file with a `{up|down}.sql` extension. The migrations are run in file name order, so you should prefix the files numerically or with a timestamp, e.g `001_create_users_table.up.sql`.

```php
$migrator = new Migrator('migrations_path', new MysqlDriver(...));

// Run migrations
$migrator->migrate();

// Revert last migration
$migrator->revert();
```

<?php

declare(strict_types=1);

use Demezio\Finbase\Finance\Infrastructure\Database\PdoConnectionFactory;

require dirname(__DIR__).'/vendor/autoload.php';

/** @var array{driver: string, host: string, port: int, database: string, username: string, password: string} $config */
$config = require dirname(__DIR__).'/config/database.php';
$connection = PdoConnectionFactory::create($config);

$connection->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        migration VARCHAR(255) NOT NULL PRIMARY KEY,
        executed_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
);

$executed = $connection->query('SELECT migration FROM migrations')->fetchAll(\PDO::FETCH_COLUMN);
$migrations = glob(__DIR__.'/migrations/*.php') ?: [];
sort($migrations, SORT_STRING);

foreach ($migrations as $path) {
    $name = basename($path);

    if (in_array($name, $executed, true)) {
        continue;
    }

    /** @var callable(\PDO): void $migration */
    $migration = require $path;

    $migration($connection);
    $statement = $connection->prepare('INSERT INTO migrations (migration, executed_at) VALUES (:migration, NOW())');
    $statement->execute(['migration' => $name]);
    fwrite(STDOUT, sprintf("Applied %s\n", $name));
}

<?php

declare(strict_types=1);

return static function (\PDO $connection): void {
    $connection->exec(
        'CREATE TABLE IF NOT EXISTS accounts (
            id CHAR(36) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            balance_amount BIGINT NOT NULL,
            currency CHAR(3) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    );
};

<?php

declare(strict_types=1);

return static function (\PDO $connection): void {
    $connection->exec(
        'CREATE TABLE IF NOT EXISTS transactions (
            id CHAR(36) NOT NULL PRIMARY KEY,
            account_id CHAR(36) NOT NULL,
            type VARCHAR(10) NOT NULL,
            amount BIGINT NOT NULL,
            currency CHAR(3) NOT NULL,
            description VARCHAR(255) NOT NULL,
            occurred_at DATETIME NOT NULL,
            CONSTRAINT fk_transactions_account
                FOREIGN KEY (account_id)
                REFERENCES accounts(id),
            INDEX idx_transactions_account_id (account_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    );
};

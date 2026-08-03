<?php

declare(strict_types=1);

$environment = [];
$environmentFile = dirname(__DIR__).'/.env';

if (file_exists($environmentFile)) {
    foreach (file($environmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $environment[trim($name)] = trim($value);
    }
}

$readEnvironment = static function (string $name, ?string $default = null) use ($environment): ?string {
    $value = getenv($name);

    if ($value !== false) {
        return $value;
    }

    return $environment[$name] ?? $default;
};

return [
    'persistence_driver' => $readEnvironment('PERSISTENCE_DRIVER', 'json'),
    'driver' => $readEnvironment('DB_DRIVER', 'mysql'),
    'host' => $readEnvironment('DB_HOST', '127.0.0.1'),
    'port' => (int) $readEnvironment('DB_PORT', '3306'),
    'database' => $readEnvironment('DB_DATABASE', 'finbase'),
    'username' => $readEnvironment('DB_USERNAME', 'finbase'),
    'password' => $readEnvironment('DB_PASSWORD', ''),
];

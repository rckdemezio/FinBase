<?php

declare(strict_types=1);

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> · FinBase</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/currency-input.js" defer></script>
</head>
<body>
    <main class="container">
        <?= $content ?>
    </main>
</body>
</html>

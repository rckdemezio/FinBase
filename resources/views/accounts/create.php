<?php

declare(strict_types=1);

/** @var list<string> $errors */
/** @var array<string, mixed> $old */

?>
<h1>Nova conta</h1>

<?php if ($errors !== []): ?>
    <section class="errors" aria-label="Erros de validação">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<form method="post" action="/accounts">
    <label>
        Nome
        <input name="name" required value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <label>
        Moeda
        <input name="currency" required value="<?= htmlspecialchars((string) ($old['currency'] ?? 'BRL'), ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <button type="submit">Criar conta</button>
</form>

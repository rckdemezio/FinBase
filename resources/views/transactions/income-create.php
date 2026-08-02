<?php

declare(strict_types=1);

use Demezio\Finbase\Finance\Domain\Entity\Account;

/** @var Account $account */
/** @var list<string> $errors */
/** @var array<string, mixed> $old */

$id = $account->id()->value();
?>
<a href="/accounts/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">← Voltar para a conta</a>

<h1>Nova renda</h1>
<p><?= htmlspecialchars($account->name(), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($account->balance()->currencyCode(), ENT_QUOTES, 'UTF-8') ?></p>

<?php if ($errors !== []): ?>
    <section class="errors" aria-label="Erros de validação">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<form method="post" action="/accounts/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>/income">
    <label>
        Valor (<?= htmlspecialchars($account->balance()->currencyCode(), ENT_QUOTES, 'UTF-8') ?>)
        <input type="hidden" name="amount" value="<?= htmlspecialchars((string) ($old['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input
            type="text"
            inputmode="numeric"
            autocomplete="off"
            data-money-input
            aria-label="Valor em <?= htmlspecialchars($account->balance()->currencyCode(), ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <small>Use vírgula para os centavos.</small>
    </label>

    <label>
        Descrição
        <input name="description" required value="<?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <label>
        Data
        <input type="datetime-local" name="occurredAt" required value="<?= htmlspecialchars((string) ($old['occurredAt'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <button type="submit">Registrar renda</button>
</form>

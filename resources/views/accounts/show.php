<?php

declare(strict_types=1);

use Demezio\Finbase\Finance\Domain\Entity\Account;

/** @var Account $account */

?>
<a href="/accounts">← Voltar para contas</a>

<h1><?= htmlspecialchars($account->name(), ENT_QUOTES, 'UTF-8') ?></h1>

<dl class="account-details">
    <div>
        <dt>Identificador</dt>
        <dd><?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?></dd>
    </div>
    <div>
        <dt>Saldo atual</dt>
        <dd>
            <?= htmlspecialchars($account->balance()->format(), ENT_QUOTES, 'UTF-8') ?>
        </dd>
    </div>
</dl>

<nav>
    <a href="/accounts/<?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?>/income/create">Registrar renda</a>
    <a href="/accounts/<?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?>/expenses/create">Registrar despesa</a>
    <a href="/accounts/<?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?>/transactions">Ver transações</a>
    <a href="/accounts/<?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?>/summary?year=<?= date('Y') ?>&amp;month=<?= date('n') ?>">Ver resumo mensal</a>
</nav>

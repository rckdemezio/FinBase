<?php

declare(strict_types=1);

/** @var array{id: string, name: string, balance: array{amount: int, currency: string}} $account */

?>
<a href="/accounts">← Voltar para contas</a>

<h1><?= htmlspecialchars($account['name'], ENT_QUOTES, 'UTF-8') ?></h1>

<dl class="account-details">
    <div>
        <dt>Identificador</dt>
        <dd><?= htmlspecialchars($account['id'], ENT_QUOTES, 'UTF-8') ?></dd>
    </div>
    <div>
        <dt>Saldo atual</dt>
        <dd>
            <?= $account['balance']['amount'] ?>
            <?= htmlspecialchars($account['balance']['currency'], ENT_QUOTES, 'UTF-8') ?>
        </dd>
    </div>
</dl>

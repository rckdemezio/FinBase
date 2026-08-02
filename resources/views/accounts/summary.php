<?php declare(strict_types=1); ?>
<a href="/accounts/<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">← Voltar para conta</a>
<h1><?= htmlspecialchars($summary['period'], ENT_QUOTES, 'UTF-8') ?></h1>
<dl class="account-details"><div><dt>Entradas</dt><dd><?= $summary['income']['amount'] ?> <?= htmlspecialchars($summary['income']['currency'], ENT_QUOTES, 'UTF-8') ?></dd></div><div><dt>Despesas</dt><dd><?= $summary['expenses']['amount'] ?> <?= htmlspecialchars($summary['expenses']['currency'], ENT_QUOTES, 'UTF-8') ?></dd></div><div><dt>Resultado</dt><dd><?= $summary['result']['amount'] ?> <?= htmlspecialchars($summary['result']['currency'], ENT_QUOTES, 'UTF-8') ?></dd></div><div><dt>Transações</dt><dd><?= $summary['transaction_count'] ?></dd></div></dl>
<h2>Movimentações do período</h2>
<?php foreach ($transactions as $transaction): ?><p><?= htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8') ?: 'Sem descrição' ?> — <?= $transaction['amount']['value'] ?> <?= htmlspecialchars($transaction['amount']['currency'], ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>

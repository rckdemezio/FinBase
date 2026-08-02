<?php declare(strict_types=1); ?>
<a href="/accounts/<?= htmlspecialchars($accountId, ENT_QUOTES, 'UTF-8') ?>">← Voltar para conta</a>
<h1>Transações</h1>
<?php if ($transactions === []): ?><p>Nenhuma transação registrada.</p><?php else: ?><section class="accounts"><?php foreach ($transactions as $transaction): ?><article class="account"><strong><?= htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8') ?: 'Sem descrição' ?></strong><span><?= htmlspecialchars($transaction['type'], ENT_QUOTES, 'UTF-8') ?> · <?= $transaction['amount']['value'] ?> <?= htmlspecialchars($transaction['amount']['currency'], ENT_QUOTES, 'UTF-8') ?></span></article><?php endforeach; ?></section><?php endif; ?>

<?php

declare(strict_types=1);

/** @var list<Demezio\Finbase\Finance\Domain\Entity\Account> $accounts */

?>
<h1>Contas</h1>

<?php if ($accounts === []): ?>
    <p>Nenhuma conta cadastrada.</p>
<?php else: ?>
    <section class="accounts">
        <?php foreach ($accounts as $account): ?>
            <article class="account">
                <strong>
                    <a href="/accounts/<?= htmlspecialchars($account->id()->value(), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($account->name(), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </strong>
                <span>
                    <?= htmlspecialchars($account->balance()->format(), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonAccountRepository;
use Demezio\Finbase\Tests\Contract\AccountRepositoryContractTestCase;

final class JsonAccountRepositoryTest extends AccountRepositoryContractTestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = sys_get_temp_dir().'/finbase-'.bin2hex(random_bytes(8)).'.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    protected function createRepository(): AccountRepository
    {
        return new JsonAccountRepository($this->filePath);
    }

    public function testItCreatesTheFileAndUsesAStableDataRepresentation(): void
    {
        $account = Account::restore(
            AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'),
            'Conta Principal',
            new Money(150000, 'BRL'),
        );

        $this->createRepository()->save($account);

        self::assertFileExists($this->filePath);
        self::assertSame(
            [[
                'id' => $account->id()->value(),
                'name' => 'Conta Principal',
                'balance' => ['amount' => 150000, 'currency' => 'BRL'],
            ]],
            json_decode((string) file_get_contents($this->filePath), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testItPersistsAccountsBetweenRepositoryInstances(): void
    {
        $account = Account::restore(
            AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'),
            'Conta Principal',
            new Money(150000, 'BRL'),
        );

        $this->createRepository()->save($account);
        $found = $this->createRepository()->findById($account->id());

        self::assertNotNull($found);
        self::assertSame('Conta Principal', $found->name());
        self::assertTrue($found->balance()->equals(new Money(150000, 'BRL')));
    }

    public function testItThrowsAPredictableExceptionForAnEmptyOrInvalidFile(): void
    {
        file_put_contents($this->filePath, '');

        $repository = $this->createRepository();

        $this->expectException(PersistenceException::class);
        $repository->all();
    }
}

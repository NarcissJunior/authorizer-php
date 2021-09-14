<?php

namespace Tests\unit;

use Authorizer\repositories\AccountRepositoryInMemory;
use Authorizer\repositories\TransactionRepositoryInMemory;
use Authorizer\services\AccountService;
use Authorizer\services\TransactionService;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private $transactionRules;

    public function setUp(): void
    {
        parent::setUp();

        $this->transactionRules = [
            \Authorizer\services\transaction_rules\DoubleTransactionRule::class,
            \Authorizer\services\transaction_rules\HighFrequencySmallIntervalRule::class
        ];
    }
    public function test_should_process_transaction_and_return_account_updated(): void
    {
        // Arrange
        $accountRepository = new AccountRepositoryInMemory();
        $accountService = new AccountService($accountRepository);
        $transactionRepository = new TransactionRepositoryInMemory();
        $service = new TransactionService($accountRepository, $transactionRepository, $this->transactionRules);

        $accountParams = ["active-card" => true, "available-limit" => 100];
        $accountService->createAccount($accountParams);

        $expected = ["account" => ["active-card" => true, "available-limit" => 80], "violations" => []];
        $transactionParams = ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:00.000Z"];

        // Act
        $actual = $service->processTransaction($transactionParams);

        // Assert
        self::assertEquals($expected, $actual);
    }
}
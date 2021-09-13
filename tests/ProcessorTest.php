<?php

namespace Tests;

use Authorizer\entities\Account;
use Authorizer\Processor;
use Authorizer\repositories\AccountRepositoryInMemory;
use Authorizer\repositories\TransactionRepositoryInMemory;
use Authorizer\services\AccountService;
use Authorizer\services\TransactionService;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    private AccountService $accountService;
    private AccountRepositoryInMemory $accountRepository;
    private TransactionService $transactionService;
    private Processor $processor;

    public function setUp(): void
    {
        parent::setUp();

        $transactionRules = [
            \Authorizer\services\transaction_rules\DoubleTransactionRule::class,
            \Authorizer\services\transaction_rules\HighFrequencySmallIntervalRule::class
        ];

        $this->accountRepository = new AccountRepositoryInMemory();
        $this->accountService = new AccountService($this->accountRepository);
        $transactionRepository = new TransactionRepositoryInMemory();
        $this->transactionService = new TransactionService($this->accountRepository, $transactionRepository, $transactionRules);
        $this->processor = new Processor($this->accountService,  $this->transactionService);
    }

    public function test_application_receive_and_return_an_account(): void
    {
        // Arrange
        $input = '{"account": {"active-card": true, "available-limit": 100}}';
        $this->processor = new Processor($this->accountService,  $this->transactionService);
        $expected = ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []];

        // Act
        $actual = $this->processor->process($input);

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function test_application_should_process_a_transaction()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 100}}',
                  '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T10:00:00.000Z"}}',
                  '{"transaction": {"merchant": "Habbibs", "amount": 40, "time": "2019-02-13T11:00:00.000Z"}}',
                  '{"transaction": {"merchant": "McDonalds", "amount": 30, "time": "2019-02-13T12:00:00.000Z"}}'
        ];
        $this->processor = new Processor($this->accountService,  $this->transactionService);

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 80], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 40], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 10], "violations" => []]
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
        // Assert

    }
}


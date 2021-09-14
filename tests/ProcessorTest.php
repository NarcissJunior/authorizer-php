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

    public function test_application_should_receive_and_return_an_account(): void
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
    }

    public function test_application_should_return_account_not_initialized_error()
    {
        // Arrange
        $input = ['{"transaction": {"merchant": "Uber Eats", "amount": 25, "time": "2020-12-01T11:07:00.000Z"}}',
                   '{"account": {"active-card": true, "available-limit": 225}}',
                   '{"transaction": {"merchant": "Uber Eats", "amount": 25, "time": "2020-12-01T11:07:00.000Z"}}'
        ];

        $results = [
            ["account" => [], "violations" => ["account-not-initialized"]],
            ["account" => ["active-card" => true, "available-limit" => 225], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 200], "violations" => []],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_return_account_already_initialized_error()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 175}}',
                  '{"account": {"active-card": true, "available-limit": 350}}'
        ];

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 175], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 175], "violations" => ["account-already-initialized"]],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_return_card_not_active_error()
    {
        // Arrange
        $input = ['{"account": {"active-card": false, "available-limit": 100}}',
                  '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T11:00:00.000Z"}}',
                  '{"transaction": {"merchant": "Habbibs", "amount": 15, "time": "2019-02-13T11:15:00.000Z"}}'
        ];

        $results = [
            ["account" => ["active-card" => false, "available-limit" => 100], "violations" => []],
            ["account" => ["active-card" => false, "available-limit" => 100], "violations" => ["card-not-active"]],
            ["account" => ["active-card" => false, "available-limit" => 100], "violations" => ["card-not-active"]],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_return_insufficient_limit_error()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 1000}}',
                    '{"transaction": {"merchant": "Vivara", "amount": 1250, "time": "2019-02-13T11:00:00.000Z"}}',
                    '{"transaction": {"merchant": "Samsung", "amount": 2500, "time": "2019-02-13T11:00:01.000Z"}}',
                    '{"transaction": {"merchant": "Nike", "amount": 800, "time": "2019-02-13T11:01:01.000Z"}}'
        ];

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 1000], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 1000], "violations" => ["insufficient-limit"]],
            ["account" => ["active-card" => true, "available-limit" => 1000], "violations" => ["insufficient-limit"]],
            ["account" => ["active-card" => true, "available-limit" => 200], "violations" => []],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_return_double_transaction_error()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 100}}',
                    '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T11:00:00.000Z"}}',
                    '{"transaction": {"merchant": "McDonalds", "amount": 10, "time": "2019-02-13T11:00:01.000Z"}}',
                    '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T11:00:02.000Z"}}',
                    '{"transaction": {"merchant": "Burger King", "amount": 15, "time": "2019-02-13T11:00:03.000Z"}}'
        ];

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 80], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 70], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 70], "violations" => ["double-transaction"]],
            ["account" => ["active-card" => true, "available-limit" => 70], "violations" => ["high-frequency-small-interval"]],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_return_high_frequency_small_interval_error()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 100}}',
            '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T11:00:00.000Z"}}',
            '{"transaction": {"merchant": "Habbibs", "amount": 20, "time": "2019-02-13T11:00:01.000Z"}}',
            '{"transaction": {"merchant": "McDonalds", "amount": 20, "time": "2019-02-13T11:01:01.000Z"}}',
            '{"transaction": {"merchant": "Subway", "amount": 20, "time": "2019-02-13T11:01:31.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 10, "time": "2019-02-13T12:00:00.000Z"}}'
        ];

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 80], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 60], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 40], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 40], "violations" => ["high-frequency-small-interval"]],
            ["account" => ["active-card" => true, "available-limit" => 30], "violations" => []],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }

    public function test_application_should_process_multiple_logics()
    {
        // Arrange
        $input = ['{"account": {"active-card": true, "available-limit": 100}}',
            '{"transaction": {"merchant": "McDonalds", "amount": 10, "time": "2019-02-13T11:00:01.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 20, "time": "2019-02-13T11:00:02.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 5, "time": "2019-02-13T11:00:07.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 5, "time": "2019-02-13T11:00:08.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 150, "time": "2019-02-13T11:00:18.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 190, "time": "2019-02-13T11:00:22.000Z"}}',
            '{"transaction": {"merchant": "Burger King", "amount": 15, "time": "2019-02-13T12:00:27.000Z"}}',
        ];

        $results = [
            ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 90], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 70], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 65], "violations" => []],
            ["account" => ["active-card" => true, "available-limit" => 65], "violations" => ["double-transaction", "high-frequency-small-interval"]],
            ["account" => ["active-card" => true, "available-limit" => 65], "violations" => ["insufficient-limit","high-frequency-small-interval"]],
            ["account" => ["active-card" => true, "available-limit" => 65], "violations" => ["insufficient-limit","high-frequency-small-interval"]],
            ["account" => ["active-card" => true, "available-limit" => 50], "violations" => []],
        ];

        // Act
        for ($i = 0; $i < count($input); $i++) {
            $actual = $this->processor->process($input[$i]);
            self::assertEquals($results[$i], $actual);
        }
    }
}


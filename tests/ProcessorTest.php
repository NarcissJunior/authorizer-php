<?php

namespace Tests;

use Authorizer\Processor;
use Authorizer\repositories\AccountRepositoryInMemory;
use Authorizer\repositories\TransactionRepositoryInMemory;
use Authorizer\services\AccountService;
use Authorizer\services\TransactionService;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    public function test_operation_returns_account()
    {
        // Arrange
        $transactionRules = [
            \Authorizer\services\transaction_rules\DoubleTransactionRule::class,
            \Authorizer\services\transaction_rules\HighFrequencySmallIntervalRule::class
        ];
        $accountRepository = new AccountRepositoryInMemory();
        $accountService = new AccountService($accountRepository);
        $transactionRepository = new TransactionRepositoryInMemory();
        $transactionService = new TransactionService($accountRepository, $transactionRepository, $transactionRules);
        $processor = new Processor($accountService, $transactionService);

        $actual = "";
        $expected = "account";

        // Act
        $accountOperation = $processor->process("account");

        // Assert
        self::assertEquals($expected, $accountOperation);
    }
}